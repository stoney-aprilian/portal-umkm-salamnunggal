<x-app-layout title="Verifikasi UMKM">
    <div class="py-8 sm:py-10">
        <div class="container-page">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-[#8A7464] transition-colors duration-150 hover:text-[#5C4033] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                Kembali ke Dashboard
            </a>

            <div class="mt-6 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Verifikasi UMKM</h1>
                    <p class="mt-1 text-sm text-slate-600">Periksa dan validasi pengajuan UMKM sebelum ditampilkan di portal.</p>
                </div>
                <p class="text-sm font-medium text-[#C26A4A]">{{ $pendingCount }} pengajuan menunggu verifikasi</p>
            </div>

            @if (session('status'))
                <x-alert type="success" class="mt-6">{{ session('status') }}</x-alert>
            @endif
            @if (session('error'))
                <x-alert type="error" class="mt-6">{{ session('error') }}</x-alert>
            @endif

            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="rounded-2xl bg-white p-4 shadow-sm sm:p-5">
                    <dl>
                        <dt class="text-xs font-medium text-slate-500">Menunggu Verifikasi</dt>
                        <dd class="mt-1 text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">{{ $pendingCount }}</dd>
                    </dl>
                </div>
                <div class="rounded-2xl bg-white p-4 shadow-sm sm:p-5">
                    <dl>
                        <dt class="text-xs font-medium text-slate-500">Disetujui</dt>
                        <dd class="mt-1 text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">{{ $approvedCount }}</dd>
                    </dl>
                </div>
                <div class="rounded-2xl bg-white p-4 shadow-sm sm:p-5">
                    <dl>
                        <dt class="text-xs font-medium text-slate-500">Ditolak</dt>
                        <dd class="mt-1 text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">{{ $rejectedCount }}</dd>
                    </dl>
                </div>
                <div class="rounded-2xl bg-white p-4 shadow-sm sm:p-5">
                    <dl>
                        <dt class="text-xs font-medium text-slate-500">Total Pengajuan</dt>
                        <dd class="mt-1 text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">{{ $totalCount }}</dd>
                    </dl>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <form method="GET" action="{{ route('admin.umkm.verification.index') }}" class="flex-1 sm:max-w-sm">
                    <label for="search" class="sr-only">Cari UMKM</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-4 w-4 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.35-4.35" />
                            </svg>
                        </div>
                        <input
                            type="search"
                            id="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari UMKM atau pemilik..."
                            class="block w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2"
                        >
                    </div>
                </form>
                <div class="flex items-center gap-2">
                    @if ($search !== '')
                        <a href="{{ route('admin.umkm.verification.index') }}" class="inline-flex min-h-10 items-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            Reset
                        </a>
                    @endif
                </div>
            </div>

            <section class="mt-4" aria-label="Daftar pengajuan UMKM">
                @if ($requests->isNotEmpty())
                    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
                        <div class="hidden grid-cols-12 gap-4 border-b border-slate-100 bg-slate-50/50 px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 sm:grid">
                            <div class="col-span-4">UMKM</div>
                            <div class="col-span-2">Pemilik</div>
                            <div class="col-span-2">Kategori</div>
                            <div class="col-span-2">Tanggal</div>
                            <div class="col-span-1">Status</div>
                            <div class="col-span-1 text-right">Aksi</div>
                        </div>
                        <ul class="divide-y divide-slate-100">
                            @foreach ($requests as $request)
                                @php
                                    $isRevision = $request->verifiable_type === \App\Models\UmkmRevision::class;
                                @endphp
                                <li>
                                    <a href="{{ route('admin.umkm.verification.show', $request) }}" class="flex items-center gap-4 px-5 py-4 transition-colors duration-150 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-inset sm:grid sm:grid-cols-12 sm:items-center sm:gap-4">
                                        <div class="min-w-0 flex-1 sm:col-span-4">
                                            <div class="flex items-center gap-2">
                                                <p class="truncate text-sm font-semibold text-slate-900">{{ $request->verifiable->name }}</p>
                                                @if ($isRevision)
                                                    <span class="inline-flex items-center rounded-full bg-sky-100 px-2 py-0.5 text-xs font-medium text-sky-800">Perubahan</span>
                                                @else
                                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-800">Pengajuan Baru</span>
                                                @endif
                                            </div>
                                            <p class="mt-0.5 text-xs text-slate-500 sm:hidden">
                                                {{ $request->user?->name ?? '—' }} &middot; {{ $request->verifiable->category?->name ?? '—' }} &middot; {{ $request->created_at->format('d M Y') }}
                                            </p>
                                        </div>
                                        <div class="hidden min-w-0 sm:col-span-2 sm:block">
                                            <p class="truncate text-sm text-slate-700">{{ $request->user?->name ?? '—' }}</p>
                                        </div>
                                        <div class="hidden min-w-0 sm:col-span-2 sm:block">
                                            <p class="truncate text-sm text-slate-700">{{ $request->verifiable->category?->name ?? '—' }}</p>
                                        </div>
                                        <div class="hidden sm:col-span-2 sm:block">
                                            <p class="text-sm text-slate-700">{{ $request->created_at->format('d M Y') }}</p>
                                        </div>
                                        <div class="hidden sm:col-span-1 sm:block">
                                            <x-badge status="pending" />
                                        </div>
                                        <div class="hidden sm:col-span-1 sm:flex sm:justify-end">
                                            <span class="inline-flex min-h-10 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-[#5C4033] transition duration-300 hover:border-[#C26A4A] hover:text-[#C26A4A]">
                                                Periksa
                                            </span>
                                        </div>
                                        <div class="sm:hidden">
                                            <div class="flex items-center justify-between">
                                                <x-badge status="pending" />
                                                <svg class="h-4 w-4 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="m9 18 6-6-6-6" />
                                                </svg>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center rounded-2xl bg-white px-5 py-10 text-center shadow-sm sm:px-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400" aria-hidden="true">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 12l2 2 4-4" />
                                <circle cx="12" cy="12" r="10" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-slate-900">Semua pengajuan sudah ditangani.</p>
                        <p class="mt-1 text-sm text-slate-500">Saat ini tidak ada pengajuan UMKM yang menunggu pemeriksaan.</p>
                        <a href="{{ route('admin.dashboard') }}" class="mt-4 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            Kembali ke Dashboard
                        </a>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
