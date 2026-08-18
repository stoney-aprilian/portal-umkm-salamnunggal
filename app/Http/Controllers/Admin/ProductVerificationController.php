<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectVerificationRequest;
use App\Http\Requests\Admin\RevisionVerificationRequest;
use App\Models\Product;
use App\Models\ProductRevision;
use App\Models\VerificationRequest;
use App\Support\VerificationActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Reviews both initial product submissions (verifiable = Product) and
 * change submissions (verifiable = ProductRevision). Approving a change
 * copies the revision onto the approved product without touching its
 * ownership, then archives the revision so the verification history stays
 * intact.
 */
class ProductVerificationController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $baseQuery = VerificationRequest::query()
            ->whereIn('verifiable_type', [Product::class, ProductRevision::class]);

        $pendingCount = (clone $baseQuery)->where('status', 'pending')->count();
        $approvedCount = (clone $baseQuery)->where('status', 'approved')->count();
        $rejectedCount = (clone $baseQuery)->where('status', 'rejected')->count();
        $totalCount = (clone $baseQuery)->count();

        $requests = $baseQuery
            ->where('status', 'pending')
            ->with([
                'verifiable.category',
                'user',
                'verifiable' => fn ($morph) => $morph->morphWith([
                    Product::class => ['umkm', 'media'],
                    ProductRevision::class => ['product.umkm', 'media'],
                ]),
            ])
            ->orderByDesc('created_at')
            ->get()
            ->filter(function (VerificationRequest $vr) use ($search): bool {
                if ($search === '') {
                    return true;
                }

                $term = mb_strtolower($search);

                $productName = $vr->verifiable->name ?? '';
                $umkmName = match ($vr->verifiable_type) {
                    ProductRevision::class => $vr->verifiable->product->umkm?->name ?? '',
                    Product::class => $vr->verifiable->umkm?->name ?? '',
                    default => '',
                };

                return str_contains(mb_strtolower($productName), $term)
                    || str_contains(mb_strtolower($umkmName), $term);
            });

        return view('admin.product-verification.index', [
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
        $this->ensureProductRequest($verificationRequest);

        $verificationRequest->load(['verifiable.category', 'user']);

        return view('admin.product-verification.show', [
            'request' => $verificationRequest,
            'product' => $verificationRequest->verifiable,
            'current' => $verificationRequest->verifiable instanceof ProductRevision
                ? $verificationRequest->verifiable->load('media')->product
                : null,
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

            $verifiable = $verificationRequest->verifiable;

            if ($verifiable instanceof ProductRevision) {
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

    /**
     * Copies the approved change onto the approved product, replaces the
     * public photo, and archives the revision. Ownership never changes.
     */
    private function applyChange(ProductRevision $revision): void
    {
        $product = $revision->product;

        $product->update([
            'category_id' => $revision->category_id,
            'name' => $revision->name,
            'slug' => Product::generateUniqueSlug($revision->name, $product->id),
            'description' => $revision->description,
            'price' => $revision->price,
        ]);

        foreach ($revision->media as $media) {
            $old = $product->media()->where('collection', 'product')->first();

            if ($old !== null) {
                Storage::disk($old->disk)->delete($old->path);
                $old->delete();
            }

            $media->update([
                'mediable_type' => Product::class,
                'mediable_id' => $product->id,
                'sort_order' => 0,
            ]);
        }

        $revision->update(['status' => 'approved']);
    }

    private function ensureProductRequest(VerificationRequest $verificationRequest): void
    {
        if (! $verificationRequest->verifiable instanceof Product
            && ! $verificationRequest->verifiable instanceof ProductRevision) {
            abort(404);
        }
    }
}