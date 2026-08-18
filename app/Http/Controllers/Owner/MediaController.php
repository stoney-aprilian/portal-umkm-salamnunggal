<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreMediaRequest;
use App\Models\Media;
use App\Models\Product;
use App\Models\ProductRevision;
use App\Models\Umkm;
use App\Models\UmkmRevision;
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

    public function storeUmkmRevisionMedia(StoreMediaRequest $request, UmkmRevision $revision, string $collection): RedirectResponse
    {
        abort_unless(in_array($collection, self::UMKM_COLLECTIONS, true), 404);

        $this->authorize('update', $revision);

        if ($redirect = $this->ensureRevisionRevising($revision)) {
            return $redirect;
        }

        if ($collection === 'gallery') {
            $this->storeGallery($revision, $request->file('gallery', []));
        } else {
            $this->replaceSingle($revision, $collection, $request->file($request->inputName()));
        }

        return redirect()->back()
            ->with('status', 'Media perubahan berhasil diunggah. Media ini hanya tampil setelah perubahan disetujui.');
    }

    public function storeProductRevisionMedia(StoreMediaRequest $request, ProductRevision $revision, string $collection): RedirectResponse
    {
        abort_unless(in_array($collection, self::PRODUCT_COLLECTIONS, true), 404);

        $this->authorize('update', $revision);

        if ($redirect = $this->ensureRevisionRevising($revision)) {
            return $redirect;
        }

        $this->replaceSingle($revision, $collection, $request->file($request->inputName()));

        return redirect()->back()
            ->with('status', 'Foto produk perubahan berhasil diunggah. Foto ini hanya tampil setelah perubahan disetujui.');
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

        if ($mediable instanceof UmkmRevision || $mediable instanceof ProductRevision) {
            return $this->ensureRevisionRevising($mediable);
        }

        return null;
    }

    /**
     * Stores the uploaded file first, then swaps the media record inside
     * a transaction. A failed upload never touches the existing media.
     */
    private function replaceSingle(Umkm|Product|UmkmRevision|ProductRevision $target, string $collection, UploadedFile $file): void
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

    private function storeGallery(Umkm|UmkmRevision $target, array $files): void
    {
        $order = (int) $target->media()->where('collection', 'gallery')->max('sort_order');

        foreach ($files as $file) {
            $path = $file->store($this->directoryFor($target, 'gallery'), 'public');
            $order++;

            try {
                DB::transaction(function () use ($target, $path, $order) {
                    $target->media()->create([
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

    private function directoryFor(Umkm|Product|UmkmRevision|ProductRevision $target, string $collection): string
    {
        if ($target instanceof UmkmRevision) {
            return 'umkms/'.$target->umkm_id.'/revisions/'.$target->id;
        }

        if ($target instanceof ProductRevision) {
            return 'products/'.$target->product_id.'/revisions/'.$target->id;
        }

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

    private function ensureRevisionRevising(UmkmRevision|ProductRevision $revision): ?RedirectResponse
    {
        if (! in_array($revision->status, ['draft', 'needs_revision', 'rejected'], true)) {
            return redirect()->back()
                ->with('error', 'Media perubahan hanya dapat dikelola ketika perubahan masih dapat diubah.');
        }

        return null;
    }
}
