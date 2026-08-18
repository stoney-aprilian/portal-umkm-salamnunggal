<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreUmkmRequest;
use App\Http\Requests\Owner\UpdateUmkmRequest;
use App\Models\Category;
use App\Models\Umkm;
use App\Models\UmkmRevision;
use App\Support\VerificationActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Lets an owner propose changes to an approved UMKM through a working
 * copy revision. The approved UMKM stays public and unchanged until an
 * administrator approves the revision.
 */
class UmkmRevisionController extends Controller
{
    private const ACTIVE_STATUSES = ['draft', 'pending', 'needs_revision', 'rejected'];

    private const EDITABLE_STATUSES = ['draft', 'needs_revision', 'rejected'];

    public function create(Umkm $umkm): View|RedirectResponse
    {
        $this->authorize('create', [UmkmRevision::class, $umkm]);

        if ($redirect = $this->ensureUmkmApproved($umkm)) {
            return $redirect;
        }

        if ($redirect = $this->ensureNoActiveRevision($umkm)) {
            return $redirect;
        }

        return view('owner.umkm-revisions.create', [
            'umkm' => $umkm,
            'categories' => Category::where('type', 'umkm')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreUmkmRequest $request, Umkm $umkm): RedirectResponse
    {
        $this->authorize('create', [UmkmRevision::class, $umkm]);

        if ($redirect = $this->ensureUmkmApproved($umkm)) {
            return $redirect;
        }

        if ($redirect = $this->ensureNoActiveRevision($umkm)) {
            return $redirect;
        }

        $revision = $umkm->revisions()->create([
            'category_id' => $request->integer('category_id'),
            'name' => $request->string('name')->toString(),
            'description' => $request->input('description'),
            'address' => $request->input('address'),
            'google_maps' => $request->input('google_maps'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'website' => $request->input('website'),
            'instagram' => $request->input('instagram'),
            'facebook' => $request->input('facebook'),
            'tiktok' => $request->input('tiktok'),
            'operational_hours' => $request->input('operational_hours'),
            'status' => 'draft',
        ]);

        return redirect()->route('owner.umkm.revisions.edit', $revision)
            ->with('status', 'Pengajuan perubahan UMKM berhasil disimpan sebagai draft. Kelola media dan kirim pengajuan agar diperiksa Administrator.');
    }

    public function edit(UmkmRevision $revision): View|RedirectResponse
    {
        $this->authorize('update', $revision);

        if ($redirect = $this->ensureRevising($revision)) {
            return $redirect;
        }

        $revision->load('media');

        return view('owner.umkm-revisions.edit', [
            'revision' => $revision,
            'umkm' => $revision->umkm,
            'categories' => Category::where('type', 'umkm')->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateUmkmRequest $request, UmkmRevision $revision): RedirectResponse
    {
        $this->authorize('update', $revision);

        if ($redirect = $this->ensureRevising($revision)) {
            return $redirect;
        }

        $revision->update([
            'category_id' => $request->integer('category_id'),
            'name' => $request->string('name')->toString(),
            'description' => $request->input('description'),
            'address' => $request->input('address'),
            'google_maps' => $request->input('google_maps'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'website' => $request->input('website'),
            'instagram' => $request->input('instagram'),
            'facebook' => $request->input('facebook'),
            'tiktok' => $request->input('tiktok'),
            'operational_hours' => $request->input('operational_hours'),
            'status' => 'draft',
        ]);

        return redirect()->route('owner.umkm.revisions.edit', $revision->fresh())
            ->with('status', 'Perubahan UMKM berhasil disimpan.');
    }

    public function submit(Request $request, UmkmRevision $revision): RedirectResponse
    {
        $this->authorize('submit', $revision);

        $submitted = DB::transaction(function () use ($request, $revision) {
            $affected = $revision->whereKey($revision->id)
                ->where('status', 'draft')
                ->update(['status' => 'pending']);

            if ($affected === 0) {
                return false;
            }

            $revision->verificationRequests()->create([
                'user_id' => $request->user()->id,
                'reviewer_id' => null,
                'status' => 'pending',
                'notes' => null,
                'reviewed_at' => null,
            ]);

            VerificationActivity::log('submitted', $revision, $request->user());

            return true;
        });

        if (! $submitted) {
            return redirect()->back()
                ->with('error', 'Perubahan hanya dapat dikirim untuk perubahan yang masih berstatus draft.');
        }

        return redirect()->route('dashboard')
            ->with('status', 'Pengajuan perubahan UMKM berhasil dikirim dan sedang menunggu pemeriksaan. Data yang tampil di publik tidak berubah sampai perubahan disetujui.');
    }

    private function ensureUmkmApproved(Umkm $umkm): ?RedirectResponse
    {
        if ($umkm->status !== 'approved') {
            return redirect()->route('dashboard')
                ->with('error', 'Perubahan hanya dapat diajukan untuk UMKM yang telah disetujui.');
        }

        return null;
    }

    private function ensureNoActiveRevision(Umkm $umkm): ?RedirectResponse
    {
        $active = $umkm->revisions()
            ->whereIn('status', self::ACTIVE_STATUSES)
            ->latest('id')
            ->first();

        if ($active === null) {
            return null;
        }

        if (in_array($active->status, self::EDITABLE_STATUSES, true)) {
            return redirect()->route('owner.umkm.revisions.edit', $active);
        }

        return redirect()->route('dashboard')
            ->with('error', 'Anda sudah memiliki pengajuan perubahan UMKM yang sedang diperiksa.');
    }

    private function ensureRevising(UmkmRevision $revision): ?RedirectResponse
    {
        if (! in_array($revision->status, self::EDITABLE_STATUSES, true)) {
            return redirect()->route('dashboard')
                ->with('error', 'Perubahan UMKM hanya dapat diubah ketika berstatus draft, membutuhkan revisi, atau ditolak.');
        }

        return null;
    }
}