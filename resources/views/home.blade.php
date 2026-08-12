<x-app-layout>
    <div class="bg-emerald-50">
        <div class="max-w-7xl mx-auto px-4 py-16 sm:px-6 sm:py-20 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-semibold uppercase tracking-widest text-emerald-600">Desa Salamnunggal</p>
                <h1 class="mt-3 text-4xl font-semibold tracking-tight text-slate-900 sm:text-5xl">
                    Portal UMKM Desa Salamnunggal
                </h1>
                <p class="mx-auto mt-4 max-w-2xl text-lg text-slate-600">
                    Temukan UMKM serta produk unggulan dari Desa Salamnunggal. Jelajahi usaha lokal, lihat produknya, dan hubungi langsung pemiliknya.
                </p>

                <form action="{{ route('public.search') }}" method="GET" class="mx-auto mt-10 flex max-w-2xl flex-col gap-2 sm:flex-row">
                    <label for="home-search" class="sr-only">Cari UMKM atau produk</label>
                    <input id="home-search" type="search" name="q" placeholder="Cari UMKM atau produk..." value="{{ request('q') }}" class="w-full min-h-12 rounded-xl border border-slate-300 bg-white px-4 text-base text-slate-900 placeholder-slate-400 focus:border-emerald-500 focus:ring-emerald-500">
                    <button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-emerald-600 px-8 text-sm font-semibold text-white transition duration-300 hover:bg-emerald-500">
                        Cari
                    </button>
                </form>
                <p class="mt-3 text-sm text-slate-500">Contoh: kue, kerajinan, makanan, batik</p>

                <div class="mt-10 flex flex-col items-center justify-center gap-2 sm:flex-row sm:gap-6">
                    <a href="{{ route('public.umkm.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 underline-offset-4 hover:underline">
                        Jelajahi UMKM
                        <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6" />
                        </svg>
                    </a>
                    <span class="hidden h-4 w-px bg-emerald-200 sm:block" aria-hidden="true"></span>
                    <a href="{{ route('public.product.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 underline-offset-4 hover:underline">
                        Lihat Produk
                        <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6" />
                        </svg>
                    </a>
                </div>

                <p class="mt-10 flex items-center justify-center gap-2 text-sm text-slate-500">
                    <svg class="h-5 w-5 text-emerald-500" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 12.75 11.25 15 15 9.75" />
                        <path d="M21.801 10A10 10 0 1 1 17 3.335" />
                    </svg>
                    Setiap UMKM dan produk telah diverifikasi sebelum ditampilkan.
                </p>
            </div>
        </div>
    </div>

    <div class="border-b border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
            <dl class="mx-auto grid max-w-3xl grid-cols-3 gap-6 text-center">
                <div class="flex flex-col-reverse">
                    <dt class="mt-1 text-sm text-slate-500">UMKM</dt>
                    <dd class="text-3xl font-semibold text-emerald-600">{{ $umkmCount }}</dd>
                </div>
                <div class="flex flex-col-reverse">
                    <dt class="mt-1 text-sm text-slate-500">Produk</dt>
                    <dd class="text-3xl font-semibold text-emerald-600">{{ $productCount }}</dd>
                </div>
                <div class="flex flex-col-reverse">
                    <dt class="mt-1 text-sm text-slate-500">Kategori</dt>
                    <dd class="text-3xl font-semibold text-emerald-600">{{ $categoryCount }}</dd>
                </div>
            </dl>
        </div>
    </div>

    @if ($categories->isNotEmpty())
        <div class="max-w-7xl mx-auto px-4 py-16 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 class="text-2xl font-semibold text-slate-900">Kategori UMKM</h2>
                <a href="{{ route('public.umkm.index') }}" class="text-sm font-medium text-emerald-600 hover:underline">Lihat Semua UMKM</a>
            </div>
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($categories as $category)
                    <a href="{{ route('public.category.umkm', $category) }}" class="inline-flex min-h-11 items-center rounded-full bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 transition duration-300 hover:bg-emerald-100">{{ $category->name }}</a>
                @endforeach
            </div>
        </div>
    @endif

    @if ($featuredUmkms->isNotEmpty())
        @php
            $cols = match ($featuredUmkms->count()) {
                1 => 'max-w-md mx-auto',
                2 => 'sm:grid-cols-2',
                default => 'sm:grid-cols-2 lg:grid-cols-3',
            };
        @endphp
        <div class="max-w-7xl mx-auto px-4 py-16 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 class="text-2xl font-semibold text-slate-900">UMKM Unggulan</h2>
                <a href="{{ route('public.umkm.index') }}" class="text-sm font-medium text-emerald-600 hover:underline">Lihat Semua UMKM</a>
            </div>
            <div class="mt-6 grid grid-cols-1 gap-6 {{ $cols }}">
                @foreach ($featuredUmkms as $umkm)
                    <x-umkm-card :umkm="$umkm" />
                @endforeach
            </div>
        </div>
    @endif

    @if ($featuredProducts->isNotEmpty())
        @php
            $cols = match ($featuredProducts->count()) {
                1 => 'max-w-md mx-auto',
                2 => 'sm:grid-cols-2',
                default => 'sm:grid-cols-2 lg:grid-cols-3',
            };
        @endphp
        <div class="max-w-7xl mx-auto px-4 py-16 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 class="text-2xl font-semibold text-slate-900">Produk Unggulan</h2>
                <a href="{{ route('public.product.index') }}" class="text-sm font-medium text-emerald-600 hover:underline">Lihat Semua Produk</a>
            </div>
            <div class="mt-6 grid grid-cols-1 gap-6 {{ $cols }}">
                @foreach ($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </div>
    @endif

    <div class="bg-emerald-700">
        <div class="max-w-7xl mx-auto px-4 py-20 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-2xl font-semibold text-white">Punya usaha di Desa Salamnunggal?</h2>
                <p class="mt-3 text-emerald-100">
                    Daftarkan UMKM Anda agar lebih mudah ditemukan oleh masyarakat, mulai dari profil usaha hingga produk unggulan.
                </p>
                <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a href="{{ route('register') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-white px-6 py-2.5 text-sm font-semibold text-emerald-700 transition duration-300 hover:bg-emerald-50">
                        Daftarkan UMKM
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-emerald-400/40 px-6 py-2.5 text-sm font-semibold text-emerald-100 transition duration-300 hover:bg-emerald-600">
                        Sudah punya akun? Masuk
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
