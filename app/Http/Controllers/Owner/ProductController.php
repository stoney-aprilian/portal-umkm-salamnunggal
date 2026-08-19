<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreProductRequest;
use App\Http\Requests\Owner\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Support\ProductManagementActivity;
use App\Support\VerificationActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Umkm $umkm): View|RedirectResponse
    {
        $this->authorize('create', [Product::class, $umkm]);

        if ($redirect = $this->ensureUmkmApproved($umkm)) {
            return $redirect;
        }

        return view('owner.products.index', [
            'umkm' => $umkm,
            'products' => $umkm->products()->with('media', 'revisions')->latest('id')->get(),
        ]);
    }

    public function create(Umkm $umkm): View|RedirectResponse
    {
        $this->authorize('create', [Product::class, $umkm]);

        if ($redirect = $this->ensureUmkmApproved($umkm)) {
            return $redirect;
        }

        return view('owner.products.create', [
            'umkm' => $umkm,
            'categories' => Category::where('type', 'product')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreProductRequest $request, Umkm $umkm): RedirectResponse
    {
        $this->authorize('create', [Product::class, $umkm]);

        if ($redirect = $this->ensureUmkmApproved($umkm)) {
            return $redirect;
        }

        $name = $request->string('name')->toString();

        $umkm->products()->create([
            'category_id' => $request->integer('category_id'),
            'name' => $name,
            'slug' => Product::generateUniqueSlug($name),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'status' => 'draft',
        ]);

        return redirect()->route('owner.products.index', $umkm)
            ->with('status', 'Draft produk berhasil disimpan.');
    }

    public function show(Product $product): View|RedirectResponse
    {
        $this->authorize('view', $product);

        if ($redirect = $this->ensureUmkmApproved($product->umkm)) {
            return $redirect;
        }

        $product->load(['umkm', 'category', 'media']);

        return view('owner.products.show', ['product' => $product]);
    }

    public function edit(Product $product): View|RedirectResponse
    {
        $this->authorize('update', $product);

        if ($redirect = $this->ensureUmkmApproved($product->umkm)) {
            return $redirect;
        }

        if ($redirect = $this->ensureEditable($product)) {
            return $redirect;
        }

        return view('owner.products.edit', [
            'product' => $product,
            'categories' => Category::where('type', 'product')->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        if ($redirect = $this->ensureUmkmApproved($product->umkm)) {
            return $redirect;
        }

        if ($redirect = $this->ensureEditable($product)) {
            return $redirect;
        }

        $name = $request->string('name')->toString();

        $product->update([
            'category_id' => $request->integer('category_id'),
            'name' => $name,
            'slug' => Product::generateUniqueSlug($name, $product->id),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'status' => 'draft',
        ]);

        return redirect()->route('owner.products.index', $product->umkm)
            ->with('status', 'Perubahan produk berhasil disimpan.');
    }

    public function submit(Product $product, Request $request): RedirectResponse
    {
        $this->authorize('submit', $product);

        if ($redirect = $this->ensureUmkmApproved($product->umkm)) {
            return $redirect;
        }

        $submitted = DB::transaction(function () use ($product, $request) {
            $affected = $product->whereKey($product->id)->where('status', 'draft')->update(['status' => 'pending']);

            if ($affected === 0) {
                return false;
            }

            $product->verificationRequests()->create([
                'user_id' => $request->user()->id,
                'reviewer_id' => null,
                'status' => 'pending',
                'notes' => null,
                'reviewed_at' => null,
            ]);

            VerificationActivity::log('submitted', $product, $request->user());

            return true;
        });

        if (! $submitted) {
            return redirect()->back()
                ->with('error', 'Pengajuan produk hanya dapat dikirim untuk produk dengan status draft.');
        }

        return redirect()->route('owner.products.index', $product->umkm)
            ->with('status', 'Pengajuan produk berhasil dikirim dan sedang menunggu pemeriksaan.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);

        if ($redirect = $this->ensureUmkmApproved($product->umkm)) {
            return $redirect;
        }

        if ($redirect = $this->ensureDeletable($product)) {
            return $redirect;
        }

        DB::transaction(function () use ($request, $product) {
            foreach ($product->revisions()->with('media')->get() as $revision) {
                foreach ($revision->media as $media) {
                    Storage::disk($media->disk)->delete($media->path);
                    $media->delete();
                }

                $revision->verificationRequests()->delete();
                $revision->delete();
            }

            foreach ($product->media as $media) {
                Storage::disk($media->disk)->delete($media->path);
                $media->delete();
            }

            $product->verificationRequests()->delete();

            ProductManagementActivity::log('product_deleted', $product, $request->user());
            $product->delete();
        });

        return redirect()->route('owner.products.index', $product->umkm)
            ->with('status', 'Produk beserta foto dan pengajuannya berhasil dihapus.');
    }

    private function ensureUmkmApproved(Umkm $umkm): ?RedirectResponse
    {
        if ($umkm->status !== 'approved') {
            return redirect()->back()
                ->with('error', 'Produk hanya dapat dikelola ketika UMKM telah disetujui.');
        }

        return null;
    }

    private function ensureDeletable(Product $product): ?RedirectResponse
    {
        if (! in_array($product->status, ['draft', 'needs_revision', 'rejected'], true)) {
            return redirect()->back()
                ->with('error', 'Produk hanya dapat dihapus ketika berstatus draft, membutuhkan revisi, atau ditolak.');
        }

        return null;
    }

    private function ensureEditable(Product $product): ?RedirectResponse
    {
        if (! in_array($product->status, ['draft', 'needs_revision', 'rejected'], true)) {
            return redirect()->back()
                ->with('error', 'Produk hanya dapat diubah ketika berstatus draft, membutuhkan revisi, atau ditolak.');
        }

        return null;
    }
}
