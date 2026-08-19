<x-app-layout title="Dashboard">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-3">
            <div class="min-w-0">
                <h1 class="text-2xl font-semibold tracking-tight text-[#3F2A22] sm:text-3xl">
                    {{ __('Dashboard') }}
                </h1>
                <p class="mt-1 text-sm text-[#8A7464] sm:text-base">
                    Selamat datang, {{ Auth::user()->name }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="container-page">
            @if (session('status'))
                <x-alert type="success" class="mb-6">{{ session('status') }}</x-alert>
            @endif

            @if (session('error'))
                <x-alert type="error" class="mb-6">{{ session('error') }}</x-alert>
            @endif

            @if ($umkm === null)
                <div class="overflow-hidden rounded-2xl bg-white shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-[#ECE5D9]">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-start gap-4">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#F4EDE1] text-[#5C4033]">
                                <svg class="h-6 w-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                                    <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                                    <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <h2 class="text-xl font-semibold tracking-tight text-[#3F2A22]">{{ __('Mulai promosikan usaha Anda') }}</h2>
                                <p class="mt-1 text-sm text-[#8A7464]">
                                    Daftarkan UMKM Anda di Portal UMKM Salamnunggal agar mudah ditemukan masyarakat.
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 grid gap-4 sm:grid-cols-3">
                            <div class="flex items-start gap-3 rounded-xl bg-[#FAF6F5] p-4">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#5C4033] text-xs font-bold text-white">1</span>
                                <div>
                                    <p class="text-sm font-semibold text-[#3F2A22]">Isi Data UMKM</p>
                                    <p class="mt-0.5 text-sm text-[#8A7464]">Lengkapi informasi usaha, alamat, dan kontak.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 rounded-xl bg-[#FAF6F5] p-4">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#5C4033] text-xs font-bold text-white">2</span>
                                <div>
                                    <p class="text-sm font-semibold text-[#3F2A22]">Kirim Pengajuan</p>
                                    <p class="mt-0.5 text-sm text-[#8A7464]">Administrator akan memeriksa pengajuan Anda.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 rounded-xl bg-[#FAF6F5] p-4">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#5C4033] text-xs font-bold text-white">3</span>
                                <div>
                                    <p class="text-sm font-semibold text-[#3F2A22]">Tampil di Portal</p>
                                    <p class="mt-0.5 text-sm text-[#8A7464]">Setelah disetujui, UMKM Anda tampil di publik.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap items-center gap-3">
                            <a href="{{ route('owner.umkm.create') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#5C4033] px-5 text-sm font-semibold text-white transition-colors duration-150 hover:bg-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                {{ __('Ajukan UMKM') }}
                            </a>
                            <p class="text-sm text-[#8A7464]">
                                Langkah berikutnya: lengkapi data UMKM Anda.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                @php
                    $actions = [];
                    if (in_array($umkm->status, ['needs_revision', 'rejected'], true)) {
                        $actions[] = [
                            'text' => $umkm->status === 'rejected'
                                ? 'UMKM perlu diperbaiki sebelum diajukan kembali'
                                : 'UMKM perlu diperbaiki',
                            'label' => 'Perbaiki UMKM',
                            'url' => route('owner.umkm.edit', $umkm),
                        ];
                    }
                    if (($productCounts['needs_revision'] ?? 0) > 0) {
                        $actions[] = ['text' => 'Produk perlu diperbaiki', 'label' => 'Perbaiki Produk', 'url' => route('owner.products.index', $umkm)];
                    }
                    if (($productCounts['rejected'] ?? 0) > 0) {
                        $actions[] = ['text' => 'Produk ditolak', 'label' => 'Perbaiki Produk', 'url' => route('owner.products.index', $umkm)];
                    }
                    if (($productCounts['draft'] ?? 0) > 0) {
                        $actions[] = ['text' => 'Anda memiliki produk yang belum diajukan', 'label' => 'Kelola Produk', 'url' => route('owner.products.index', $umkm)];
                    }
                    if ($umkm->status === 'approved' && $umkmRevision !== null && in_array($umkmRevision->status, ['needs_revision', 'rejected'], true)) {
                        $actions[] = ['text' => 'Perubahan UMKM Anda perlu diperbaiki', 'label' => 'Perbaiki Perubahan', 'url' => route('owner.umkm.revisions.edit', $umkmRevision)];
                    } elseif ($umkm->status === 'approved' && $umkmRevision !== null && $umkmRevision->status === 'draft') {
                        $actions[] = ['text' => 'Perubahan UMKM Anda belum diajukan', 'label' => 'Kelola Perubahan', 'url' => route('owner.umkm.revisions.edit', $umkmRevision)];
                    }

                    $umkmStatus = match ($umkm->status) {
                        'draft' => [
                            'title' => 'Belum diajukan',
                            'icon' => 'draft',
                            'message' => 'UMKM Anda masih berupa draft. Lengkapi informasi dan kirim pengajuan agar diperiksa Administrator.',
                            'accent' => 'bg-[#F4EDE1] text-[#5C4033]',
                        ],
                        'pending' => [
                            'title' => 'Sedang diperiksa',
                            'icon' => 'pending',
                            'message' => 'UMKM Anda sedang menunggu pemeriksaan Administrator.',
                            'accent' => 'bg-amber-100 text-amber-700',
                        ],
                        'approved' => [
                            'title' => 'Aktif',
                            'icon' => 'approved',
                            'message' => 'UMKM Anda telah disetujui dan dapat ditampilkan di Portal.',
                            'accent' => 'bg-emerald-100 text-emerald-700',
                        ],
                        'needs_revision' => [
                            'title' => 'Perlu diperbaiki',
                            'icon' => 'needs_revision',
                            'message' => 'Administrator meminta perubahan pada pengajuan Anda.',
                            'accent' => 'bg-amber-100 text-amber-700',
                        ],
                        'rejected' => [
                            'title' => 'Pengajuan ditolak',
                            'icon' => 'rejected',
                            'message' => 'Pengajuan UMKM Anda ditolak. Silakan perbaiki informasi sesuai catatan Administrator, lalu kirim kembali.',
                            'accent' => 'bg-red-100 text-red-700',
                        ],
                        default => ['title' => 'Aktif', 'icon' => 'approved', 'message' => '', 'accent' => 'bg-emerald-100 text-emerald-700'],
                    };

                    $bulan = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                    ];
                @endphp

                <div class="space-y-6">
                    <section class="overflow-hidden rounded-2xl bg-white shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-[#ECE5D9]">
                        <div class="p-6 sm:p-8">
                            <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex items-start gap-4">
                                    @if ($umkmStatus['icon'] === 'approved')
                                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                                            <svg class="h-6 w-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M20 6 9 17l-5-5" />
                                            </svg>
                                        </span>
                                    @elseif ($umkmStatus['icon'] === 'pending')
                                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                                            <svg class="h-6 w-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10" />
                                                <path d="M12 6v6l4 2" />
                                            </svg>
                                        </span>
                                    @elseif ($umkmStatus['icon'] === 'needs_revision')
                                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                                            <svg class="h-6 w-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                                                <line x1="12" x2="12" y1="9" y2="13" />
                                                <line x1="12" x2="12.01" y1="17" y2="17" />
                                            </svg>
                                        </span>
                                    @elseif ($umkmStatus['icon'] === 'rejected')
                                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-100 text-red-700">
                                            <svg class="h-6 w-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10" />
                                                <path d="m15 9-6 6" />
                                                <path d="m9 9 6 6" />
                                            </svg>
                                        </span>
                                    @else
                                         <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#F4EDE1] text-[#5C4033]">
                                            <svg class="h-6 w-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                                                <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                                            </svg>
                                        </span>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h2 class="text-xl font-semibold tracking-tight text-[#3F2A22]">{{ $umkmStatus['title'] }}</h2>
                                            <x-badge :status="$umkm->status" />
                                        </div>
                                        <p class="mt-1 break-words text-sm font-medium text-[#6F5D50]">{{ $umkm->name }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2 sm:flex-col sm:items-end">
                                    @if ($umkm->status === 'draft')
                                        <form method="POST" action="{{ route('owner.umkm.submit', $umkm) }}" onsubmit="event.preventDefault(); confirmAction(this, 'Kirim Pengajuan UMKM?', 'UMKM akan dikirim untuk diperiksa Administrator.', 'success', 'Kirim Pengajuan', 'Batal');">
                                            @csrf
                                            <x-primary-button>{{ __('Kirim Pengajuan') }}</x-primary-button>
                                        </form>
                                        <a href="{{ route('owner.umkm.edit', $umkm) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#ECE5D9] bg-white px-4 text-sm font-semibold text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                            {{ __('Ubah UMKM') }}
                                        </a>
                                    @elseif (in_array($umkm->status, ['needs_revision', 'rejected'], true))
                                        <a href="{{ route('owner.umkm.edit', $umkm) }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#C26A4A] px-5 text-sm font-semibold text-white transition-colors duration-150 hover:bg-[#A3523A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                            <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                                                <line x1="12" x2="12" y1="9" y2="13" />
                                                <line x1="12" x2="12.01" y1="17" y2="17" />
                                            </svg>
                                            {{ __('Perbaiki UMKM') }}
                                        </a>
                                    @elseif ($umkm->status === 'approved')
                                        <a href="{{ route('public.umkm.show', $umkm) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#ECE5D9] bg-white px-4 text-sm font-semibold text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                            {{ __('Lihat UMKM') }}
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <p class="mt-4 text-sm leading-relaxed text-[#6F5D50]">{{ $umkmStatus['message'] }}</p>

                            @if ($umkm->status === 'needs_revision' && $revisionNote)
                                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                                    <p class="text-sm text-amber-800">
                                        <span class="font-semibold">Catatan Administrator:</span> {{ $revisionNote }}
                                    </p>
                                </div>
                            @elseif ($umkm->status === 'rejected' && $rejectionNote)
                                <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                                    <p class="text-sm text-red-800">
                                        <span class="font-semibold">Alasan Penolakan:</span> {{ $rejectionNote }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </section>

                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        @if ($umkm->status === 'approved')
                            <section class="overflow-hidden rounded-2xl bg-white shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-[#ECE5D9]">
                                <div class="p-6 sm:p-8">
                                    <h2 class="text-xs font-semibold uppercase tracking-wider text-[#8A7464]">{{ __('Status Produk') }}</h2>

                                    @if ($productCounts['total'] > 0)
                                        <dl class="mt-4 grid grid-cols-2 gap-x-6 gap-y-4 sm:grid-cols-3 xl:grid-cols-6">
                                            <div>
                                                <dt class="text-sm text-[#8A7464]">Total Produk</dt>
                                                <dd class="mt-1 text-lg font-semibold text-[#3F2A22]">{{ $productCounts['total'] }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-sm text-[#8A7464]">Draft</dt>
                                                <dd class="mt-1 text-lg font-semibold text-[#3F2A22]">{{ $productCounts['draft'] }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-sm text-[#8A7464]">Menunggu</dt>
                                                <dd class="mt-1 text-lg font-semibold text-[#3F2A22]">{{ $productCounts['pending'] }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-sm text-[#8A7464]">Disetujui</dt>
                                                <dd class="mt-1 text-lg font-semibold text-[#3F2A22]">{{ $productCounts['approved'] }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-sm text-[#8A7464]">Perlu Revisi</dt>
                                                <dd class="mt-1 text-lg font-semibold text-[#3F2A22]">{{ $productCounts['needs_revision'] }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-sm text-[#8A7464]">Ditolak</dt>
                                                <dd class="mt-1 text-lg font-semibold text-[#3F2A22]">{{ $productCounts['rejected'] }}</dd>
                                            </div>
                                        </dl>
                                    @else
                                        <p class="mt-4 text-sm leading-relaxed text-[#6F5D50]">
                                            Belum ada produk. Tambahkan produk pertama Anda untuk mulai menampilkan katalog usaha.
                                        </p>
                                    @endif
                                    <div class="mt-5">
                                        <a href="{{ route('owner.products.index', $umkm) }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-[#ECE5D9] bg-white px-4 text-sm font-semibold text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                            {{ __('Kelola Produk') }}
                                        </a>
                                    </div>
                                </div>
                            </section>

                            <section class="overflow-hidden rounded-2xl bg-white shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-[#ECE5D9]">
                                <div class="p-6 sm:p-8">
                                    <h2 class="text-xs font-semibold uppercase tracking-wider text-[#8A7464]">{{ __('Perubahan UMKM') }}</h2>

                                    @if ($umkmRevision === null)
                                        <p class="mt-4 text-sm leading-relaxed text-[#6F5D50]">
                                            Data UMKM Anda sedang tampil di publik. Ajukan perubahan (informasi, kontak, atau media) dan perubahan baru berlaku setelah disetujui Administrator.
                                        </p>
                                        <a href="{{ route('owner.umkm.revisions.create', $umkm) }}" class="mt-4 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#5C4033] px-5 text-sm font-semibold text-white transition-colors duration-150 hover:bg-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                            {{ __('Ajukan Perubahan UMKM') }}
                                        </a>
                                    @else
                                        <div class="mt-4 flex flex-wrap items-center gap-2">
                                            <span class="text-sm font-medium text-[#3F2A22]">Perubahan saat ini:</span>
                                            <x-badge :status="$umkmRevision->status" />
                                        </div>
                                        @if ($umkmRevisionNote && in_array($umkmRevision->status, ['needs_revision', 'rejected'], true))
                                            <p class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                                                <span class="font-semibold">Catatan Administrator:</span> {{ $umkmRevisionNote }}
                                            </p>
                                        @endif
                                        <p class="mt-3 text-sm leading-relaxed text-[#6F5D50]">
                                            Data yang tampil di publik tetap menggunakan data lama sampai perubahan disetujui.
                                        </p>
                                        <div class="mt-4 flex flex-wrap gap-3">
                                            @if ($umkmRevision->status === 'draft')
                                                <form method="POST" action="{{ route('owner.umkm.revisions.submit', $umkmRevision) }}" onsubmit="event.preventDefault(); confirmAction(this, 'Kirim Perubahan UMKM?', 'Perubahan UMKM akan dikirim untuk diperiksa Administrator.', 'success', 'Kirim Perubahan', 'Batal');">
                                                    @csrf
                                                    <x-primary-button>{{ __('Kirim Perubahan') }}</x-primary-button>
                                                </form>
                                            @endif
                                            @if (in_array($umkmRevision->status, ['draft', 'needs_revision', 'rejected'], true))
                                                <a href="{{ route('owner.umkm.revisions.edit', $umkmRevision) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#ECE5D9] bg-white px-4 text-sm font-semibold text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                                    {{ in_array($umkmRevision->status, ['needs_revision', 'rejected'], true) ? __('Perbaiki Perubahan') : __('Kelola Perubahan') }}
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </section>
                        @endif

                        <section class="overflow-hidden rounded-2xl bg-white shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-[#ECE5D9] @if($umkm->status === 'approved') lg:col-span-2 @endif">
                            <div class="p-6 sm:p-8">
                                <h2 class="text-xs font-semibold uppercase tracking-wider text-[#8A7464]">{{ __('Hal yang Perlu Tindakan') }}</h2>

                                @if ($umkm->status === 'draft')
                                    <div class="mt-4 flex items-start gap-3">
                                        <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#F4EDE1] text-[#5C4033]">
                                            <svg class="h-3.5 w-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                                                <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                                            </svg>
                                        </span>
                                        <p class="text-sm leading-relaxed text-[#6F5D50]">
                                            Lengkapi informasi UMKM lalu kirim pengajuan agar diperiksa Administrator.
                                        </p>
                                    </div>
                                @elseif ($umkm->status === 'pending')
                                    <div class="mt-4 flex items-start gap-3">
                                        <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                                            <svg class="h-3.5 w-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10" />
                                                <path d="M12 6v6l4 2" />
                                            </svg>
                                        </span>
                                        <p class="text-sm leading-relaxed text-[#6F5D50]">
                                            Pengajuan UMKM Anda sedang diperiksa Administrator. Hasil pemeriksaan akan muncul di sini.
                                        </p>
                                    </div>
                                @elseif ($umkm->status === 'approved' && ($productCounts['pending'] ?? 0) > 0)
                                    <div class="mt-4 flex items-start gap-3">
                                        <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                                            <svg class="h-3.5 w-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10" />
                                                <path d="M12 6v6l4 2" />
                                            </svg>
                                        </span>
                                        <p class="text-sm leading-relaxed text-[#6F5D50]">
                                            Anda memiliki {{ $productCounts['pending'] }} produk yang sedang diperiksa Administrator.
                                        </p>
                                    </div>
                                @elseif ($umkm->status === 'approved' && $umkmRevision !== null && $umkmRevision->status === 'pending')
                                    <div class="mt-4 flex items-start gap-3">
                                        <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                                            <svg class="h-3.5 w-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10" />
                                                <path d="M12 6v6l4 2" />
                                            </svg>
                                        </span>
                                        <p class="text-sm leading-relaxed text-[#6F5D50]">
                                            Perubahan UMKM Anda sedang diperiksa Administrator. Data yang tampil di publik tidak berubah sampai perubahan disetujui.
                                        </p>
                                    </div>
                                @elseif (! empty($actions))
                                    <ul class="mt-4 divide-y divide-[#ECE5D9]">
                                        @foreach ($actions as $action)
                                            <li class="flex flex-col gap-3 py-3 first:pt-0 last:pb-0 sm:flex-row sm:items-center">
                                                <div class="flex min-w-0 items-start gap-3">
                                                    <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                                                        <svg class="h-3.5 w-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                                                            <line x1="12" x2="12" y1="9" y2="13" />
                                                            <line x1="12" x2="12.01" y1="17" y2="17" />
                                                        </svg>
                                                    </span>
                                                    <p class="break-words text-sm text-[#6F5D50]">{{ $action['text'] }}</p>
                                                </div>
                                                <a href="{{ $action['url'] }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#C26A4A] px-5 text-sm font-semibold text-white transition-colors duration-150 hover:bg-[#A3523A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 sm:ms-auto">
                                                    {{ $action['label'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="mt-4 flex items-start gap-3">
                                        <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                                            <svg class="h-3.5 w-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M20 6 9 17l-5-5" />
                                            </svg>
                                        </span>
                                        <p class="text-sm leading-relaxed text-[#6F5D50]">
                                            Semua pengajuan Anda sudah diproses. Tidak ada tindakan yang diperlukan.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </section>

                        @if ($umkm->status === 'approved')
                            <section class="overflow-hidden rounded-2xl bg-white shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-[#ECE5D9] lg:col-span-2">
                                <div class="p-6 sm:p-8">
                                    <h2 class="text-xs font-semibold uppercase tracking-wider text-[#8A7464]">{{ __('Aksi Cepat') }}</h2>

                                    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                                        <a href="{{ route('owner.products.create', $umkm) }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#5C4033] px-5 text-sm font-semibold text-white transition-colors duration-150 hover:bg-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                            <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 12h14" />
                                                <path d="M12 5v14" />
                                            </svg>
                                            {{ __('Tambah Produk') }}
                                        </a>
                                        <a href="{{ route('public.umkm.show', $umkm) }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-[#ECE5D9] bg-white px-5 text-sm font-semibold text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                            <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M15 3h6v6" />
                                                <path d="M10 14 21 3" />
                                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                                            </svg>
                                            {{ __('Lihat Portal Publik') }}
                                        </a>
                                    </div>
                                </div>
                            </section>
                        @endif

                        <section class="overflow-hidden rounded-2xl bg-white shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-[#ECE5D9] @if($umkm->status === 'approved') lg:col-span-2 @endif">
                            <div class="p-6 sm:p-8">
                                <h2 class="text-xs font-semibold uppercase tracking-wider text-[#8A7464]">{{ __('Aktivitas Terbaru') }}</h2>

                                @if ($activities->isEmpty())
                                    <p class="mt-4 text-sm leading-relaxed text-[#6F5D50]">
                                        Belum ada aktivitas.
                                    </p>
                                @else
                                    <ul class="mt-4 space-y-3">
                                        @foreach ($activities as $activity)
                                            @php
                                                $type = match ($activity->event) {
                                                    'approved' => 'success',
                                                    'needs_revision', 'rejected' => 'warning',
                                                    default => 'neutral',
                                                };
                                                $tanggal = $activity->created_at->format('d') . ' ' . $bulan[$activity->created_at->month] . ' ' . $activity->created_at->format('Y');
                                            @endphp
                                            <li class="flex items-start gap-3">
                                                @if ($type === 'success')
                                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                                                        <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M20 6 9 17l-5-5" />
                                                        </svg>
                                                    </span>
                                                @elseif ($type === 'warning')
                                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                                                        <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                                                            <line x1="12" x2="12" y1="9" y2="13" />
                                                            <line x1="12" x2="12.01" y1="17" y2="17" />
                                                        </svg>
                                                    </span>
                                                @else
                                                     <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#F4EDE1] text-[#5C4033]">
                                                        <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <circle cx="12" cy="12" r="10" />
                                                            <circle cx="12" cy="12" r="1" fill="currentColor" stroke="none" />
                                                        </svg>
                                                    </span>
                                                @endif
                                                <div class="text-sm text-[#6F5D50]">
                                                    {{ $activity->description }}
                                                    <span class="block text-xs text-[#8A7464]">{{ $tanggal }}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </section>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
