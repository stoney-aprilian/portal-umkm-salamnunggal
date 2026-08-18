<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreMediaRequest;
use App\Models\Media;
use App\Models\Product;
use App\Models\Umkm;
use App\Support\MediaManagementActivity;
use App\Support\ProductManagementActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Administrator media management for UMKM (logo, banner, gallery) and
 * products (single photo) as part of the assisted service. Validation
 * reuses the owner StoreMediaRequest verbatim (same file types, sizes,
 * and gallery limit) and media records keep pointing at the owning entity
 * (never the administrator). Unlike the owner flow, the administrator may
 * manage media of any status, including approved.
 */
class MediaController extends Controller
{
    private const UMKM_COLLECTIONS = ['logo', 'banner', 'gallery'];

    private const PRODUCT_COLLECTIONS = ['product'];

    public function store(StoreMediaRequest $request, Umkm $umkm, string $collection): RedirectResponse
    {
        abort_unless(in_array($collection, self::UMKM_COLLECTIONS, true), 404);

        $this->authorize('update', $umkm);

        $replacing = $umkm->media()->where('collection', $collection)->exists();

        if ($collection === 'gallery') {
            $this->storeGallery($umkm, $request->file('gallery', []));
        } else {
            $this->replaceSingle($umkm, $collection, $request->file($request->inputName()));
        }

        MediaManagementActivity::log(
            $replacing ? 'media_replaced' : 'media_uploaded',
            $umkm,
            $request->user(),
            $collection
        );

        return redirect()->back()
            ->with('status', 'Media berhasil diunggah.');
    }

    public function storeProduct(StoreMediaRequest $request, Product $product, string $collection): RedirectResponse
    {
        abort_unless(in_array($collection, self::PRODUCT_COLLECTIONS, true), 404);

        $this->authorize('update', $product);

        $replacing = $product->media()->where('collection', $collection)->exists();

        $this->replaceSingle($product, $collection, $request->file($request->inputName()));

        ProductManagementActivity::log(
            $replacing ? 'product_media_replaced' : 'product_media_uploaded',
            $product,
            $request->user()
        );

        return redirect()->back()
            ->with('status', 'Foto produk berhasil diunggah.');
    }

    public function destroy(Request $request, Media $media): RedirectResponse
    {
        abort_unless($media->mediable instanceof Umkm || $media->mediable instanceof Product, 404);

        $this->authorize('delete', $media);

        $mediable = $media->mediable;
        $collection = $media->collection;
        $disk = $media->disk;
        $path = $media->path;

        $media->delete();
        Storage::disk($disk)->delete($path);

        if ($mediable instanceof Product) {
            ProductManagementActivity::log('product_media_deleted', $mediable, $request->user());
        } else {
            MediaManagementActivity::log('media_deleted', $mediable, $request->user(), $collection);
        }

        return redirect()->back()
            ->with('status', 'Media berhasil dihapus.');
    }

    /**
     * Stores the uploaded file first, then swaps the media record inside
     * a transaction. A failed upload never touches the existing media.
     * Mirrors the owner media controller behavior.
     */
    private function replaceSingle(Umkm|Product $target, string $collection, UploadedFile $file): void
    {
        $path = $file->store($this->directoryFor($target), 'public');

        try {
            DB::transaction(function () use ($target, $collection, $path) {
                $old = $target->media()->where('collection', $collection)->first();

                if ($old !== null) {
                    Storage::disk($old->disk)->delete($old->path);
                    $old->delete();
                }

                $target->media()->create([
                    'disk' => 'public',
                    'path' => $path,
                    'collection' => $collection,
                    'sort_order' => 0,
                ]);
            });
        } catch (Throwable $e) {
            Storage::disk('public')->delete($path);
            throw $e;
        }
    }

    private function storeGallery(Umkm $umkm, array $files): void
    {
        $order = (int) $umkm->media()->where('collection', 'gallery')->max('sort_order');

        foreach ($files as $file) {
            $path = $file->store('umkms/'.$umkm->id.'/gallery', 'public');
            $order++;

            try {
                DB::transaction(function () use ($umkm, $path, $order) {
                    $umkm->media()->create([
                        'disk' => 'public',
                        'path' => $path,
                        'collection' => 'gallery',
                        'sort_order' => $order,
                    ]);
                });
            } catch (Throwable $e) {
                Storage::disk('public')->delete($path);
                throw $e;
            }
        }
    }

    private function directoryFor(Umkm|Product $target): string
    {
        if ($target instanceof Product) {
            return 'products/'.$target->id;
        }

        return 'umkms/'.$target->id;
    }
}