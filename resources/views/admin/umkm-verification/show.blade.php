<x-app-layout :title="$umkm->name . ' — Verifikasi UMKM'">
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ $umkm->name }}
        </h1>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <x-alert type="success" class="mb-4">{{ session('status') }}</x-alert>
            @endif

            @if (session('error'))
                <x-alert type="error" class="mb-4">{{ session('error') }}</x-alert>
            @endif

            <a href="{{ route('admin.umkm.verification.index') }}" class="inline-flex min-h-11 items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900">
                <span aria-hidden="true">&larr;</span>
                {{ __('Kembali ke Antrean') }}
            </a>

            @if ($umkm->status === 'approved')
                <a href="{{ route('public.umkm.show', $umkm) }}" target="_blank" rel="noopener" class="mt-4 inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50">
                    Lihat Halaman Publik
                </a>
            @endif

            <div class="mt-4 card">
                <div class="p-6">
                    <h2 class="font-semibold text-slate-900">Informasi UMKM</h2>
                    <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
                        <div>
                            <dt class="font-medium text-slate-500">Nama</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $umkm->name }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Kategori</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $umkm->category?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Status</dt>
                            <dd class="mt-0.5 text-slate-900">{{ match ($umkm->status) {
                                'draft' => 'Draft',
                                'pending' => 'Menunggu Pemeriksaan',
                                'approved' => 'Disetujui',
                                'needs_revision' => 'Perlu Revisi',
                                'rejected' => 'Ditolak',
                                default => $umkm->status,
                            } }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Diajukan</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $request->created_at->format('d M Y H:i') }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="font-medium text-slate-500">Deskripsi</dt>
                            <dd class="mt-0.5 text-slate-900 whitespace-pre-line">{{ $umkm->description ?? '—' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="font-medium text-slate-500">Alamat</dt>
                            <dd class="mt-0.5 text-slate-900 whitespace-pre-line">{{ $umkm->address ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Google Maps</dt>
                            <dd class="mt-0.5 text-slate-900 break-all">{{ $umkm->google_maps ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Nomor Telepon</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $umkm->phone ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Email</dt>
                            <dd class="mt-0.5 text-slate-900 break-all">{{ $umkm->email ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Website</dt>
                            <dd class="mt-0.5 text-slate-900 break-all">{{ $umkm->website ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Instagram</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $umkm->instagram ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Facebook</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $umkm->facebook ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">TikTok</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $umkm->tiktok ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Jam Operasional</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $umkm->operational_hours ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="mt-6 card">
                <div class="p-6">
                    <h2 class="font-semibold text-slate-900">Media UMKM</h2>
                    <p class="mt-1 text-sm text-slate-600">
                        Media yang diunggah pemilik untuk ditampilkan di halaman publik.
                    </p>

                    @php
                        $logo = $umkm->media->firstWhere('collection', 'logo');
                        $banner = $umkm->media->firstWhere('collection', 'banner');
                        $gallery = $umkm->media->where('collection', 'gallery')->sortBy('sort_order')->values();
                    @endphp

                    @if ($logo === null && $banner === null && $gallery->isEmpty())
                        <p class="mt-3 text-sm text-slate-500">
                            Belum ada media yang diunggah. Tanpa logo dan banner, profil UMKM tampil kurang lengkap di halaman publik.
                        </p>
                    @else
                        <div class="mt-3 space-y-4">
                            @if ($logo)
                                <div>
                                    <p class="text-sm font-medium text-slate-700">Logo</p>
                                    <img src="{{ Storage::disk($logo->disk)->url($logo->path) }}" alt="Logo {{ $umkm->name }}" class="mt-1 h-24 w-24 rounded-lg border border-slate-200 object-cover">
                                </div>
                            @endif

                            @if ($banner)
                                <div>
                                    <p class="text-sm font-medium text-slate-700">Banner</p>
                                    <img src="{{ Storage::disk($banner->disk)->url($banner->path) }}" alt="Banner {{ $umkm->name }}" class="mt-1 aspect-[3/1] w-full max-w-xl rounded-lg border border-slate-200 object-cover">
                                </div>
                            @endif

                            @if ($gallery->isNotEmpty())
                                <div>
                                    <p class="text-sm font-medium text-slate-700">Galeri ({{ $gallery->count() }})</p>
                                    <div class="mt-1 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                        @foreach ($gallery as $item)
                                            <img src="{{ Storage::disk($item->disk)->url($item->path) }}" alt="Galeri {{ $umkm->name }}" class="aspect-[4/3] w-full rounded-lg border border-slate-200 object-cover">
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-6 card">
                <div class="p-6">
                    <h2 class="font-semibold text-slate-900">Pemilik</h2>
                    <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
                        <div>
                            <dt class="font-medium text-slate-500">Nama</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $request->user?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Email</dt>
                            <dd class="mt-0.5 text-slate-900 break-all">{{ $request->user?->email ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            @if ($request->status === 'pending')
                <div class="mt-6 card">
                    <div class="p-6">
                        <h2 class="font-semibold text-slate-900">Tindakan Pemeriksaan</h2>
                        <p class="mt-1 text-sm text-slate-600">
                            Setujui jika data lengkap dan sesuai. Gunakan Perlu Revisi untuk perbaikan kecil, atau Tolak jika pengajuan tidak memenuhi ketentuan.
                        </p>

                        <x-verification-actions
                            :approve-url="route('admin.umkm.verification.approve', $request)"
                            :reject-url="route('admin.umkm.verification.reject', $request)"
                            :revision-url="route('admin.umkm.verification.needs-revision', $request)"
                            subject="pengajuan UMKM ini"
                        />
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
