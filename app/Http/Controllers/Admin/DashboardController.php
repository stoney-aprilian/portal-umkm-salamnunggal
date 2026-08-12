<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\VerificationRequest;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const RECENT_LIMIT = 5;

    public function index(): View
    {
        $umkmCount = VerificationRequest::query()
            ->where('verifiable_type', Umkm::class)
            ->where('status', 'pending')
            ->count();

        $productCount = VerificationRequest::query()
            ->where('verifiable_type', Product::class)
            ->where('status', 'pending')
            ->count();

        $recentUmkm = VerificationRequest::query()
            ->with(['verifiable', 'user'])
            ->where('verifiable_type', Umkm::class)
            ->where('status', 'pending')
            ->latest('created_at')
            ->limit(self::RECENT_LIMIT)
            ->get();

        $recentProducts = VerificationRequest::query()
            ->with(['verifiable.umkm', 'user'])
            ->where('verifiable_type', Product::class)
            ->where('status', 'pending')
            ->latest('created_at')
            ->limit(self::RECENT_LIMIT)
            ->get();

        $recent = $recentUmkm
            ->concat($recentProducts)
            ->sortByDesc('created_at')
            ->take(self::RECENT_LIMIT)
            ->filter(fn (VerificationRequest $request) => $request->verifiable !== null && $request->user !== null)
            ->map(fn (VerificationRequest $request) => [
                'label' => $request->verifiable_type === Umkm::class ? 'UMKM' : 'Produk',
                'name' => $request->verifiable->name,
                'umkmName' => $request->verifiable_type === Product::class ? ($request->verifiable->umkm?->name ?? '—') : null,
                'ownerName' => $request->user->name,
                'submittedAt' => $request->created_at->format('d M Y'),
                'reviewUrl' => $request->verifiable_type === Umkm::class
                    ? route('admin.umkm.verification.show', $request)
                    : route('admin.products.verification.show', $request),
            ])
            ->values();

        return view('admin.dashboard', [
            'umkmCount' => $umkmCount,
            'productCount' => $productCount,
            'totalCount' => $umkmCount + $productCount,
            'recent' => $recent,
        ]);
    }
}
