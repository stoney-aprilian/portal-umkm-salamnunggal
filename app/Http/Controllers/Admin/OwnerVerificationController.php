<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectVerificationRequest;
use App\Http\Requests\Admin\RevisionVerificationRequest;
use App\Models\User;
use App\Models\VerificationRequest;
use App\Support\VerificationActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Reviews owner account verification submissions (verifiable = User)
 * submitted through Self-Service registration or an owner resubmission.
 *
 * Reviewing only touches the owner's account status and the verification
 * request itself: ownership of UMKM data, roles, and passwords are never
 * altered here. The target must be an owner account (enforced by
 * ensureUserRequest); administrators can never be reviewed.
 */
class OwnerVerificationController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $baseQuery = VerificationRequest::query()
            ->where('verifiable_type', User::class);

        $pendingCount = (clone $baseQuery)->where('status', 'pending')->count();
        $approvedCount = (clone $baseQuery)->where('status', 'approved')->count();
        $rejectedCount = (clone $baseQuery)->where('status', 'rejected')->count();
        $totalCount = (clone $baseQuery)->count();

        $requests = $baseQuery
            ->where('status', 'pending')
            ->with(['verifiable'])
            ->orderByDesc('created_at')
            ->get()
            ->filter(function (VerificationRequest $vr) use ($search): bool {
                if ($search === '') {
                    return true;
                }

                $term = mb_strtolower($search);

                return str_contains(mb_strtolower($vr->verifiable->name ?? ''), $term)
                    || str_contains(mb_strtolower($vr->verifiable->email ?? ''), $term);
            });

        return view('admin.owner-verification.index', [
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
        $this->ensureUserRequest($verificationRequest);

        $verificationRequest->load(['verifiable.umkm']);

        return view('admin.owner-verification.show', [
            'request' => $verificationRequest,
            'owner' => $verificationRequest->verifiable,
        ]);
    }

    public function approve(Request $request, VerificationRequest $verificationRequest): RedirectResponse
    {
        $this->authorize('review', $verificationRequest);
        $this->ensureUserRequest($verificationRequest);

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

            $verifiable->update(['status' => 'approved']);

            VerificationActivity::log('approved', $verifiable, $request->user());

            return true;
        });

        if (! $approved) {
            return redirect()->back()
                ->with('error', 'Pengajuan ini sudah diperiksa dan tidak dapat diubah lagi.');
        }

        return redirect()->route('admin.owner-verification.index')
            ->with('status', 'Akun owner berhasil disetujui.');
    }

    public function reject(RejectVerificationRequest $request, VerificationRequest $verificationRequest): RedirectResponse
    {
        $this->authorize('review', $verificationRequest);
        $this->ensureUserRequest($verificationRequest);

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

            $verifiable = $verificationRequest->verifiable;

            $verifiable->update(['status' => 'rejected']);

            VerificationActivity::log('rejected', $verifiable, $request->user());

            return true;
        });

        if (! $rejected) {
            return redirect()->back()
                ->with('error', 'Pengajuan ini sudah diperiksa dan tidak dapat diubah lagi.');
        }

        return redirect()->route('admin.owner-verification.index')
            ->with('status', 'Akun owner ditolak.');
    }

    public function needsRevision(RevisionVerificationRequest $request, VerificationRequest $verificationRequest): RedirectResponse
    {
        $this->authorize('review', $verificationRequest);
        $this->ensureUserRequest($verificationRequest);

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

            $verifiable = $verificationRequest->verifiable;

            $verifiable->update(['status' => 'needs_revision']);

            VerificationActivity::log('needs_revision', $verifiable, $request->user());

            return true;
        });

        if (! $revised) {
            return redirect()->back()
                ->with('error', 'Pengajuan ini sudah diperiksa dan tidak dapat diubah lagi.');
        }

        return redirect()->route('admin.owner-verification.index')
            ->with('status', 'Akun owner ditandai perlu revisi.');
    }

    private function ensureUserRequest(VerificationRequest $verificationRequest): void
    {
        if (! $verificationRequest->verifiable instanceof User
            || ! $verificationRequest->verifiable->hasRole('owner')) {
            abort(404);
        }
    }
}