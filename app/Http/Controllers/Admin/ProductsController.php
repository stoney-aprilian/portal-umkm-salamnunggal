<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Support\ProductManagementActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Administrator CRUD for products (assisted service). Products always
 * belong to an approved UMKM selected by the administrator (never to the
 * administrator) and are published directly with status "approved",
 * matching administrator authority.
 */
class ProductsController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->trim()->toString();
        $category = $request->string('category')->trim()->toString();
        $umkm = $request->string('umkm')->trim()->toString();
        $sort = $request->string('sort')->trim()->toString();

        $baseQuery = Product::query()->with(['umkm.user', 'category']);

        $totalCount = (clone $baseQuery)->count();
        $approvedCount = (clone $baseQuery)->where('status', 'approved')->count();
        $pendingCount = (clone $baseQuery)->where('status', 'pending')->count();
        $rejectedCount = (clone $baseQuery)->whereIn('status', ['rejected', 'needs_revision'])->count();

        $query = $baseQuery;

        if ($search !== '') {
            $term = mb_strtolower($search);
            $query->where(function ($q) use ($term): void {
                $q->whereRaw('LOWER(name) LIKE ?', ['%'.$term.'%'])
                    ->orWhereHas('umkm', function ($umkmQuery) use ($term): void {
                        $umkmQuery->whereRaw('LOWER(name) LIKE ?', ['%'.$term.'%']);
                    });
            });
        }

        if ($status !== '' && in_array($status, ['draft', 'pending', 'approved', 'needs_revision', 'rejected'], true)) {
            $query->where('status', $status);
        }

        if ($category !== '' && Category::where('type', 'product')->where('id', $category)->exists()) {
            $query->where('category_id', $category);
        }

        if ($umkm !== '' && Umkm::where('id', $umkm)->exists()) {
            $query->where('umkm_id', $umkm);
        }

        $sort = match ($sort) {
            'oldest' => 'asc',
            'name_asc' => 'name_asc',
            'name_desc' => 'name_desc',
            default => 'desc',
        };

        if ($sort === 'name_asc') {
            $query->orderBy('name', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('name', 'desc');
        } else {
            $query->orderBy('created_at', $sort);
        }

        $products = $query->get();

        return view('admin.products.index', [
            'products' => $products,
            'totalCount' => $totalCount,
            'approvedCount' => $approvedCount,
            'pendingCount' => $pendingCount,
            'rejectedCount' => $rejectedCount,
            'search' => $search,
            'status' => $status,
            'category' => $category,
            'umkm' => $umkm,
            'sort' => $sort,
            'categories' => Category::where('type', 'product')->orderBy('name')->get(),
            'umkms' => Umkm::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', [Product::class, null]);

        return view('admin.products.create', [
            'umkms' => Umkm::with('user')->where('status', 'approved')->orderBy('name')->get(),
            'categories' => Category::where('type', 'product')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->authorize('create', [Product::class, null]);

        $name = $request->string('name')->toString();

        $product = DB::transaction(function () use ($request, $name) {
            $product = Product::create([
                'umkm_id' => $request->integer('umkm_id'),
                'category_id' => $request->integer('category_id'),
                'name' => $name,
                'slug' => Product::generateUniqueSlug($name),
                'description' => $request->input('description'),
                'price' => $request->input('price'),
                'status' => 'approved',
            ]);

            ProductManagementActivity::log('product_created', $product, $request->user());

            return $product;
        });

        return redirect()->route('admin.products.show', $product)
            ->with('status', 'Produk berhasil dibuat untuk UMKM terkait dan langsung tampil di portal.');
    }

    public function show(Product $product): View
    {
        $this->authorize('view', $product);

        $product->load(['umkm.user', 'category', 'media']);

        return view('admin.products.show', ['product' => $product]);
    }

    public function edit(Product $product): View
    {
        $this->authorize('update', $product);

        $product->load(['umkm.user']);

        return view('admin.products.edit', [
            'product' => $product,
            'umkms' => Umkm::with('user')->where('status', 'approved')->orderBy('name')->get(),
            'categories' => Category::where('type', 'product')->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $name = $request->string('name')->toString();

        DB::transaction(function () use ($request, $product, $name) {
            $product->update([
                'umkm_id' => $request->integer('umkm_id'),
                'name' => $name,
                'slug' => Product::generateUniqueSlug($name, $product->id),
                'category_id' => $request->integer('category_id'),
                'description' => $request->input('description'),
                'price' => $request->input('price'),
            ]);

            ProductManagementActivity::log('product_updated', $product, $request->user());
        });

        return redirect()->route('admin.products.show', $product)
            ->with('status', 'Perubahan produk berhasil disimpan.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);

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
            $product->delete();

            ProductManagementActivity::log('product_deleted', $product, $request->user());
        });

        return redirect()->route('admin.products.index')
            ->with('status', 'Produk beserta foto dan pengajuan verifikasinya berhasil dihapus.');
    }
}