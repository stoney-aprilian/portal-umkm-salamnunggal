<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductRevision;
use App\Models\Umkm;
use App\Models\UmkmRevision;
use App\Models\User;
use App\Models\VerificationRequest;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const RECENT_LIMIT = 5;

    public function index(): View
    {
        $umkmCount = VerificationRequest::query()
            ->whereIn('verifiable_type', [Umkm::class, UmkmRevision::class])
            ->where('status', 'pending')
            ->count();

        $productCount = VerificationRequest::query()
            ->whereIn('verifiable_type', [Product::class, ProductRevision::class])
            ->where('status', 'pending')
            ->count();

        $ownerCount = VerificationRequest::query()
            ->where('verifiable_type', User::class)
            ->where('status', 'pending')
            ->count();

        $recentUmkm = VerificationRequest::query()
            ->with(['verifiable', 'user'])
            ->whereIn('verifiable_type', [Umkm::class, UmkmRevision::class])
            ->where('status', 'pending')
            ->latest('created_at')
            ->limit(self::RECENT_LIMIT)
            ->get();

        $recentProducts = VerificationRequest::query()
            ->with(['verifiable.umkm', 'user'])
            ->whereIn('verifiable_type', [Product::class, ProductRevision::class])
            ->where('status', 'pending')
            ->latest('created_at')
            ->limit(self::RECENT_LIMIT)
            ->get();

        $recentOwners = VerificationRequest::query()
            ->with(['verifiable', 'user'])
            ->where('verifiable_type', User::class)
            ->where('status', 'pending')
            ->latest('created_at')
            ->limit(self::RECENT_LIMIT)
            ->get();

        $recent = $recentUmkm
            ->concat($recentProducts)
            ->concat($recentOwners)
            ->sortByDesc('created_at')
            ->take(self::RECENT_LIMIT)
            ->filter(fn (VerificationRequest $request) => $request->verifiable !== null && $request->user !== null)
            ->map(fn (VerificationRequest $request) => [
                'label' => match ($request->verifiable_type) {
                    UmkmRevision::class => 'Perubahan UMKM',
                    ProductRevision::class => 'Perubahan Produk',
                    Product::class => 'Produk',
                    User::class => 'Owner',
                    default => 'UMKM',
                },
                'name' => $request->verifiable->name,
                'umkmName' => match ($request->verifiable_type) {
                    Product::class => $request->verifiable->umkm?->name ?? '—',
                    ProductRevision::class => $request->verifiable->product->umkm?->name ?? '—',
                    default => null,
                },
                'ownerName' => $request->user->name,
                'submittedAt' => $request->created_at->format('d M Y'),
                'reviewUrl' => match ($request->verifiable_type) {
                    Umkm::class, UmkmRevision::class => route('admin.umkm.verification.show', $request),
                    Product::class, ProductRevision::class => route('admin.products.verification.show', $request),
                    default => route('admin.owner-verification.show', $request),
                },
            ])
            ->values();

        $totalUmkm = Umkm::count();
        $totalProduct = Product::count();
        $totalUser = User::count();
        $totalCategory = Category::count();
        $verifiedCount = VerificationRequest::where('status', 'approved')->count();

        return view('admin.dashboard', [
            'umkmCount' => $umkmCount,
            'productCount' => $productCount,
            'ownerCount' => $ownerCount,
            'totalCount' => $umkmCount + $productCount + $ownerCount,
            'recent' => $recent,
            'totalUmkm' => $totalUmkm,
            'totalProduct' => $totalProduct,
            'totalUser' => $totalUser,
            'totalCategory' => $totalCategory,
            'verifiedCount' => $verifiedCount,
        ]);
    }
}