<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreMediaRequest;
use App\Models\Media;
use App\Models\Product;
use App\Models\Umkm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Minimal media management for UMKM (logo, banner, gallery) and
 * products (single photo). All targets come from route model binding;
 * the request never supplies disk, path, collection, or mediable ids.
 */
class MediaController extends Controller
{
    private const UMKM_COLLECTIONS = ['logo', 'banner', 'gallery'];

    private const PRODUCT_COLLECTIONS = ['product'];

    public function storeUmkmMedia(StoreMediaRequest $request, Umkm $umkm, string $collection): RedirectResponse
    {
        abort_unless(in_array($collection, self::UMKM_COLLECTIONS, true), 404);

        $this->authorize('update', $umkm);

        if ($redirect = $this->ensureUmkmRevising($umkm)) {
            return $redirect;
        }

        if ($collection === 'gallery') {
            $this->storeGallery($umkm, $request->file('gallery', []));
        } else {
            $this->replaceSingle($umkm, $collection, $request->file($request->inputName()));
        }

        return redirect()->back()
            ->with('status', 'Media berhasil diunggah.');
    }

    public function storeProductMedia(StoreMediaRequest $request, Product $product, string $collection): RedirectResponse
    {
        abort_unless(in_array($collection, self::PRODUCT_COLLECTIONS, true), 404);

        $this->authorize('update', $product);

        if ($redirect = $this->ensureProductMediaEditable($product)) {
            return $redirect;
        }

        $this->replaceSingle($product, $collection, $request->file($request->inputName()));

        return redirect()->back()
            ->with('status', 'Foto produk berhasil diunggah.');
    }

    public function destroy(Media $media): RedirectResponse
    {
        $this->authorize('delete', $media);

        if ($redirect = $this->ensureMediaDeletable($media)) {
            return $redirect;
        }

        $disk = $media->disk;
        $path = $media->path;

        $media->delete();
        Storage::disk($disk)->delete($path);

        return redirect()->back()
            ->with('status', 'Media berhasil dihapus.');
    }

    private function ensureMediaDeletable(Media $media): ?RedirectResponse
    {
        $mediable = $media->mediable;

        if ($mediable instanceof Umkm) {
            return $this->ensureUmkmRevising($mediable);
        }

        if ($mediable instanceof Product) {
            return $this->ensureProductMediaEditable($mediable);
        }

        return null;
    }

    /**
     * Stores the uploaded file first, then swaps the media record inside
     * a transaction. A failed upload never touches the existing media.
     */
    private function replaceSingle(Umkm|Product $target, string $collection, UploadedFile $file): void
    {
        $path = $file->store($this->directoryFor($target, $collection), 'public');

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

    private function directoryFor(Umkm|Product $target, string $collection): string
    {
        if ($target instanceof Product) {
            return 'products/'.$target->id;
        }

        return 'umkms/'.$target->id;
    }

    private function ensureUmkmRevising(Umkm $umkm): ?RedirectResponse
    {
        if (! in_array($umkm->status, ['draft', 'needs_revision', 'rejected'], true)) {
            return redirect()->back()
                ->with('error', 'Media hanya dapat dikelola ketika UMKM masih dapat diubah.');
        }

        return null;
    }

    private function ensureProductMediaEditable(Product $product): ?RedirectResponse
    {
        if ($product->umkm->status !== 'approved') {
            return redirect()->back()
                ->with('error', 'Media produk hanya dapat dikelola ketika UMKM telah disetujui.');
        }

        if (! in_array($product->status, ['draft', 'needs_revision', 'rejected'], true)) {
            return redirect()->back()
                ->with('error', 'Media produk hanya dapat dikelola ketika produk masih dapat diubah.');
        }

        return null;
    }
}
