<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Support\CategoryActivity;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Administrator Category Management. Categories stay in the single
 * `categories` table with type `umkm` or `product`. Type is bound in the
 * route and never accepted from the form, so the two category types can
 * never be mixed. Deleting a category that is still referenced by UMKM
 * or Product is blocked (the foreign key protects the data; a friendly
 * message is shown instead of a raw constraint error).
 */
class CategoriesController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Category::class);

        $search = $request->string('search')->trim()->toString();

        $umkmQuery = Category::where('type', 'umkm')->withCount(['umkms', 'products']);
        $productQuery = Category::where('type', 'product')->withCount(['umkms', 'products']);

        $totalUmkm = (clone $umkmQuery)->count();
        $totalProduct = (clone $productQuery)->count();

        if ($search !== '') {
            $term = mb_strtolower($search);
            $umkmQuery->whereRaw('LOWER(name) LIKE ?', ['%'.$term.'%']);
            $productQuery->whereRaw('LOWER(name) LIKE ?', ['%'.$term.'%']);
        }

        $umkmCategories = $umkmQuery->orderBy('name')->get();
        $productCategories = $productQuery->orderBy('name')->get();

        return view('admin.categories.index', [
            'umkmCategories' => $umkmCategories,
            'productCategories' => $productCategories,
            'totalCount' => $totalUmkm + $totalProduct,
            'totalUmkm' => $totalUmkm,
            'totalProduct' => $totalProduct,
            'search' => $search,
        ]);
    }

    public function create(Request $request, string $type): View
    {
        $this->authorize('create', Category::class);
        $this->ensureType($type);

        return view('admin.categories.create', [
            'type' => $type,
        ]);
    }

    public function store(StoreCategoryRequest $request, string $type): RedirectResponse
    {
        $this->authorize('create', Category::class);
        $this->ensureType($type);

        $name = $request->string('name')->toString();

        $category = DB::transaction(function () use ($request, $type, $name) {
            $category = Category::create([
                'type' => $type,
                'name' => $name,
                'slug' => Category::generateUniqueSlug($name),
                'description' => $request->input('description'),
            ]);

            CategoryActivity::log('category_created', $category, $request->user());

            return $category;
        });

        return redirect()->route('admin.categories.index')
            ->with('status', "Kategori {$name} berhasil dibuat.");
    }

    public function edit(Request $request, Category $category): View
    {
        $this->authorize('update', $category);

        return view('admin.categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);

        $name = $request->string('name')->toString();

        DB::transaction(function () use ($request, $category, $name) {
            $category->update([
                'name' => $name,
                'slug' => Category::generateUniqueSlug($name, $category->id),
                'description' => $request->input('description'),
            ]);

            CategoryActivity::log('category_updated', $category, $request->user());
        });

        return redirect()->route('admin.categories.index')
            ->with('status', "Kategori {$name} berhasil diperbarui.");
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        $umkmCount = $category->umkms()->count();
        $productCount = $category->products()->count();

        if ($umkmCount > 0 || $productCount > 0) {
            return redirect()->back()
                ->with('error', "Kategori {$category->name} masih digunakan oleh {$umkmCount} UMKM dan {$productCount} produk, sehingga tidak dapat dihapus.");
        }

        try {
            DB::transaction(function () use ($request, $category) {
                $category->delete();

                CategoryActivity::log('category_deleted', $category, $request->user());
            });
        } catch (QueryException) {
            return redirect()->back()
                ->with('error', "Kategori {$category->name} tidak dapat dihapus karena masih digunakan.");
        }

        return redirect()->route('admin.categories.index')
            ->with('status', "Kategori {$category->name} berhasil dihapus.");
    }

    private function ensureType(string $type): void
    {
        if (! in_array($type, ['umkm', 'product'], true)) {
            abort(404);
        }
    }
}