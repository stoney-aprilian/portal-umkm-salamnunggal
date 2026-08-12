<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreUmkmRequest;
use App\Http\Requests\Owner\UpdateUmkmRequest;
use App\Models\Category;
use App\Models\Umkm;
use App\Support\VerificationActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UmkmController extends Controller
{
    public function create(): View
    {
        $this->authorize('create', Umkm::class);

        return view('owner.umkm.create', [
            'categories' => Category::where('type', 'umkm')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreUmkmRequest $request): RedirectResponse
    {
        $this->authorize('create', Umkm::class);

        Umkm::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->integer('category_id'),
            'name' => $request->string('name')->toString(),
            'slug' => Umkm::generateUniqueSlug($request->string('name')->toString()),
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

        return redirect()->route('dashboard')->with('status', 'Draft UMKM berhasil disimpan.');
    }

    public function submit(Umkm $umkm, Request $request): RedirectResponse
    {
        $this->authorize('submit', $umkm);

        $submitted = DB::transaction(function () use ($umkm, $request) {
            $affected = $umkm->whereKey($umkm->id)->where('status', 'draft')->update(['status' => 'pending']);

            if ($affected === 0) {
                return false;
            }

            $umkm->verificationRequests()->create([
                'user_id' => $request->user()->id,
                'reviewer_id' => null,
                'status' => 'pending',
                'notes' => null,
                'reviewed_at' => null,
            ]);

            VerificationActivity::log('submitted', $umkm, $request->user());

            return true;
        });

        if (! $submitted) {
            return redirect()->back()
                ->with('error', 'Pengajuan UMKM hanya dapat dikirim untuk UMKM dengan status draft.');
        }

        return redirect()->route('dashboard')
            ->with('status', 'Pengajuan UMKM berhasil dikirim dan sedang menunggu pemeriksaan.');
    }

    public function edit(Umkm $umkm): View|RedirectResponse
    {
        $this->authorize('update', $umkm);

        if ($redirect = $this->ensureRevising($umkm)) {
            return $redirect;
        }

        $umkm->load('media');

        return view('owner.umkm.edit', [
            'umkm' => $umkm,
            'categories' => Category::where('type', 'umkm')->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateUmkmRequest $request, Umkm $umkm): RedirectResponse
    {
        $this->authorize('update', $umkm);

        if ($redirect = $this->ensureRevising($umkm)) {
            return $redirect;
        }

        $umkm->update([
            'name' => $request->string('name')->toString(),
            'slug' => Umkm::generateUniqueSlug($request->string('name')->toString(), $umkm->id),
            'category_id' => $request->integer('category_id'),
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

        return redirect()->route('dashboard')
            ->with('status', 'Perubahan UMKM berhasil disimpan.');
    }

    private function ensureRevising(Umkm $umkm): ?RedirectResponse
    {
        if (! in_array($umkm->status, ['draft', 'needs_revision', 'rejected'], true)) {
            return redirect()->back()
                ->with('error', 'UMKM hanya dapat diubah ketika berstatus draft, membutuhkan revisi, atau ditolak.');
        }

        return null;
    }
}
