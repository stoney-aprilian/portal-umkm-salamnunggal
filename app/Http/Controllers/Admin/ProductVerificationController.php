<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectVerificationRequest;
use App\Http\Requests\Admin\RevisionVerificationRequest;
use App\Models\Product;
use App\Models\VerificationRequest;
use App\Support\VerificationActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductVerificationController extends Controller
{
    public function index(): View
    {
        $requests = VerificationRequest::query()
            ->with(['verifiable.category', 'verifiable.umkm', 'user'])
            ->where('verifiable_type', Product::class)
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.product-verification.index', [
            'requests' => $requests,
        ]);
    }

    public function show(VerificationRequest $verificationRequest): View
    {
        $this->authorize('review', $verificationRequest);
        $this->ensureProductRequest($verificationRequest);

        $verificationRequest->load(['verifiable.category', 'verifiable.umkm', 'user']);

        return view('admin.product-verification.show', [
            'request' => $verificationRequest,
            'product' => $verificationRequest->verifiable,
        ]);
    }

    public function approve(Request $request, VerificationRequest $verificationRequest): RedirectResponse
    {
        $this->authorize('review', $verificationRequest);
        $this->ensureProductRequest($verificationRequest);

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

            $verificationRequest->verifiable->update(['status' => 'approved']);

            VerificationActivity::log('approved', $verificationRequest->verifiable, $request->user());

            return true;
        });

        if (! $approved) {
            return redirect()->back()
                ->with('error', 'Pengajuan ini sudah diperiksa dan tidak dapat diubah lagi.');
        }

        return redirect()->route('admin.products.verification.index')
            ->with('status', 'Pengajuan produk berhasil disetujui.');
    }

    public function reject(RejectVerificationRequest $request, VerificationRequest $verificationRequest): RedirectResponse
    {
        $this->authorize('review', $verificationRequest);
        $this->ensureProductRequest($verificationRequest);

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

        return redirect()->route('admin.products.verification.index')
            ->with('status', 'Pengajuan produk ditolak.');
    }

    public function needsRevision(RevisionVerificationRequest $request, VerificationRequest $verificationRequest): RedirectResponse
    {
        $this->authorize('review', $verificationRequest);
        $this->ensureProductRequest($verificationRequest);

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

        return redirect()->route('admin.products.verification.index')
            ->with('status', 'Pengajuan produk ditandai perlu revisi.');
    }

    private function ensureProductRequest(VerificationRequest $verificationRequest): void
    {
        if (! $verificationRequest->verifiable instanceof Product) {
            abort(404);
        }
    }
}
