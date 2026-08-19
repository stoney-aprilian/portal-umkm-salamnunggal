<x-app-layout :title="$product->name . ' — Detail Produk'">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
                    <path d="M3 6h18" />
                    <path d="M16 10a4 4 0 0 1-8 0" />
                </svg>
            </div>
            <h1 class="font-semibold text-xl text-slate-900 leading-tight">{{ $product->name }}</h1>
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
                <a href="{{ route('admin.products.index') }}" class="inline-flex min-h-11 items-center gap-2 text-sm font-medium text-[#8A7464] transition-colors duration-150 hover:text-[#5C4033] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                    <span aria-hidden="true">&larr;</span>
                    Kembali ke Daftar Produk
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
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex w-fit items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusStyles[$product->status] ?? 'bg-slate-100 text-slate-700' }}">
                        {{ $statusLabels[$product->status] ?? $product->status }}
                    </span>
                    @if ($product->is_featured)
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                            </svg>
                            Unggulan
                        </span>
                    @endif
                    <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        Edit Produk
                    </a>
                </div>
            </div>
            </div>

            @if ($product->status === 'approved' && $product->umkm?->status === 'approved')
                <div class="mt-4">
                    <a href="{{ route('public.product.show', $product) }}" target="_blank" rel="noopener" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        Lihat Halaman Publik
                    </a>
                </div>
            @endif

            <div class="mt-6 rounded-2xl bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#C26A4A]" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Status Unggulan</h2>
                            <p class="mt-0.5 text-xs text-slate-500">Tentukan apakah produk ini ditampilkan di section produk unggulan di halaman depan.</p>
                        </div>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    @if ($product->is_featured)
                        <form method="POST" action="{{ route('admin.products.unfeature', $product) }}" onsubmit="event.preventDefault(); confirmAction(this, 'Hapus dari Unggulan?', 'Produk {{ $product->name }} akan dihapus dari daftar unggulan dan tidak lagi ditampilkan di section produk unggulan homepage.', 'warning', 'Hapus dari Unggulan', 'Batal');">
                            @csrf
                            @method('POST')
                            <p class="text-sm text-slate-600">Produk ini saat ini merupakan <strong>Produk Unggulan</strong> dan ditampilkan di section unggulan homepage.</p>
                            <button type="submit" class="mt-4 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                Hapus dari Unggulan
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.products.feature', $product) }}" onsubmit="event.preventDefault(); confirmAction(this, 'Jadikan Unggulan?', 'Produk {{ $product->name }} akan ditampilkan di section produk unggulan homepage.', 'success', 'Jadikan Unggulan', 'Batal');">
                            @csrf
                            @method('POST')
                            <p class="text-sm text-slate-600">Produk ini bukan unggulan. Menetapkan sebagai unggulan akan menampilkannya di section produk unggulan homepage.</p>
                            <button type="submit" class="mt-4 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#C26A4A] px-5 text-sm font-semibold text-white transition duration-300 hover:bg-[#A8563A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                Jadikan Unggulan
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="mt-6 rounded-2xl bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="16" x2="12" y2="12" />
                                <line x1="12" y1="8" x2="12.01" y2="8" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Informasi Produk</h2>
                            <p class="mt-0.5 text-xs text-slate-500">Data utama produk.</p>
                        </div>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
                        <div>
                            <dt class="font-medium text-slate-500">Nama</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $product->name }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Kategori</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $product->category?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Harga</dt>
                            <dd class="mt-0.5 text-slate-900">Rp{{ number_format((float) $product->price, 0, ',', '.') }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="font-medium text-slate-500">Deskripsi</dt>
                            <dd class="mt-0.5 text-slate-900 whitespace-pre-line">{{ $product->description ?? '—' }}</dd>
                        </div>
                    </dl>
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
                            <h2 class="text-base font-semibold text-slate-900">UMKM & Pemilik</h2>
                            <p class="mt-0.5 text-xs text-slate-500">Usaha yang menawarkan produk ini.</p>
                        </div>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
                        <div>
                            <dt class="font-medium text-slate-500">UMKM</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $product->umkm?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Pemilik</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $product->umkm?->user?->name ?? '—' }}</dd>
                        </div>
                        @if ($product->umkm)
                            <div class="sm:col-span-2">
                                <a href="{{ route('admin.umkms.show', $product->umkm) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-[#C26A4A] transition duration-300 hover:border-[#C26A4A] hover:bg-[#FAF8F5] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                    Lihat Detail UMKM
                                </a>
                            </div>
                        @endif
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
                            <h2 class="text-base font-semibold text-slate-900">Foto Produk</h2>
                            <p class="mt-0.5 text-xs text-slate-500">Foto utama produk untuk ditampilkan di halaman publik. Format JPG, PNG, atau WEBP, maksimal 2 MB.</p>
                        </div>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    @php
                        $photo = $product->media->firstWhere('collection', 'product');
                    @endphp

                    <div class="mt-4">
                        <x-media-upload
                            title="Foto Produk"
                            description="Foto utama produk. Tampil di daftar dan halaman detail produk."
                            :current="$photo"
                            :store-url="route('admin.products.media.store', [$product, 'product'])"
                            input-name="file_product"
                            :delete-url="$photo ? route('admin.media.destroy', $photo) : null"
                            item-label="foto produk"
                            optional
                            preview-class="aspect-[4/3] w-full max-w-sm"
                        />
                    </div>
                </div>
            </div>

            <div class="mt-6 rounded-2xl bg-white shadow-sm">
                <div class="border-b border-red-50 px-5 py-4 sm:px-6 sm:py-5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-red-50 text-red-600" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6" />
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2 2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Hapus Produk</h2>
                            <p class="mt-0.5 text-xs text-slate-500">Hapus produk beserta foto dan pengajuan verifikasinya secara permanen.</p>
                        </div>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="event.preventDefault(); confirmAction(this, 'Hapus Produk?', 'Produk {{ $product->name }} beserta foto dan pengajuan verifikasi terkait akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.', 'danger', 'Hapus Produk', 'Batal');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-red-600 px-5 text-sm font-semibold text-white transition duration-300 hover:bg-red-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">
                            Hapus Produk
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
