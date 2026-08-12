<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function umkm(Category $category): View
    {
        abort_unless($category->type === 'umkm', 404);

        $umkms = Umkm::query()
            ->with([
                'category',
                'media' => fn ($query) => $query->where('collection', 'logo'),
            ])
            ->where('status', 'approved')
            ->where('category_id', $category->id)
            ->latest()
            ->get();

        return view('public.categories.umkm', ['category' => $category, 'umkms' => $umkms]);
    }

    public function product(Category $category): View
    {
        abort_unless($category->type === 'product', 404);

        $products = Product::query()
            ->with([
                'category',
                'umkm',
                'media' => fn ($query) => $query->where('collection', 'product'),
            ])
            ->where('status', 'approved')
            ->where('category_id', $category->id)
            ->whereHas('umkm', fn ($query) => $query->where('status', 'approved'))
            ->latest()
            ->get();

        return view('public.categories.product', ['category' => $category, 'products' => $products]);
    }
}
