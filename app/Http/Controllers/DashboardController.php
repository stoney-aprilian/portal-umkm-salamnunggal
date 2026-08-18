<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductRevision;
use App\Models\Umkm;
use App\Models\UmkmRevision;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    private const ACTIVITY_LIMIT = 5;

    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->hasRole('administrator')) {
            return redirect()->route('admin.dashboard');
        }

        $umkm = $user->umkm;

        $umkmRevision = $umkm?->revisions()
            ->whereIn('status', ['draft', 'pending', 'needs_revision', 'rejected'])
            ->latest('id')
            ->first();

        return view('dashboard', [
            'umkm' => $umkm,
            'productCounts' => $this->productCounts($umkm),
            'activities' => $umkm === null ? collect() : $this->recentActivities($umkm),
            'umkmRevision' => $umkmRevision,
            'umkmRevisionNote' => $umkmRevision?->verificationRequests()
                ->whereIn('status', ['needs_revision', 'rejected'])
                ->latest('id')
                ->value('notes'),
            'revisionNote' => $umkm?->verificationRequests()
                ->where('status', 'needs_revision')
                ->latest('id')
                ->value('notes'),
            'rejectionNote' => $umkm?->verificationRequests()
                ->where('status', 'rejected')
                ->latest('id')
                ->value('notes'),
        ]);
    }

    private function productCounts(?Umkm $umkm): array
    {
        $counts = ['draft' => 0, 'pending' => 0, 'approved' => 0, 'needs_revision' => 0, 'rejected' => 0];

        if ($umkm === null) {
            return $counts;
        }

        foreach ($umkm->products()->selectRaw('status, count(*) as total')->groupBy('status')->get() as $row) {
            $counts[$row->status] = $row->total;
        }

        $counts['total'] = array_sum($counts);

        return $counts;
    }

    private function recentActivities(Umkm $umkm): Collection
    {
        $productIds = $umkm->products()->pluck('id');
        $umkmRevisionIds = $umkm->revisions()->pluck('id');
        $productRevisionIds = $productIds->isEmpty()
            ? collect()
            : ProductRevision::whereIn('product_id', $productIds)->pluck('id');

        return Activity::query()
            ->where(function ($query) use ($umkm, $productIds, $umkmRevisionIds, $productRevisionIds) {
                $query->where('subject_type', Umkm::class)
                    ->where('subject_id', $umkm->id);

                if ($productIds->isNotEmpty()) {
                    $query->orWhere(function ($productQuery) use ($productIds) {
                        $productQuery->where('subject_type', Product::class)
                            ->whereIn('subject_id', $productIds);
                    });
                }

                if ($umkmRevisionIds->isNotEmpty()) {
                    $query->orWhere(function ($revisionQuery) use ($umkmRevisionIds) {
                        $revisionQuery->where('subject_type', UmkmRevision::class)
                            ->whereIn('subject_id', $umkmRevisionIds);
                    });
                }

                if ($productRevisionIds->isNotEmpty()) {
                    $query->orWhere(function ($productRevisionQuery) use ($productRevisionIds) {
                        $productRevisionQuery->where('subject_type', ProductRevision::class)
                            ->whereIn('subject_id', $productRevisionIds);
                    });
                }
            })
            ->latest('created_at')
            ->latest('id')
            ->limit(self::ACTIVITY_LIMIT)
            ->get();
    }
}
