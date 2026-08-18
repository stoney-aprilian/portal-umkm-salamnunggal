<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreProductRequest;
use App\Http\Requests\Owner\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductRevision;
use App\Support\VerificationActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Lets an owner propose changes to an approved product through a working
 * copy revision. The approved product stays public and unchanged until an
 * administrator approves the revision.
 */
class ProductRevisionController extends Controller
{
    private const ACTIVE_STATUSES = ['draft', 'pending', 'needs_revision', 'rejected'];

    private const EDITABLE_STATUSES = ['draft', 'needs_revision', 'rejected'];

    public function create(Product $product): View|RedirectResponse
    {
        $this->authorize('create', [ProductRevision::class, $product]);

        if ($redirect = $this->ensureProductChangeable($product)) {
            return $redirect;
        }

        return view('owner.product-revisions.create', [
            'product' => $product,
            'categories' => Category::where('type', 'product')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreProductRequest $request, Product $product): RedirectResponse
    {
        $this->authorize('create', [ProductRevision::class, $product]);

        if ($redirect = $this->ensureProductChangeable($product)) {
            return $redirect;
        }

        $revision = $product->revisions()->create([
            'category_id' => $request->integer('category_id'),
            'name' => $request->string('name')->toString(),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'status' => 'draft',
        ]);

        return redirect()->route('owner.products.revisions.edit', $revision)
            ->with('status', 'Pengajuan perubahan produk berhasil disimpan sebagai draft. Kelola foto dan kirim pengajuan agar diperiksa Administrator.');
    }

    public function edit(ProductRevision $revision): View|RedirectResponse
    {
        $this->authorize('update', $revision);

        if ($redirect = $this->ensureRevising($revision)) {
            return $redirect;
        }

        $revision->load('media');

        return view('owner.product-revisions.edit', [
            'revision' => $revision,
            'product' => $revision->product,
            'categories' => Category::where('type', 'product')->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, ProductRevision $revision): RedirectResponse
    {
        $this->authorize('update', $revision);

        if ($redirect = $this->ensureRevising($revision)) {
            return $redirect;
        }

        $revision->update([
            'category_id' => $request->integer('category_id'),
            'name' => $request->string('name')->toString(),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'status' => 'draft',
        ]);

        return redirect()->route('owner.products.revisions.edit', $revision->fresh())
            ->with('status', 'Perubahan produk berhasil disimpan.');
    }

    public function submit(Request $request, ProductRevision $revision): RedirectResponse
    {
        $this->authorize('submit', $revision);

        $submitted = DB::transaction(function () use ($request, $revision) {
            $affected = $revision->whereKey($revision->id)
                ->where('status', 'draft')
                ->update(['status' => 'pending']);

            if ($affected === 0) {
                return false;
            }

            $revision->verificationRequests()->create([
                'user_id' => $request->user()->id,
                'reviewer_id' => null,
                'status' => 'pending',
                'notes' => null,
                'reviewed_at' => null,
            ]);

            VerificationActivity::log('submitted', $revision, $request->user());

            return true;
        });

        if (! $submitted) {
            return redirect()->back()
                ->with('error', 'Perubahan hanya dapat dikirim untuk perubahan yang masih berstatus draft.');
        }

        return redirect()->route('owner.products.index', $revision->product->umkm)
            ->with('status', 'Pengajuan perubahan produk berhasil dikirim dan sedang menunggu pemeriksaan. Produk yang tampil di publik tidak berubah sampai perubahan disetujui.');
    }

    private function ensureProductChangeable(Product $product): ?RedirectResponse
    {
        if ($product->umkm->status !== 'approved') {
            return redirect()->route('owner.products.index', $product->umkm)
                ->with('error', 'Produk hanya dapat dikelola ketika UMKM telah disetujui.');
        }

        if ($product->status !== 'approved') {
            return redirect()->route('owner.products.index', $product->umkm)
                ->with('error', 'Perubahan hanya dapat diajukan untuk produk yang telah disetujui.');
        }

        $active = $product->revisions()
            ->whereIn('status', self::ACTIVE_STATUSES)
            ->latest('id')
            ->first();

        if ($active === null) {
            return null;
        }

        if (in_array($active->status, self::EDITABLE_STATUSES, true)) {
            return redirect()->route('owner.products.revisions.edit', $active);
        }

        return redirect()->route('owner.products.index', $product->umkm)
            ->with('error', 'Anda sudah memiliki pengajuan perubahan produk ini yang sedang diperiksa.');
    }

    private function ensureRevising(ProductRevision $revision): ?RedirectResponse
    {
        if (! in_array($revision->status, self::EDITABLE_STATUSES, true)) {
            return redirect()->route('owner.products.index', $revision->product->umkm)
                ->with('error', 'Perubahan produk hanya dapat diubah ketika berstatus draft, membutuhkan revisi, atau ditolak.');
        }

        return null;
    }
}