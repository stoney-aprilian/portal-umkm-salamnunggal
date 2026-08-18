<x-app-layout title="Cari">
    <div class="py-8 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- ============ PAGE HEADER ============ --}}
            <div class="border-b border-[#E8D8C8] pb-6">
                <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-3">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#C26A4A]">Pencarian</p>
                        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-[#3F2A22]">Cari UMKM dan Produk</h1>
                        <p class="mt-2 max-w-2xl text-[#6F5D50]">Temukan UMKM, produk, dan kategori lokal Desa Salamnunggal.</p>
                    </div>
                </div>
            </div>

            {{-- ============ SEARCH BAR ============ --}}
            <div class="mt-6 flex justify-center">
                <form action="{{ route('public.search') }}" method="GET" class="flex w-full max-w-3xl flex-col gap-3 sm:flex-row">
                    <label for="search-input" class="sr-only">Kata kunci pencarian</label>
                    <input id="search-input" type="search" name="q" value="{{ $query }}" placeholder="Cari UMKM, produk, atau kategori..." class="w-full min-h-[52px] flex-1 rounded-xl border border-[#E8D8C8] bg-white px-4 text-base text-[#3F2A22] placeholder-[#A99A8C] focus:border-[#C26A4A] focus:ring-[#C26A4A]">
                    <button type="submit" class="inline-flex min-h-[52px] w-full shrink-0 items-center justify-center gap-2 rounded-xl bg-[#5C4033] px-6 text-sm font-semibold text-white transition duration-300 hover:bg-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 sm:w-auto">
                        <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                        Cari
                    </button>
                </form>
            </div>

            {{-- ============ INITIAL STATE ============ --}}
            @if ($query === '')
                <div class="mt-8">
                    <div class="mx-auto max-w-2xl text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]">
                            <svg class="h-6 w-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.3-4.3" />
                            </svg>
                        </div>
                        <h2 class="mt-4 text-xl font-semibold text-[#3F2A22]">Temukan UMKM dan produk lokal</h2>
                        <p class="mx-auto mt-2 max-w-md text-sm text-[#6F5D50]">Cari berdasarkan nama, produk, atau kategori untuk menemukan usaha dan produk lokal Desa Salamnunggal.</p>
                    </div>

                    @if ($categories->isNotEmpty())
                        <div class="mt-8">
                            <p class="text-sm font-medium text-[#8A7464]">Jelajahi berdasarkan kategori:</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($categories->take(8) as $category)
                                    <a href="{{ $category->type === 'umkm' ? route('public.category.umkm', $category) : route('public.category.product', $category) }}" class="inline-flex min-h-11 items-center gap-2 rounded-full bg-[#F4EDE1] px-4 py-2 text-sm font-medium text-[#5C4033] transition duration-300 hover:bg-[#E8D8C8] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                        {{ $category->name }}
                                        <span class="text-xs font-semibold text-[#C26A4A]">{{ $category->type === 'umkm' ? 'UMKM' : 'Produk' }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

            {{-- ============ SEARCH WITH RESULTS ============ --}}
            @elseif ($umkms->isNotEmpty() || $products->isNotEmpty() || $categories->isNotEmpty())
                <p class="mt-5 text-sm text-[#8A7464]">
                    Menampilkan {{ $umkms->count() + $products->count() + $categories->count() }} hasil untuk
                    <span class="break-words font-medium text-[#3F2A22]">"{{ $query }}"</span>
                </p>

                @if ($categories->isNotEmpty())
                    <section class="mt-8">
                        <h2 class="text-xl font-semibold tracking-tight text-[#3F2A22]">Kategori</h2>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($categories as $category)
                                <a href="{{ $category->type === 'umkm' ? route('public.category.umkm', $category) : route('public.category.product', $category) }}" class="inline-flex min-h-11 items-center gap-2 rounded-full bg-[#F4EDE1] px-4 py-2 text-sm font-medium text-[#5C4033] transition duration-300 hover:bg-[#E8D8C8] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                    {{ $category->name }}
                                    <span class="text-xs font-semibold text-[#C26A4A]">{{ $category->type === 'umkm' ? 'UMKM' : 'Produk' }}</span>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($umkms->isNotEmpty())
                    <section class="mt-10">
                        <h2 class="text-xl font-semibold tracking-tight text-[#3F2A22]">UMKM</h2>
                        <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2">
                            @foreach ($umkms as $umkm)
                                <x-umkm-card :umkm="$umkm" variant="warm" />
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($products->isNotEmpty())
                    <section class="mt-10">
                        <h2 class="text-xl font-semibold tracking-tight text-[#3F2A22]">Produk</h2>
                        <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2">
                            @foreach ($products as $product)
                                <x-product-card :product="$product" variant="warm" />
                            @endforeach
                        </div>
                    </section>
                @endif

            {{-- ============ NO RESULTS ============ --}}
            @else
                <div class="mt-8">
                    <div class="mx-auto max-w-2xl text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]">
                            <svg class="h-6 w-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.3-4.3" />
                            </svg>
                        </div>
                        <h2 class="mt-4 text-xl font-semibold text-[#3F2A22]">Tidak menemukan hasil untuk "{{ $query }}"</h2>
                        <p class="mt-2 text-sm text-[#6F5D50]">Coba gunakan kata kunci lain atau lihat semua UMKM dan produk.</p>
                        <div class="mt-6 flex flex-col items-center justify-center gap-3 sm:flex-row">
                            <a href="{{ route('public.umkm.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#5C4033] px-5 py-2.5 text-sm font-semibold text-white transition duration-300 hover:bg-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                Lihat Semua UMKM
                            </a>
                            <a href="{{ route('public.product.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#3F2A22]/20 bg-white px-5 py-2.5 text-sm font-semibold text-[#3F2A22] transition duration-300 hover:border-[#3F2A22] hover:bg-[#FAF6F5] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#3F2A22] focus-visible:ring-offset-2">
                                Lihat Semua Produk
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ============ CTA ============ --}}
            <section class="mt-10 overflow-hidden rounded-2xl bg-[#E8D8C8] px-6 py-10 sm:px-8 sm:py-12">
                <div class="relative mx-auto max-w-2xl text-center">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-white/40 text-[#5C4033]">
                        <svg class="h-6 w-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                            <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                            <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                        </svg>
                    </span>
                    <h2 class="mt-4 text-xl font-bold tracking-tight text-[#3F2A22] sm:text-2xl">Punya produk unggulan?</h2>
                    <p class="mx-auto mt-2 max-w-xl text-sm text-[#6F5D50]">Daftarkan UMKM Anda dan tampilkan produk Anda kepada lebih banyak pelanggan di Desa Salamnunggal.</p>
                    <div class="mt-5 flex flex-col items-center justify-center gap-2.5 sm:flex-row">
                        <a href="{{ route('register') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#5C4033] px-6 text-sm font-semibold text-white shadow-sm transition-colors duration-150 hover:bg-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#C26A4A]">
                            Daftarkan UMKM
                            <svg class="h-4 w-4 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14" />
                                <path d="m12 5 7 7-7 7" />
                            </svg>
                        </a>
                        <a href="{{ route('public.umkm.index') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-[#3F2A22]/20 bg-white px-6 text-sm font-semibold text-[#3F2A22] transition-colors duration-150 hover:border-[#3F2A22] hover:bg-[#FAF6F5] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#3F2A22] focus-visible:ring-offset-2">
                            Jelajahi UMKM
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>