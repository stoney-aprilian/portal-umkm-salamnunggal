<x-app-layout title="Katalog UMKM">
    <div class="py-8 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- ============ PAGE HEADER ============ --}}
            <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-3 border-b border-[#E8D8C8] pb-6">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#C26A4A]">UMKM Salamnunggal</p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-[#3F2A22]">Temukan UMKM Lokal Salamnunggal</h1>
                    <p class="mt-2 max-w-2xl text-[#6F5D50]">Jelajahi usaha lokal, kenali produknya, dan temukan cara untuk menghubungi pemiliknya.</p>
                </div>
                @if ($umkms->isNotEmpty())
                    <p class="text-sm text-[#8A7464]">{{ $umkms->count() }} UMKM terverifikasi</p>
                @endif
            </div>

            {{-- ============ SEARCH & FILTER ============ --}}
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <form action="{{ route('public.search') }}" method="GET" class="flex w-full flex-1 items-center gap-2 rounded-xl border border-[#E8D8C8] bg-white px-3 transition-colors duration-150 focus-within:border-[#C26A4A] focus-within:ring-1 focus-within:ring-[#C26A4A]">
                    <label for="umkm-search" class="sr-only">Cari UMKM</label>
                    <svg class="h-4 w-4 shrink-0 text-[#A99A8C]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.3-4.3" />
                    </svg>
                    <input id="umkm-search" type="search" name="q" placeholder="Cari nama UMKM, produk, atau kategori..." class="w-full min-h-[48px] bg-transparent px-1 text-sm text-[#3F2A22] placeholder-[#A99A8C] outline-none">
                    <button type="submit" class="inline-flex min-h-[40px] shrink-0 items-center justify-center rounded-lg bg-[#5C4033] px-4 text-xs font-semibold text-white transition-colors duration-150 hover:bg-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        Cari
                    </button>
                </form>

                @php $categories = \App\Models\Category::where('type', 'umkm')->orderBy('name')->get(); @endphp
                @if ($categories->isNotEmpty())
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                        <a href="{{ route('public.umkm.index') }}" class="inline-flex min-h-[40px] shrink-0 items-center rounded-lg border border-[#3F2A22] bg-[#3F2A22] px-3 text-xs font-semibold text-white transition-colors duration-150 hover:bg-[#5C4033]">
                            Semua
                        </a>
                        @foreach ($categories as $category)
                            <a href="{{ route('public.category.umkm', $category) }}" class="inline-flex min-h-[40px] shrink-0 items-center rounded-lg border border-[#E8D8C8] bg-white px-3 text-xs font-medium text-[#5C4033] transition-colors duration-150 hover:border-[#C26A4A] hover:text-[#C26A4A]">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ============ RESULT COUNT ============ --}}
            @if ($umkms->isNotEmpty())
                <p class="mt-5 text-sm text-[#8A7464]">Menampilkan {{ $umkms->count() }} UMKM</p>
            @endif

            {{-- ============ UMKM DIRECTORY ============ --}}
            @if ($umkms->isNotEmpty())
                <div class="mt-4 divide-y divide-[#ECE5D9] rounded-2xl border border-[#ECE5D9] bg-white shadow-[0_2px_12px_rgba(63,42,34,0.06)]">
                    @foreach ($umkms as $umkm)
                        <x-umkm-card :umkm="$umkm" variant="list" />
                    @endforeach
                </div>
            @else
                <div class="mt-6 rounded-2xl bg-[#FAF6F5] px-6 py-10 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-[#E8D8C8]">
                        <svg class="h-6 w-6 text-[#5C4033]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                            <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                            <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                            <path d="M2 7h20" />
                        </svg>
                    </div>
                    <h2 class="mt-4 text-lg font-semibold tracking-tight text-[#3F2A22]">UMKM tidak ditemukan</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-[#6F5D50]">Belum ada UMKM yang sesuai dengan pencarian atau filter Anda.</p>
                    <a href="{{ route('public.umkm.index') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-[#5C4033] px-5 text-sm font-semibold text-white transition duration-300 hover:bg-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        Reset Filter
                    </a>
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
                    <h2 class="mt-4 text-xl font-bold tracking-tight text-[#3F2A22] sm:text-2xl">Punya usaha di Salamnunggal?</h2>
                    <p class="mx-auto mt-2 max-w-xl text-sm text-[#6F5D50]">Daftarkan UMKM Anda dan jangkau lebih banyak pelanggan melalui Portal UMKM Salamnunggal.</p>
                    <div class="mt-5 flex flex-col items-center justify-center gap-2.5 sm:flex-row">
                        <a href="{{ route('register') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#5C4033] px-6 text-sm font-semibold text-white shadow-sm transition-colors duration-150 hover:bg-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#C26A4A]">
                            Daftarkan UMKM
                            <svg class="h-4 w-4 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14" />
                                <path d="m12 5 7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>