<x-app-layout title="Cari">
    <div class="py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="border-b border-slate-200 pb-6 text-center">
                <h1 class="text-3xl font-semibold tracking-tight text-slate-900">Cari UMKM dan Produk</h1>
                <p class="mx-auto mt-2 max-w-2xl text-slate-600">Temukan UMKM, produk, dan kategori lokal Desa Salamnunggal.</p>
            </div>

            <div class="mt-8 flex w-full justify-center">
                <form action="{{ route('public.search') }}" method="GET" class="flex w-full max-w-2xl flex-col gap-2 sm:flex-row">
                    <label for="search-input" class="sr-only">Kata kunci pencarian</label>
                    <input id="search-input" type="search" name="q" value="{{ $query }}" placeholder="Cari UMKM, produk, atau kategori..." class="w-full min-h-12 flex-1 rounded-xl border border-slate-300 bg-white px-4 text-base text-slate-900 placeholder-slate-400 focus:border-emerald-500 focus:ring-emerald-500">
                    <button type="submit" class="inline-flex min-h-12 w-full shrink-0 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 text-sm font-semibold text-white transition duration-300 hover:bg-emerald-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 sm:w-auto">
                        <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                        Cari
                    </button>
                </form>
            </div>

            @if ($query === '')
                <div class="mt-10 rounded-2xl bg-white px-6 py-10 text-center shadow-sm sm:px-10">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-50 text-slate-400">
                        <svg class="h-6 w-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                    </div>
                    <h2 class="mt-4 text-xl font-semibold text-slate-900">Temukan UMKM dan produk yang Anda cari.</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm text-slate-600">Cari UMKM, produk, atau kategori berdasarkan nama atau deskripsi.</p>
                </div>
            @elseif ($umkms->isEmpty() && $products->isEmpty() && $categories->isEmpty())
                <div class="mt-10 rounded-2xl bg-white px-6 py-10 text-center shadow-sm sm:px-10">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-50 text-slate-400">
                        <svg class="h-6 w-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                    </div>
                    <h2 class="mx-auto mt-4 max-w-xl break-words text-xl font-semibold leading-snug text-slate-900">Tidak menemukan hasil untuk kata kunci "{{ $query }}"</h2>
                    <p class="mt-2 text-sm text-slate-600">Coba kata kunci lain, atau jelajahi seluruh katalog.</p>
                    <div class="mt-6 flex flex-col items-center justify-center gap-3 sm:flex-row">
                        <a href="{{ route('public.umkm.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition duration-300 hover:bg-emerald-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                            Lihat Semua UMKM
                        </a>
                        <a href="{{ route('public.product.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                            Lihat Semua Produk
                        </a>
                    </div>
                </div>
            @else
                <p class="mt-8 text-sm text-slate-500">
                    Menampilkan {{ $umkms->count() + $products->count() + $categories->count() }} hasil untuk
                    <span class="break-words font-medium text-slate-700">"{{ $query }}"</span>
                </p>

                @if ($categories->isNotEmpty())
                    <section class="mt-10">
                        <h2 class="text-xl font-semibold tracking-tight text-slate-900">Kategori</h2>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($categories as $category)
                                <a href="{{ $category->type === 'umkm' ? route('public.category.umkm', $category) : route('public.category.product', $category) }}" class="inline-flex min-h-11 items-center gap-2 rounded-full bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 transition duration-300 hover:bg-emerald-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                                    {{ $category->name }}
                                    <span class="text-xs font-semibold text-emerald-600">{{ $category->type === 'umkm' ? 'UMKM' : 'Produk' }}</span>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($umkms->isNotEmpty())
                    @php $cols = $umkms->count() === 1 ? 'max-w-md mx-auto' : 'sm:grid-cols-2 lg:grid-cols-3'; @endphp
                    <section class="mt-10">
                        <h2 class="text-xl font-semibold tracking-tight text-slate-900">UMKM</h2>
                        <div class="mt-4 grid grid-cols-1 gap-6 {{ $cols }}">
                            @foreach ($umkms as $umkm)
                                <x-umkm-card :umkm="$umkm" />
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($products->isNotEmpty())
                    @php $cols = $products->count() === 1 ? 'max-w-md mx-auto' : 'sm:grid-cols-2 lg:grid-cols-3'; @endphp
                    <section class="mt-10">
                        <h2 class="text-xl font-semibold tracking-tight text-slate-900">Produk</h2>
                        <div class="mt-4 grid grid-cols-1 gap-6 {{ $cols }}">
                            @foreach ($products as $product)
                                <x-product-card :product="$product" refined />
                            @endforeach
                        </div>
                    </section>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
