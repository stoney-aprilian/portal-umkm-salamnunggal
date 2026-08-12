<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use Illuminate\View\View;

class UmkmController extends Controller
{
    public function index(): View
    {
        $umkms = Umkm::query()
            ->with([
                'category',
                'media' => fn ($query) => $query->where('collection', 'logo'),
            ])
            ->where('status', 'approved')
            ->latest()
            ->get();

        return view('public.umkms.index', ['umkms' => $umkms]);
    }

    public function show(Umkm $umkm): View
    {
        abort_unless($umkm->status === 'approved', 404);

        $umkm->load([
            'category',
            'media' => fn ($query) => $query->orderBy('sort_order'),
            'products' => fn ($query) => $query
                ->where('status', 'approved')
                ->with(['category', 'media' => fn ($mediaQuery) => $mediaQuery->where('collection', 'product')])
                ->latest(),
        ]);

        $similarUmkms = collect();

        if ($umkm->category) {
            $similarUmkms = Umkm::query()
                ->with([
                    'category',
                    'media' => fn ($query) => $query->where('collection', 'logo'),
                ])
                ->where('status', 'approved')
                ->where('category_id', $umkm->category_id)
                ->whereKeyNot($umkm->id)
                ->latest()
                ->limit(4)
                ->get();
        }

        return view('public.umkms.show', ['umkm' => $umkm, 'similarUmkms' => $similarUmkms]);
    }
}
