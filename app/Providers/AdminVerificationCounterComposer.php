<?php

namespace App\Providers;

use App\Models\VerificationRequest;
use Illuminate\Contracts\View\View;

class AdminVerificationCounterComposer
{
    public function compose(View $view): void
    {
        $umkmCount = VerificationRequest::query()
            ->whereIn('verifiable_type', [\App\Models\Umkm::class, \App\Models\UmkmRevision::class])
            ->where('status', 'pending')
            ->count();

        $productCount = VerificationRequest::query()
            ->whereIn('verifiable_type', [\App\Models\Product::class, \App\Models\ProductRevision::class])
            ->where('status', 'pending')
            ->count();

        $ownerCount = VerificationRequest::query()
            ->where('verifiable_type', \App\Models\User::class)
            ->where('status', 'pending')
            ->count();

        $view->with('totalPending', $umkmCount + $productCount + $ownerCount);
        $view->with('pendingUmkm', $umkmCount);
        $view->with('pendingProduct', $productCount);
        $view->with('pendingOwner', $ownerCount);
    }
}
