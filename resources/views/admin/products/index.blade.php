<x-app-layout title="Kelola Produk">
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
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Kelola Produk</h1>
                    <p class="mt-1 text-sm text-slate-600">Kelola seluruh produk yang terdaftar pada UMKM.</p>
                </div>
                <a href="{{ route('admin.products.create') }}" class="inline-flex min-h-11 w-full shrink-0 items-center justify-center gap-2 rounded-xl bg-[#C26A4A] px-5 text-sm font-semibold text-white transition duration-300 hover:bg-[#A8563A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 sm:w-auto">
                    + Tambah Produk
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
                        <dt class="text-xs font-medium text-slate-500">Total Produk</dt>
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
                <form method="GET" action="{{ route('admin.products.index') }}" class="flex-1 sm:max-w-sm">
                    <label for="search" class="sr-only">Cari nama produk atau UMKM</label>
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
                            placeholder="Cari nama produk atau UMKM..."
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
                    <select name="umkm" onchange="this.form.submit()" class="rounded-xl border border-slate-300 bg-white py-2.5 pl-3 pr-8 text-sm text-slate-700 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        <option value="">Semua UMKM</option>
                        @foreach ($umkms as $u)
                            <option value="{{ $u->id }}" @selected($umkm === (string) $u->id)>{{ $u->name }}</option>
                        @endforeach
                    </select>
                    <select name="sort" onchange="this.form.submit()" class="rounded-xl border border-slate-300 bg-white py-2.5 pl-3 pr-8 text-sm text-slate-700 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        <option value="desc" @selected($sort === 'desc')>Terbaru</option>
                        <option value="asc" @selected($sort === 'asc')>Terlama</option>
                        <option value="name_asc" @selected($sort === 'name_asc')>Nama A–Z</option>
                        <option value="name_desc" @selected($sort === 'name_desc')>Nama Z–A</option>
                    </select>
                    @if ($search !== '' || $status !== '' || $category !== '' || $umkm !== '' || $sort !== 'desc')
                        <a href="{{ route('admin.products.index') }}" class="inline-flex min-h-10 items-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            Reset
                        </a>
                    @endif
                </div>
            </div>

            <section class="mt-4" aria-label="Daftar produk">
                @if ($products->isNotEmpty())
                    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
                        <div class="hidden grid-cols-12 gap-4 border-b border-slate-100 bg-slate-50/50 px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 sm:grid">
                            <div class="col-span-4">Produk</div>
                            <div class="col-span-2">UMKM</div>
                            <div class="col-span-2">Kategori</div>
                            <div class="col-span-1">Harga</div>
                            <div class="col-span-1">Status</div>
                            <div class="col-span-1">Unggulan</div>
                            <div class="col-span-1">Aksi</div>
                        </div>
                        <ul class="divide-y divide-slate-100">
                            @foreach ($products as $product)
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
                                    $photo = $product->media->firstWhere('collection', 'product');
                                @endphp
                                <li>
                                    <div class="flex items-center gap-4 px-5 py-4 transition-colors duration-150 hover:bg-slate-50 focus:outline-none sm:grid sm:grid-cols-12 sm:items-center sm:gap-4">
                                        <div class="hidden sm:col-span-4 sm:flex sm:items-center sm:gap-3">
                                            <a href="{{ route('admin.products.show', $product) }}" class="flex items-center gap-3 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-inset">
                                                <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                                                    @if ($photo)
                                                        <img src="{{ Storage::disk($photo->disk)->url($photo->path) }}" alt="Foto {{ $product->name }}" class="h-full w-full object-cover">
                                                    @else
                                                        <svg class="h-5 w-5 text-slate-300" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                                                            <circle cx="9" cy="9" r="2" />
                                                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="truncate text-sm font-semibold text-slate-900">{{ $product->name }}</p>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="hidden sm:col-span-2 sm:block">
                                            <p class="truncate text-sm text-slate-700">{{ $product->umkm?->name ?? '—' }}</p>
                                        </div>
                                        <div class="hidden sm:col-span-2 sm:block">
                                            <p class="truncate text-sm text-slate-700">{{ $product->category?->name ?? '—' }}</p>
                                        </div>
                                        <div class="hidden sm:col-span-1 sm:block">
                                            <p class="text-sm text-slate-700">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="hidden sm:col-span-1 sm:block">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusStyles[$product->status] ?? 'bg-slate-100 text-slate-700' }}">
                                                {{ $statusLabels[$product->status] ?? $product->status }}
                                            </span>
                                        </div>
                                        <div class="hidden sm:col-span-1 sm:flex sm:justify-end">
                                            @if ($product->is_featured)
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
                                            <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex min-h-10 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-[#5C4033] transition duration-300 hover:border-[#C26A4A] hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                                Edit
                                            </a>
                                        </div>
                                        <div class="sm:hidden">
                                            <div class="flex items-center justify-between">
                                                <a href="{{ route('admin.products.show', $product) }}" class="flex-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                                                    <div>
                                                        <p class="text-sm font-semibold text-slate-900">{{ $product->name }}</p>
                                                        <p class="mt-0.5 text-xs text-slate-500">{{ $product->umkm?->name ?? '—' }}</p>
                                                        <p class="mt-0.5 text-xs text-slate-500">{{ $product->category?->name ?? '—' }} &middot; Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>
                                                    </div>
                                                </a>
                                                <div class="flex flex-col items-end gap-2">
                                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusStyles[$product->status] ?? 'bg-slate-100 text-slate-700' }}">
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
                                                    <a href="{{ route('admin.products.edit', $product) }}" class="text-xs font-semibold text-[#C26A4A] hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
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
                        @if ($search !== '' || $status !== '' || $category !== '' || $umkm !== '' || $sort !== 'desc')
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400" aria-hidden="true">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8" />
                                    <path d="m21 21-4.35-4.35" />
                                </svg>
                            </div>
                            <p class="mt-3 text-sm font-semibold text-slate-900">Produk tidak ditemukan</p>
                            <p class="mt-1 text-sm text-slate-500">Coba ubah kata kunci atau filter yang digunakan.</p>
                        @else
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400" aria-hidden="true">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96" />
                                    <line x1="12" x2="12" y1="22.08" y2="12" />
                                </svg>
                            </div>
                            <p class="mt-3 text-sm font-semibold text-slate-900">Belum ada produk</p>
                            <p class="mt-1 text-sm text-slate-500">Belum ada produk yang dapat dikelola saat ini.</p>
                            <a href="{{ route('admin.products.create') }}" class="mt-4 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#C26A4A] px-5 text-sm font-semibold text-white transition duration-300 hover:bg-[#A8563A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                + Tambah Produk
                            </a>
                        @endif
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
