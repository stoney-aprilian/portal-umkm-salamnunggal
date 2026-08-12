<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    private const RESULT_LIMIT = 12;

    private const CATEGORY_LIMIT = 8;

    public function __invoke(Request $request): View
    {
        $query = trim((string) $request->query('q'));

        $umkms = collect();
        $products = collect();
        $categories = collect();

        if ($query !== '') {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $query);

            $umkms = Umkm::query()
                ->with([
                    'category',
                    'media' => fn ($q) => $q->where('collection', 'logo'),
                ])
                ->where('status', 'approved')
                ->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                })
                ->latest()
                ->limit(self::RESULT_LIMIT)
                ->get();

            $products = Product::query()
                ->with([
                    'category',
                    'umkm',
                    'media' => fn ($q) => $q->where('collection', 'product'),
                ])
                ->where('status', 'approved')
                ->whereHas('umkm', fn ($builder) => $builder->where('status', 'approved'))
                ->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                })
                ->latest()
                ->limit(self::RESULT_LIMIT)
                ->get();

            $categories = Category::query()
                ->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                })
                ->where(function ($builder) {
                    $builder->where(function ($type) {
                        $type->where('type', 'umkm')
                            ->whereHas('umkms', fn ($builder) => $builder->where('status', 'approved'));
                    })->orWhere(function ($type) {
                        $type->where('type', 'product')
                            ->whereHas('products', fn ($builder) => $builder
                                ->where('status', 'approved')
                                ->whereHas('umkm', fn ($builder) => $builder->where('status', 'approved')));
                    });
                })
                ->latest()
                ->limit(self::CATEGORY_LIMIT)
                ->get();
        }

        return view('public.search', [
            'query' => $query,
            'umkms' => $umkms,
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
