<x-app-layout :title="$umkm->name . ' — Detail UMKM'">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0Z" />
                    <circle cx="12" cy="10" r="3" />
                </svg>
            </div>
            <h1 class="font-semibold text-xl text-slate-900 leading-tight">{{ $umkm->name }}</h1>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="container-page">
            @if (session('status'))
                <x-alert type="success" class="mb-4">{{ session('status') }}</x-alert>
            @endif
            @if (session('error'))
                <x-alert type="error" class="mb-4">{{ session('error') }}</x-alert>
            @endif

            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('admin.umkms.index') }}" class="inline-flex min-h-11 items-center gap-2 text-sm font-medium text-[#8A7464] transition-colors duration-150 hover:text-[#5C4033]">
                    <span aria-hidden="true">&larr;</span>
                    Kembali ke Daftar UMKM
                </a>
                @php
                    $statusLabels = [
                        'draft' => 'Draft',
                        'pending' => 'Menunggu Pemeriksaan',
                        'approved' => 'Disetujui',
                        'needs_revision' => 'Perlu Revisi',
                        'rejected' => 'Ditolak',
                    ];
                    $statusStyles = [
                        'draft' => 'bg-slate-100 text-slate-700',
                        'pending' => 'bg-amber-100 text-amber-800',
                        'approved' => 'bg-emerald-100 text-emerald-800',
                        'needs_revision' => 'bg-orange-100 text-orange-800',
                        'rejected' => 'bg-red-100 text-red-700',
                    ];
                @endphp
                <span class="inline-flex w-fit items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusStyles[$umkm->status] ?? 'bg-slate-100 text-slate-700' }}">
                    {{ $statusLabels[$umkm->status] ?? $umkm->status }}
                </span>
            </div>

            @if ($umkm->status === 'approved')
                <div class="mt-4">
                    <a href="{{ route('public.umkm.show', $umkm) }}" target="_blank" rel="noopener" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        Lihat Halaman Publik
                    </a>
                </div>
            @endif

            <div class="mt-6 rounded-2xl bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Informasi UMKM</h2>
                            <p class="mt-0.5 text-xs text-slate-500">Data utama dan profil usaha.</p>
                        </div>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
                        <div>
                            <dt class="font-medium text-slate-500">Nama</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $umkm->name }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Kategori</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $umkm->category?->name ?? '—' }}</dd>
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
                            <dt class="font-medium text-slate-500">Jam Operasional</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $umkm->operational_hours ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="mt-6 rounded-2xl bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Kontak & Media Sosial</h2>
                            <p class="mt-0.5 text-xs text-slate-500">Informasi komunikasi yang ditampilkan di portal.</p>
                        </div>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
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
                    </dl>
                </div>
            </div>

            <div class="mt-6 rounded-2xl bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                <circle cx="8.5" cy="8.5" r="1.5" />
                                <polyline points="21 15 16 10 5 21" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Media UMKM</h2>
                            <p class="mt-0.5 text-xs text-slate-500">Kelola logo, banner, dan galeri untuk halaman publik. Format JPG, PNG, atau WEBP, maksimal 2 MB per file.</p>
                        </div>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    @php
                        $logo = $umkm->media->firstWhere('collection', 'logo');
                        $banner = $umkm->media->firstWhere('collection', 'banner');
                        $gallery = $umkm->media->where('collection', 'gallery')->sortBy('sort_order')->values();
                    @endphp

                    <div class="space-y-5">
                        <x-media-upload
                            title="Logo"
                            description="Logo usaha. Tampil sebagai identitas UMKM di halaman publik."
                            :current="$logo"
                            :store-url="route('admin.umkms.media.store', [$umkm, 'logo'])"
                            input-name="file_logo"
                            :delete-url="$logo ? route('admin.media.destroy', $logo) : null"
                            item-label="logo"
                            optional
                            preview-class="aspect-square w-32"
                        />

                        <x-media-upload
                            title="Banner"
                            description="Banner tampilan atas halaman UMKM."
                            :current="$banner"
                            :store-url="route('admin.umkms.media.store', [$umkm, 'banner'])"
                            input-name="file_banner"
                            :delete-url="$banner ? route('admin.media.destroy', $banner) : null"
                            item-label="banner"
                            optional
                            preview-class="aspect-[3/1] w-full"
                        />

                        <div class="rounded-xl border border-slate-200 p-4">
                            <div class="flex items-baseline justify-between gap-3">
                                <h3 class="text-sm font-semibold text-slate-900">Galeri</h3>
                                <span class="shrink-0 text-xs font-medium text-slate-400">Opsional</span>
                            </div>
                            <p class="mt-1 text-sm text-slate-600">Kumpulan foto usaha. Maksimal 5 gambar dalam satu unggahan.</p>

                            <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                @forelse ($gallery as $item)
                                    <div class="rounded-lg border border-slate-200 p-2">
                                        <img src="{{ Storage::disk($item->disk)->url($item->path) }}" alt="Galeri {{ $umkm->name }}" class="aspect-[4/3] w-full rounded object-cover">
                                        <form method="POST" action="{{ route('admin.media.destroy', $item) }}" class="mt-2" onsubmit="return confirm('Hapus foto galeri ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button class="w-full justify-center">{{ __('Hapus') }}</x-danger-button>
                                        </form>
                                    </div>
                                @empty
                                    <p class="col-span-full mt-0 text-sm text-slate-500">Belum ada foto galeri.</p>
                                @endforelse
                            </div>

                            <form method="POST" action="{{ route('admin.umkms.media.store', [$umkm, 'gallery']) }}" enctype="multipart/form-data" class="mt-4">
                                @csrf
                                <label for="gallery" class="block text-sm font-medium text-slate-700">{{ __('Pilih Foto Galeri') }}</label>
                                <input id="gallery" type="file" name="gallery[]" multiple accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full text-sm text-slate-700 file:me-3 file:min-h-11 file:rounded-xl file:border-0 file:bg-[#C26A4A] file:px-4 file:text-sm file:font-semibold file:text-white hover:file:bg-[#A8563A]">
                                <x-input-error :messages="$errors->get('gallery')" class="mt-2" />
                                <x-input-error :messages="$errors->get('gallery.*')" class="mt-2" />
                                <button type="submit" class="mt-3 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#C26A4A] px-5 text-sm font-semibold text-white transition duration-300 hover:bg-[#A8563A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                    {{ __('Unggah Galeri') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 rounded-2xl bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
                                <path d="M3 6h18" />
                                <path d="M16 10a4 4 0 0 1-8 0" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Produk UMKM ini</h2>
                            <p class="mt-0.5 text-xs text-slate-500">Produk yang terdaftar untuk UMKM ini.</p>
                        </div>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    @if ($umkm->products->isNotEmpty())
                        <ul class="overflow-hidden rounded-xl border border-slate-200">
                            @foreach ($umkm->products as $product)
                                @php
                                    $statusLabels = [
                                        'draft' => 'Draft',
                                        'pending' => 'Menunggu Pemeriksaan',
                                        'approved' => 'Disetujui',
                                        'needs_revision' => 'Perlu Revisi',
                                        'rejected' => 'Ditolak',
                                    ];
                                    $statusStyles = [
                                        'draft' => 'bg-slate-100 text-slate-700',
                                        'pending' => 'bg-amber-100 text-amber-800',
                                        'approved' => 'bg-emerald-100 text-emerald-800',
                                        'needs_revision' => 'bg-orange-100 text-orange-800',
                                        'rejected' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <li class="min-w-0 border-b border-slate-100 px-5 py-4 last:border-b-0">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="min-w-0">
                                            <p class="break-words text-base font-medium text-slate-900">{{ $product->name }}</p>
                                            <dl class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-sm leading-relaxed text-slate-500">
                                                @if ($product->category)
                                                    <div class="min-w-0">
                                                        <dt class="inline font-medium text-slate-400">Kategori:</dt>
                                                        <dd class="inline break-words">{{ $product->category->name }}</dd>
                                                    </div>
                                                @endif
                                                <div class="min-w-0">
                                                    <dt class="inline font-medium text-slate-400">Harga:</dt>
                                                    <dd class="inline break-words">Rp{{ number_format((float) $product->price, 0, ',', '.') }}</dd>
                                                </div>
                                            </dl>
                                        </div>

                                        <div class="flex shrink-0 flex-col items-start gap-2 sm:flex-row sm:items-center">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusStyles[$product->status] ?? 'bg-slate-100 text-slate-700' }}">
                                                {{ $statusLabels[$product->status] ?? $product->status }}
                                            </span>
                                            <a href="{{ route('admin.products.show', $product) }}" class="inline-flex min-h-10 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-[#C26A4A] transition duration-300 hover:border-[#C26A4A] hover:bg-[#FAF8F5] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                                Kelola
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="rounded-xl border border-slate-200 px-5 py-6">
                            <p class="text-sm leading-relaxed text-slate-600">
                                Belum ada produk untuk UMKM ini.
                                @if ($umkm->status === 'approved')
                                    Tambahkan produk pertama untuk mulai menampilkan katalog usaha di halaman publik.
                                @else
                                    Produk dapat ditambahkan setelah UMKM berstatus Disetujui.
                                @endif
                            </p>
                            @if ($umkm->status === 'approved')
                                <a href="{{ route('admin.products.create', ['umkm' => $umkm->id]) }}" class="mt-4 inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-[#C26A4A] transition duration-300 hover:border-[#C26A4A] hover:bg-[#FAF8F5] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                    Tambah Produk
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-6 rounded-2xl bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Pemilik</h2>
                            <p class="mt-0.5 text-xs text-slate-500">Akun pengguna yang menjadi pemilik UMKM ini.</p>
                        </div>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
                        <div>
                            <dt class="font-medium text-slate-500">Nama</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $umkm->user?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Email</dt>
                            <dd class="mt-0.5 text-slate-900 break-all">{{ $umkm->user?->email ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="mt-6 rounded-2xl bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-red-50 text-red-600" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6" />
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2 2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Hapus UMKM</h2>
                            <p class="mt-0.5 text-xs text-slate-500">Hapus UMKM beserta seluruh produk, media, dan pengajuan verifikasi terkait secara permanen.</p>
                        </div>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    <form method="POST" action="{{ route('admin.umkms.destroy', $umkm) }}" onsubmit="return confirm('Yakin ingin menghapus UMKM {{ $umkm->name }}? Seluruh produk, media, dan pengajuan verifikasi terkait juga akan dihapus permanen.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-red-600 px-5 text-sm font-semibold text-white transition duration-300 hover:bg-red-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">
                            Hapus UMKM
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
