<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectVerificationRequest;
use App\Http\Requests\Admin\RevisionVerificationRequest;
use App\Models\Umkm;
use App\Models\UmkmRevision;
use App\Models\VerificationRequest;
use App\Support\VerificationActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Reviews both initial UMKM submissions (verifiable = Umkm) and change
 * submissions (verifiable = UmkmRevision). Approving a change copies the
 * revision onto the approved UMKM without touching its ownership, then
 * archives the revision so the verification history stays intact.
 */
class UmkmVerificationController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $baseQuery = VerificationRequest::query()
            ->whereIn('verifiable_type', [Umkm::class, UmkmRevision::class]);

        $pendingCount = (clone $baseQuery)->where('status', 'pending')->count();
        $approvedCount = (clone $baseQuery)->where('status', 'approved')->count();
        $rejectedCount = (clone $baseQuery)->where('status', 'rejected')->count();
        $totalCount = (clone $baseQuery)->count();

        $requests = $baseQuery
            ->where('status', 'pending')
            ->with(['verifiable.category', 'user'])
            ->orderByDesc('created_at')
            ->get()
            ->filter(function (VerificationRequest $vr) use ($search): bool {
                if ($search === '') {
                    return true;
                }

                $term = mb_strtolower($search);

                return str_contains(mb_strtolower($vr->verifiable->name ?? ''), $term)
                    || str_contains(mb_strtolower($vr->user->name ?? ''), $term);
            });

        return view('admin.umkm-verification.index', [
            'requests' => $requests,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'totalCount' => $totalCount,
            'search' => $search,
        ]);
    }

    public function show(VerificationRequest $verificationRequest): View
    {
        $this->authorize('review', $verificationRequest);
        $this->ensureUmkmRequest($verificationRequest);

        $verificationRequest->load(['verifiable.category', 'user']);

        return view('admin.umkm-verification.show', [
            'request' => $verificationRequest,
            'umkm' => $verificationRequest->verifiable,
            'current' => $verificationRequest->verifiable instanceof UmkmRevision
                ? $verificationRequest->verifiable->load('media')->umkm
                : null,
        ]);
    }

    public function approve(Request $request, VerificationRequest $verificationRequest): RedirectResponse
    {
        $this->authorize('review', $verificationRequest);
        $this->ensureUmkmRequest($verificationRequest);

        $approved = DB::transaction(function () use ($request, $verificationRequest) {
            $affected = $verificationRequest->whereKey($verificationRequest->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'approved',
                    'reviewer_id' => $request->user()->id,
                    'reviewed_at' => now(),
                    'notes' => null,
                ]);

            if ($affected === 0) {
                return false;
            }

            $verifiable = $verificationRequest->verifiable;

            if ($verifiable instanceof UmkmRevision) {
                $this->applyChange($verifiable);
            } else {
                $verifiable->update(['status' => 'approved']);
            }

            VerificationActivity::log('approved', $verifiable, $request->user());

            return true;
        });

        if (! $approved) {
            return redirect()->back()
                ->with('error', 'Pengajuan ini sudah diperiksa dan tidak dapat diubah lagi.');
        }

        return redirect()->route('admin.umkm.verification.index')
            ->with('status', 'Pengajuan UMKM berhasil disetujui.');
    }

    public function reject(RejectVerificationRequest $request, VerificationRequest $verificationRequest): RedirectResponse
    {
        $this->authorize('review', $verificationRequest);
        $this->ensureUmkmRequest($verificationRequest);

        $rejected = DB::transaction(function () use ($request, $verificationRequest) {
            $affected = $verificationRequest->whereKey($verificationRequest->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'reviewer_id' => $request->user()->id,
                    'reviewed_at' => now(),
                    'notes' => $request->string('notes')->toString(),
                ]);

            if ($affected === 0) {
                return false;
            }

            $verificationRequest->verifiable->update(['status' => 'rejected']);

            VerificationActivity::log('rejected', $verificationRequest->verifiable, $request->user());

            return true;
        });

        if (! $rejected) {
            return redirect()->back()
                ->with('error', 'Pengajuan ini sudah diperiksa dan tidak dapat diubah lagi.');
        }

        return redirect()->route('admin.umkm.verification.index')
            ->with('status', 'Pengajuan UMKM ditolak.');
    }

    public function needsRevision(RevisionVerificationRequest $request, VerificationRequest $verificationRequest): RedirectResponse
    {
        $this->authorize('review', $verificationRequest);
        $this->ensureUmkmRequest($verificationRequest);

        $revised = DB::transaction(function () use ($request, $verificationRequest) {
            $affected = $verificationRequest->whereKey($verificationRequest->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'needs_revision',
                    'reviewer_id' => $request->user()->id,
                    'reviewed_at' => now(),
                    'notes' => $request->string('notes')->toString(),
                ]);

            if ($affected === 0) {
                return false;
            }

            $verificationRequest->verifiable->update(['status' => 'needs_revision']);

            VerificationActivity::log('needs_revision', $verificationRequest->verifiable, $request->user());

            return true;
        });

        if (! $revised) {
            return redirect()->back()
                ->with('error', 'Pengajuan ini sudah diperiksa dan tidak dapat diubah lagi.');
        }

        return redirect()->route('admin.umkm.verification.index')
            ->with('status', 'Pengajuan UMKM ditandai perlu revisi.');
    }

    /**
     * Copies the approved change onto the approved UMKM, moves the change
     * media over, and archives the revision. Ownership never changes.
     */
    private function applyChange(UmkmRevision $revision): void
    {
        $umkm = $revision->umkm;

        $umkm->update([
            'category_id' => $revision->category_id,
            'name' => $revision->name,
            'slug' => Umkm::generateUniqueSlug($revision->name, $umkm->id),
            'description' => $revision->description,
            'address' => $revision->address,
            'google_maps' => $revision->google_maps,
            'phone' => $revision->phone,
            'email' => $revision->email,
            'website' => $revision->website,
            'instagram' => $revision->instagram,
            'facebook' => $revision->facebook,
            'tiktok' => $revision->tiktok,
            'operational_hours' => $revision->operational_hours,
        ]);

        foreach ($revision->media as $media) {
            if (in_array($media->collection, ['logo', 'banner'], true)) {
                $old = $umkm->media()->where('collection', $media->collection)->first();

                if ($old !== null) {
                    Storage::disk($old->disk)->delete($old->path);
                    $old->delete();
                }

                $media->update([
                    'mediable_type' => Umkm::class,
                    'mediable_id' => $umkm->id,
                    'sort_order' => 0,
                ]);
            } elseif ($media->collection === 'gallery') {
                $order = (int) $umkm->media()->where('collection', 'gallery')->max('sort_order');

                $media->update([
                    'mediable_type' => Umkm::class,
                    'mediable_id' => $umkm->id,
                    'sort_order' => $order + $media->sort_order,
                ]);
            }
        }

        $revision->update(['status' => 'approved']);
    }

    private function ensureUmkmRequest(VerificationRequest $verificationRequest): void
    {
        if (! $verificationRequest->verifiable instanceof Umkm
            && ! $verificationRequest->verifiable instanceof UmkmRevision) {
            abort(404);
        }
    }
}