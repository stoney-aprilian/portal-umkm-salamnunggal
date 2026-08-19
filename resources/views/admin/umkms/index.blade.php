<x-app-layout title="Kelola UMKM">
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
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Kelola UMKM</h1>
                    <p class="mt-1 text-sm text-slate-600">Kelola data UMKM seluruh pemilik, termasuk informasi profil, kategori, dan status.</p>
                </div>
                <a href="{{ route('admin.umkms.create') }}" class="inline-flex min-h-11 w-full shrink-0 items-center justify-center gap-2 rounded-xl bg-[#C26A4A] px-5 text-sm font-semibold text-white transition duration-300 hover:bg-[#A8563A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 sm:w-auto">
                    + Tambah UMKM
                </a>
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
                        <dt class="text-xs font-medium text-slate-500">Total UMKM</dt>
                        <dd class="mt-1 text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">{{ $totalCount }}</dd>
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
                        <dt class="text-xs font-medium text-slate-500">Menunggu</dt>
                        <dd class="mt-1 text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">{{ $pendingCount }}</dd>
                    </dl>
                </div>
                <div class="rounded-2xl bg-white p-4 shadow-sm sm:p-5">
                    <dl>
                        <dt class="text-xs font-medium text-slate-500">Ditolak / Revisi</dt>
                        <dd class="mt-1 text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">{{ $rejectedCount }}</dd>
                    </dl>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <form method="GET" action="{{ route('admin.umkms.index') }}" class="flex-1 sm:max-w-sm">
                    <label for="search" class="sr-only">Cari nama UMKM atau pemilik</label>
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
                            placeholder="Cari nama UMKM atau pemilik..."
                            class="block w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2"
                        >
                    </div>
                </form>
                <div class="flex items-center gap-2">
                    <select name="status" onchange="this.form.submit()" class="rounded-xl border border-slate-300 bg-white py-2.5 pl-3 pr-8 text-sm text-slate-700 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        <option value="">Semua Status</option>
                        <option value="approved" @selected($status === 'approved')>Disetujui</option>
                        <option value="pending" @selected($status === 'pending')>Menunggu</option>
                        <option value="rejected" @selected($status === 'rejected')>Ditolak</option>
                        <option value="draft" @selected($status === 'draft')>Draft</option>
                        <option value="needs_revision" @selected($status === 'needs_revision')>Perlu Revisi</option>
                    </select>
                    <select name="category" onchange="this.form.submit()" class="rounded-xl border border-slate-300 bg-white py-2.5 pl-3 pr-8 text-sm text-slate-700 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected($category === (string) $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <select name="sort" onchange="this.form.submit()" class="rounded-xl border border-slate-300 bg-white py-2.5 pl-3 pr-8 text-sm text-slate-700 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        <option value="desc" @selected($sort === 'desc')>Terbaru</option>
                        <option value="asc" @selected($sort === 'asc')>Terlama</option>
                        <option value="name_asc" @selected($sort === 'name_asc')>Nama A–Z</option>
                        <option value="name_desc" @selected($sort === 'name_desc')>Nama Z–A</option>
                    </select>
                    @if ($search !== '' || $status !== '' || $category !== '' || $sort !== 'desc')
                        <a href="{{ route('admin.umkms.index') }}" class="inline-flex min-h-10 items-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            Reset
                        </a>
                    @endif
                </div>
            </div>

            <section class="mt-4" aria-label="Daftar UMKM">
                @if ($umkms->isNotEmpty())
                    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
                        <div class="hidden grid-cols-12 gap-4 border-b border-slate-100 bg-slate-50/50 px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 sm:grid">
                            <div class="col-span-4">UMKM</div>
                            <div class="col-span-2">Pemilik</div>
                            <div class="col-span-2">Kategori</div>
                            <div class="col-span-1">Terdaftar</div>
                            <div class="col-span-1">Status</div>
                            <div class="col-span-1">Unggulan</div>
                            <div class="col-span-1">Aksi</div>
                        </div>
                        <ul class="divide-y divide-slate-100">
                            @foreach ($umkms as $umkm)
                                @php
                                    $statusLabels = [
                                        'draft' => 'Draft',
                                        'pending' => 'Menunggu',
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
                                    $logo = $umkm->media->firstWhere('collection', 'logo');
                                @endphp
                                <li>
                                    <div class="flex items-center gap-4 px-5 py-4 transition-colors duration-150 hover:bg-slate-50 focus:outline-none sm:grid sm:grid-cols-12 sm:items-center sm:gap-4">
                                        <div class="hidden sm:col-span-4 sm:flex sm:items-center sm:gap-3">
                                            <a href="{{ route('admin.umkms.show', $umkm) }}" class="flex items-center gap-3 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-inset">
                                                <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                                                    @if ($logo)
                                                        <img src="{{ Storage::disk($logo->disk)->url($logo->path) }}" alt="Logo {{ $umkm->name }}" class="h-full w-full object-cover">
                                                    @else
                                                        <span class="text-xs font-semibold text-slate-400">{{ substr($umkm->name, 0, 1) }}</span>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="truncate text-sm font-semibold text-slate-900">{{ $umkm->name }}</p>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="hidden sm:col-span-2 sm:block">
                                            <p class="truncate text-sm text-slate-700">{{ $umkm->user?->name ?? '—' }}</p>
                                        </div>
                                        <div class="hidden sm:col-span-2 sm:block">
                                            <p class="truncate text-sm text-slate-700">{{ $umkm->category?->name ?? '—' }}</p>
                                        </div>
                                        <div class="hidden sm:col-span-1 sm:block">
                                            <p class="text-sm text-slate-700">{{ $umkm->created_at->format('d M Y') }}</p>
                                        </div>
                                        <div class="hidden sm:col-span-1 sm:block">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusStyles[$umkm->status] ?? 'bg-slate-100 text-slate-700' }}">
                                                {{ $statusLabels[$umkm->status] ?? $umkm->status }}
                                            </span>
                                        </div>
                                        <div class="hidden sm:col-span-1 sm:flex sm:justify-end">
                                            @if ($umkm->is_featured)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">
                                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                                    </svg>
                                                    Unggulan
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-500">
                                                    Biasa
                                                </span>
                                            @endif
                                        </div>
                                        <div class="hidden sm:col-span-1 sm:flex sm:items-center sm:justify-end">
                                            <a href="{{ route('admin.umkms.edit', $umkm) }}" class="inline-flex min-h-10 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-[#5C4033] transition duration-300 hover:border-[#C26A4A] hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                                Edit
                                            </a>
                                        </div>
                                        <div class="sm:hidden">
                                            <div class="flex items-center justify-between">
                                                <a href="{{ route('admin.umkms.show', $umkm) }}" class="flex-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                                                    <div>
                                                        <p class="text-sm font-semibold text-slate-900">{{ $umkm->name }}</p>
                                                        <p class="mt-0.5 text-xs text-slate-500">{{ $umkm->user?->name ?? '—' }}</p>
                                                        <p class="mt-0.5 text-xs text-slate-500">{{ $umkm->category?->name ?? '—' }} &middot; {{ $umkm->created_at->format('d M Y') }}</p>
                                                    </div>
                                                </a>
                                                <div class="flex flex-col items-end gap-2">
                                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusStyles[$umkm->status] ?? 'bg-slate-100 text-slate-700' }}">
                                                        {{ $statusLabels[$umkm->status] ?? $umkm->status }}
                                                    </span>
                                                    @if ($umkm->is_featured)
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">
                                                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                                            </svg>
                                                            Unggulan
                                                        </span>
                                                    @endif
                                                    <a href="{{ route('admin.umkms.edit', $umkm) }}" class="text-xs font-semibold text-[#C26A4A] hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                                                        Edit
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center rounded-2xl bg-white px-5 py-10 text-center shadow-sm sm:px-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400" aria-hidden="true">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-slate-900">Belum ada UMKM</p>
                        <p class="mt-1 text-sm text-slate-500">Belum ada data UMKM yang dapat dikelola. Tambahkan UMKM pertama untuk mulai mengelola data.</p>
                        <a href="{{ route('admin.umkms.create') }}" class="mt-4 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#C26A4A] px-5 text-sm font-semibold text-white transition duration-300 hover:bg-[#A8563A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            + Tambah UMKM
                        </a>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
