<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use Illuminate\View\View;

class HomeController extends Controller
{
    private const FEATURED_LIMIT = 6;

    public function index(): View
    {
        $umkmCount = Umkm::query()
            ->where('status', 'approved')
            ->count();

        $productCount = Product::query()
            ->where('status', 'approved')
            ->whereHas('umkm', fn ($query) => $query->where('status', 'approved'))
            ->count();

        $umkmCategoryCount = Category::query()
            ->where('type', 'umkm')
            ->whereIn('id', Umkm::query()->where('status', 'approved')->select('category_id'))
            ->count();

        $productCategoryCount = Category::query()
            ->where('type', 'product')
            ->whereIn('id', Product::query()
                ->where('status', 'approved')
                ->whereHas('umkm', fn ($query) => $query->where('status', 'approved'))
                ->select('category_id'))
            ->count();

        $categories = Category::query()
            ->where('type', 'umkm')
            ->whereIn('id', Umkm::query()->where('status', 'approved')->select('category_id'))
            ->orderBy('name')
            ->get();

        $featuredUmkms = Umkm::query()
            ->with([
                'category',
                'media' => fn ($query) => $query->where('collection', 'logo'),
            ])
            ->where('status', 'approved')
            ->latest()
            ->limit(self::FEATURED_LIMIT)
            ->get();

        $featuredProducts = Product::query()
            ->with([
                'category',
                'umkm',
                'media' => fn ($query) => $query->where('collection', 'product'),
            ])
            ->where('status', 'approved')
            ->whereHas('umkm', fn ($query) => $query->where('status', 'approved'))
            ->latest()
            ->limit(self::FEATURED_LIMIT)
            ->get();

        return view('home', [
            'umkmCount' => $umkmCount,
            'productCount' => $productCount,
            'categoryCount' => $umkmCategoryCount + $productCategoryCount,
            'categories' => $categories,
            'featuredUmkms' => $featuredUmkms,
            'featuredProducts' => $featuredProducts,
        ]);
    }
}
