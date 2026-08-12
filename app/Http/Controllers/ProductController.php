<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with([
                'category',
                'umkm',
                'media' => fn ($query) => $query->where('collection', 'product'),
            ])
            ->where('status', 'approved')
            ->whereHas('umkm', fn ($query) => $query->where('status', 'approved'))
            ->latest()
            ->get();

        return view('public.products.index', ['products' => $products]);
    }

    public function show(Product $product): View
    {
        $product->load('umkm');

        abort_unless($product->status === 'approved' && $product->umkm?->status === 'approved', 404);

        $product->load([
            'category',
            'media' => fn ($query) => $query->where('collection', 'product')->orderBy('sort_order'),
        ]);

        $similarProducts = collect();

        if ($product->category) {
            $similarProducts = Product::query()
                ->with([
                    'category',
                    'umkm',
                    'media' => fn ($query) => $query->where('collection', 'product'),
                ])
                ->where('status', 'approved')
                ->where('category_id', $product->category_id)
                ->whereKeyNot($product->id)
                ->whereHas('umkm', fn ($query) => $query->where('status', 'approved'))
                ->latest()
                ->limit(4)
                ->get();
        }

        return view('public.products.show', ['product' => $product, 'similarProducts' => $similarProducts]);
    }
}
