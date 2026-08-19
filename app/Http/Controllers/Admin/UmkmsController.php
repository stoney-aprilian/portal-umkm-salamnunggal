<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUmkmRequest;
use App\Http\Requests\Admin\UpdateUmkmRequest;
use App\Models\Category;
use App\Models\Umkm;
use App\Models\User;
use App\Support\UmkmManagementActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Administrator CRUD for UMKM (assisted service). UMKM created here are
 * owned by the selected owner (never the administrator) and are published
 * directly with status "approved", matching administrator authority.
 */
class UmkmsController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Umkm::class);

        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->trim()->toString();
        $category = $request->string('category')->trim()->toString();
        $sort = $request->string('sort')->trim()->toString();

        $baseQuery = Umkm::query()->with(['user', 'category']);

        $totalCount = (clone $baseQuery)->count();
        $approvedCount = (clone $baseQuery)->where('status', 'approved')->count();
        $pendingCount = (clone $baseQuery)->where('status', 'pending')->count();
        $rejectedCount = (clone $baseQuery)->whereIn('status', ['rejected', 'needs_revision'])->count();

        $query = $baseQuery;

        if ($search !== '') {
            $term = mb_strtolower($search);
            $query->where(function ($q) use ($term): void {
                $q->whereRaw('LOWER(name) LIKE ?', ['%'.$term.'%'])
                    ->orWhereHas('user', function ($userQuery) use ($term): void {
                        $userQuery->whereRaw('LOWER(name) LIKE ?', ['%'.$term.'%']);
                    });
            });
        }

        if ($status !== '' && in_array($status, ['draft', 'pending', 'approved', 'needs_revision', 'rejected'], true)) {
            $query->where('status', $status);
        }

        if ($category !== '' && Category::where('type', 'umkm')->where('id', $category)->exists()) {
            $query->where('category_id', $category);
        }

        $sort = match ($sort) {
            'oldest' => 'asc',
            'name_asc' => 'name_asc',
            'name_desc' => 'name_desc',
            default => 'desc',
        };

        if ($sort === 'name_asc') {
            $query->orderBy('name', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('name', 'desc');
        } else {
            $query->orderBy('created_at', $sort);
        }

        $umkms = $query->get();

        return view('admin.umkms.index', [
            'umkms' => $umkms,
            'totalCount' => $totalCount,
            'approvedCount' => $approvedCount,
            'pendingCount' => $pendingCount,
            'rejectedCount' => $rejectedCount,
            'search' => $search,
            'status' => $status,
            'category' => $category,
            'sort' => $sort,
            'categories' => Category::where('type', 'umkm')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Umkm::class);

        return view('admin.umkms.create', [
            'owners' => User::role('owner')->doesntHave('umkm')->orderBy('name')->get(),
            'categories' => Category::where('type', 'umkm')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreUmkmRequest $request): RedirectResponse
    {
        $this->authorize('create', Umkm::class);

        $umkm = DB::transaction(function () use ($request) {
            $umkm = Umkm::create([
                'user_id' => $request->integer('owner_id'),
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
                'status' => 'approved',
            ]);

            UmkmManagementActivity::log('umkm_created', $umkm, $request->user());

            return $umkm;
        });

        return redirect()->route('admin.umkms.show', $umkm)
            ->with('status', 'UMKM berhasil dibuat atas nama pemilik dan langsung tampil di portal.');
    }

    public function show(Umkm $umkm): View
    {
        $this->authorize('view', $umkm);

        $umkm->load(['user', 'category', 'media', 'products.category']);

        return view('admin.umkms.show', ['umkm' => $umkm]);
    }

    public function edit(Umkm $umkm): View
    {
        $this->authorize('update', $umkm);

        $umkm->load('user');

        return view('admin.umkms.edit', [
            'umkm' => $umkm,
            'owners' => User::role('owner')->orderBy('name')->get(),
            'categories' => Category::where('type', 'umkm')->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateUmkmRequest $request, Umkm $umkm): RedirectResponse
    {
        $this->authorize('update', $umkm);

        DB::transaction(function () use ($request, $umkm) {
            $umkm->update([
                'user_id' => $request->integer('owner_id'),
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
            ]);

            UmkmManagementActivity::log('umkm_updated', $umkm, $request->user());
        });

        return redirect()->route('admin.umkms.show', $umkm)
            ->with('status', 'Perubahan UMKM berhasil disimpan.');
    }

    public function destroy(Request $request, Umkm $umkm): RedirectResponse
    {
        $this->authorize('delete', $umkm);

        DB::transaction(function () use ($request, $umkm) {
            foreach ($umkm->products()->with('media', 'revisions.media')->get() as $product) {
                foreach ($product->revisions as $revision) {
                    foreach ($revision->media as $media) {
                        Storage::disk($media->disk)->delete($media->path);
                        $media->delete();
                    }

                    $revision->verificationRequests()->delete();
                    $revision->delete();
                }

                foreach ($product->media as $media) {
                    Storage::disk($media->disk)->delete($media->path);
                    $media->delete();
                }

                $product->verificationRequests()->delete();
                $product->delete();
            }

            foreach ($umkm->revisions()->with('media')->get() as $revision) {
                foreach ($revision->media as $media) {
                    Storage::disk($media->disk)->delete($media->path);
                    $media->delete();
                }

                $revision->verificationRequests()->delete();
                $revision->delete();
            }

            foreach ($umkm->media as $media) {
                Storage::disk($media->disk)->delete($media->path);
                $media->delete();
            }

            $umkm->verificationRequests()->delete();

            UmkmManagementActivity::log('umkm_deleted', $umkm, $request->user());
            $umkm->delete();
        });

        return redirect()->route('admin.umkms.index')
            ->with('status', 'UMKM beserta produk dan medianya berhasil dihapus.');
    }
}