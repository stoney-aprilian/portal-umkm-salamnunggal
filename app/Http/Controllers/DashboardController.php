<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Umkm;
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

        return view('dashboard', [
            'umkm' => $umkm,
            'productCounts' => $this->productCounts($umkm),
            'activities' => $umkm === null ? collect() : $this->recentActivities($umkm),
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

        return Activity::query()
            ->where(function ($query) use ($umkm, $productIds) {
                $query->where('subject_type', Umkm::class)
                    ->where('subject_id', $umkm->id);

                if ($productIds->isNotEmpty()) {
                    $query->orWhere(function ($productQuery) use ($productIds) {
                        $productQuery->where('subject_type', Product::class)
                            ->whereIn('subject_id', $productIds);
                    });
                }
            })
            ->latest('created_at')
            ->latest('id')
            ->limit(self::ACTIVITY_LIMIT)
            ->get();
    }
}
