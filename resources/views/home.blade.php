<x-app-layout>
{{-- ============ HERO ============ --}}
    <section class="relative overflow-hidden bg-[#FAF6F5]">
        <svg class="pointer-events-none absolute -right-8 top-8 h-56 w-56 text-[#C26A4A] opacity-[0.18] lg:h-72 lg:w-56" aria-hidden="true" viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10 110C35 90 55 70 70 45S90 15 110 10" />
            <path d="M38 92c-2-14 6-26 18-30" />
            <path d="M58 66c-2-12 6-22 16-25" />
            <path d="M78 40c-2-10 4-18 12-20" />
            <path d="M35 84c-4 2-10 2-14-2" />
            <ellipse cx="54" cy="64" rx="3.5" ry="8" transform="rotate(-38 54 64)" />
            <ellipse cx="74" cy="38" rx="3" ry="7" transform="rotate(-40 74 38)" />
            <ellipse cx="37" cy="72" rx="3" ry="7" transform="rotate(-30 37 72)" />
        </svg>
        <div class="container-page relative pt-10 pb-14 sm:pt-12 sm:pb-20 lg:pt-12 lg:pb-24">
            <div class="grid items-center gap-12 lg:grid-cols-12">
                <div class="lg:col-span-7">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#C26A4A]">{{ $settings['site.tagline'] ?? 'Desa Salamnunggal' }}</p>
                    <h1 class="mt-3 max-w-2xl text-4xl font-bold leading-[1.1] tracking-tight text-[#3F2A22] sm:text-5xl">
                        {{ $settings['site.hero_title'] ?? 'Portal UMKM Desa Salamnunggal' }}
                    </h1>
                    <p class="mt-4 max-w-xl text-base leading-relaxed text-[#6F5D50] sm:text-lg">
                        {{ $settings['site.hero_description'] ?? 'Temukan UMKM serta produk unggulan dari Desa Salamnunggal. Jelajahi usaha lokal, lihat produknya, dan hubungi langsung pemiliknya.' }}
                    </p>

                    <form action="{{ route('public.search') }}" method="GET" class="mt-8 max-w-xl" role="search">
                        <label for="home-search" class="sr-only">Cari UMKM atau produk</label>
                        <div class="flex flex-col gap-2.5 sm:flex-row">
                            <div class="relative flex-1">
                                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-[#8A7464]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8" />
                                    <path d="m21 21-4.35-4.35" />
                                </svg>
                                <input id="home-search" type="search" name="q" placeholder="Cari UMKM atau produk..." value="{{ request('q') }}" autocomplete="off" class="w-full min-h-12 rounded-xl border border-[#E3D9CB] bg-white pl-12 pr-4 text-base text-[#3F2A22] placeholder-[#A99A8C] shadow-sm focus:border-[#C26A4A] focus:ring-[#C26A4A]">
                            </div>
                            <button type="submit" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-[#3F2A22] px-7 text-sm font-semibold text-white transition-colors duration-150 hover:bg-[#5C4033] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                Cari
                            </button>
                        </div>
                    </form>
                    <p class="mt-3 text-sm text-[#8A7464]">Contoh: kue, kerajinan, makanan, batik</p>

                    <div class="mt-5 flex items-start gap-2 text-sm text-[#6F5D50]">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-[#C26A4A]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>
                        <span>Setiap UMKM dan produk telah diverifikasi sebelum ditampilkan.</span>
                    </div>

                    <div class="mt-6 flex flex-col items-start gap-2 sm:flex-row sm:items-center sm:gap-6">
                        <a href="{{ route('public.umkm.index') }}" class="inline-flex min-h-11 items-center gap-1.5 rounded-full px-4 text-sm font-semibold text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] hover:text-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            Jelajahi UMKM
                            <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </a>
                        <span class="hidden h-4 w-px bg-[#E3D9CB] sm:block" aria-hidden="true"></span>
                        <a href="{{ route('public.product.index') }}" class="inline-flex min-h-11 items-center gap-1.5 rounded-full px-4 text-sm font-semibold text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] hover:text-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            Lihat Produk
                            <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Hero visual: main image + one elegant floating card --}}
                <div class="relative hidden lg:col-span-5 lg:block">
                    @php $heroUmkm = $featuredUmkms->first(); @endphp
                    @php $heroImage = $settings['site.hero_image'] ?? null; @endphp

                    {{-- Main hero image --}}
                    @if (!empty($heroImage))
                        <img src="{{ asset('storage/'.$heroImage) }}" alt="{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}" class="relative w-full rounded-2xl border border-[#ECE5D9] object-cover shadow-[0_12px_32px_-16px_rgba(63,42,34,0.3)]">
                    @endif

                    {{-- ONE elegant floating card (UMKM only, product card removed from hero) --}}
                    @if (!empty($heroImage) && !empty($heroUmkm))
                        <a href="{{ route('public.umkm.show', $heroUmkm) }}" class="relative block overflow-hidden rounded-2xl border border-[#ECE5D9] bg-white shadow-[0_12px_32px_-16px_rgba(63,42,34,0.3)] transition-shadow duration-300 hover:shadow-[0_16px_40px_-16px_rgba(63,42,34,0.45)] group-hover:shadow-[0_20px_56px_-12px_rgba(63,42,34,0.5)] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            {{-- Floating card body: subtle overlap above hero image --}}
                            <div class="relative z-10 pt-6 px-4 pb-8">
                                {{-- Logo/avatar left side --}}
                                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-[#F4EDE1] shrink-0">
                                    @php $heroLogo = $heroUmkm->media->first(); @endphp
                                    @if ($heroLogo)
                                        <img src="{{ Storage::disk($heroLogo->disk)->url($heroLogo->path) }}" alt="Logo {{ $heroUmkm->name }}" class="h-10 w-10 rounded-xl object-cover">
                                    @else
                                        <span class="flex h-10 w-10 items-center justify-center rounded-xl text-lg font-semibold text-[#5C4033]">{{ mb_strtoupper(mb_substr($heroUmkm->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                {{-- Name + category right side --}}
                                <div class="ml-4 min-w-0">
                                    <span class="block truncate text-sm font-bold text-[#3F2A22]">{{ $heroUmkm->name }}</span>
                                    @if ($heroUmkm->category)
                                        <span class="mt-0.5 block text-xs font-medium text-[#C26A4A]">{{ $heroUmkm->category->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @elseif (!empty($heroImage))
                        {{-- Fallback: just the image, no floating card when no featured UMKM --}}
                    @elseif (!empty($heroUmkm) && empty($heroImage))
                        {{-- Standalone UMKM card when no hero image --}}
                        <a href="{{ route('public.umkm.show', $heroUmkm) }}" class="relative block rounded-2xl border border-[#ECE5D9] bg-white p-6 pb-10 shadow-[0_12px_32px_-16px_rgba(63,42,34,0.3)] transition-shadow duration-300 hover:shadow-[0_16px_40px_-16px_rgba(63,42,34,0.4)] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            <div class="flex items-center gap-4">
                                @php $heroLogo = $heroUmkm->media->first(); @endphp
                                @if ($heroLogo)
                                    <img src="{{ Storage::disk($heroLogo->disk)->url($heroLogo->path) }}" alt="Logo {{ $heroUmkm->name }}" class="h-16 w-16 rounded-2xl object-cover">
                                @else
                                    <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-[#F4EDE1] text-xl font-semibold text-[#5C4033]">{{ mb_strtoupper(mb_substr($heroUmkm->name, 0, 1)) }}</span>
                                @endif
                                <div class="min-w-0">
                                    <span class="block truncate text-lg font-bold text-[#3F2A22]">{{ $heroUmkm->name }}</span>
                                    @if ($heroUmkm->category)
                                        <span class="mt-0.5 block text-sm font-medium text-[#C26A4A]">{{ $heroUmkm->category->name }}</span>
                                    @endif
                                    @if ($heroUmkm->address)
                                        <span class="mt-1 flex items-center gap-1 text-xs text-[#8A7464]">
                                            <svg class="h-3.5 w-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0Z" />
                                                <circle cx="12" cy="10" r="3" />
                                            </svg>
                                            <span class="truncate">{{ $heroUmkm->address }}</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endif
                </div>
</div>
</section>

    {{-- ============ STATISTICS ============ --}}
    <section class="container-page py-12 sm:py-16">
        <div class="grid gap-6 rounded-2xl border border-[#ECE5D9] bg-white p-6 shadow-[0_2px_12px_rgba(63,42,34,0.06)] sm:grid-cols-3 sm:gap-0 sm:divide-x sm:divide-[#ECE5D9] sm:p-8">
            <div class="flex items-center gap-4 sm:justify-center sm:px-6">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#F4EDE1] text-[#C26A4A]">
                    <svg class="h-6 w-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                        <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                        <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                    </svg>
                </span>
                <dl class="flex flex-col">
                    <dt class="order-2 mt-1 text-sm font-medium text-[#6F5D50]">UMKM Terdaftar<span class="block text-xs text-[#A99A8C]">Usaha lokal aktif</span></dt>
                    <dd class="order-1 text-3xl font-bold tracking-tight text-[#3F2A22] sm:text-4xl">{{ $umkmCount }}</dd>
                </dl>
            </div>
            <div class="flex items-center gap-4 sm:justify-center sm:px-6">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#F4EDE1] text-[#C26A4A]">
                    <svg class="h-6 w-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m7.5 4.27 9 5.15" />
                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                        <path d="m3.3 7 8.7 5 8.7-5" />
                        <path d="M12 22V12" />
                    </svg>
                </span>
                <dl class="flex flex-col">
                    <dt class="order-2 mt-1 text-sm font-medium text-[#6F5D50]">Produk Tersedia<span class="block text-xs text-[#A99A8C]">Produk lokal berkualitas</span></dt>
                    <dd class="order-1 text-3xl font-bold tracking-tight text-[#3F2A22] sm:text-4xl">{{ $productCount }}</dd>
                </dl>
            </div>
            <div class="flex items-center gap-4 sm:justify-center sm:px-6">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#F4EDE1] text-[#C26A4A]">
                    <svg class="h-6 w-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="7" height="7" x="3" y="3" rx="1" />
                        <rect width="7" height="7" x="14" y="3" rx="1" />
                        <rect width="7" height="7" x="14" y="14" rx="1" />
                        <rect width="7" height="7" x="3" y="14" rx="1" />
                    </svg>
                </span>
                <dl class="flex flex-col">
                    <dt class="order-2 mt-1 text-sm font-medium text-[#6F5D50]">Kategori<span class="block text-xs text-[#A99A8C]">Bidang usaha</span></dt>
                    <dd class="order-1 text-3xl font-bold tracking-tight text-[#3F2A22] sm:text-4xl">{{ $categoryCount }}</dd>
                </dl>
            </div>
        </div>
    </section>

    {{-- ============ CATEGORIES ============ --}}
    @if ($categories->isNotEmpty())
        <section class="bg-[#FAF6F5]">
            <div class="container-page py-12 sm:py-14">
                <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#C26A4A]">Jelajahi</p>
                        <h2 class="mt-1 text-xl font-bold tracking-tight text-[#3F2A22] sm:text-2xl">Kategori UMKM</h2>
                        <p class="mt-1 text-sm text-[#6F5D50]">Temukan usaha lokal berdasarkan kategori</p>
                    </div>
                    <a href="{{ route('public.umkm.index') }}" class="flex items-center gap-2 rounded-full bg-[#3F2A22]/10 px-4 text-sm font-medium text-[#3F2A22] transition-colors duration-150 hover:bg-[#3F2A22]/20 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        Lihat Semua 
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="m12 5 7 7-7 7" />
                        </svg>
                    </a>
                </div>
                <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($categories as $category)
                        <a href="{{ route('public.category.umkm', $category) }}" class="group flex flex-col items-center gap-3 rounded-xl border border-[#ECE5D9] bg-white p-5 py-6 transition-colors duration-200 hover:translate-y-[-2px] hover:shadow-[0_4px_12px_rgba(63,42,34,0.2)] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            <span class="relative flex h-10 w-10 items-center justify-center rounded-xl mb-3 bg-[#F4EDE1] text-[#5C4033]">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                                    <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                                    <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                                </svg>
                                <span class="absolute -top-0.5 -left-0.5 text-xs font-bold text-[#3F2A22]">{{ mb_strtoupper(mb_substr($category->name, 0, 1)) }}</span>
                            </span>
                            <span class="text-center text-sm font-semibold text-[#3F2A22]">{{ $category->name }}</span>
                            <span class="text-xs text-[#8A7464]">{{ $category->umkms->count() }} usaha</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ FEATURED UMKM ============ --}}
    @if ($featuredUmkms->isNotEmpty())
        @php
            $umkmCount = $featuredUmkms->count();
            $umkmCols = match (true) {
                $umkmCount === 1 => 'lg:grid-cols-2',
                $umkmCount === 2 => 'lg:grid-cols-2',
                default => 'lg:grid-cols-3',
            };
            $umkmSpan = $umkmCount === 1 ? 'lg:col-span-2' : '';
        @endphp
        <section class="relative py-16 sm:py-20 bg-[#FAF6F5]">
            {{-- Banner hero: general Salamnunggal visual, not product-specific --}}
            <div class="relative overflow-hidden rounded-2xl border border-[#ECE5D9] mb-10 sm:mb-16 lg:mb-20">
                @php $heroImage = $settings['site.hero_image'] ?? null; @endphp
                @if (!empty($heroImage))
                    <img src="{{ asset('storage/'.$heroImage) }}" alt="Salamnunggal UMKM" class="w-full h-64 sm:h-80 lg:h-96 object-cover object-center rounded-2xl">
                @else
                    {{-- Fallback: subtle pattern/accent when no hero image --}}
                    <div class="absolute inset-0 bg-gradient-to-b from-[#FAF6F5] to-[#F0E8E0]">
                        <svg class="pointer-events-none absolute -right-8 top-8 h-48 w-48 text-[#C26A4A] opacity-[0.15]" viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 110C35 90 55 70 70 45S90 15 110 10" />
                            <path d="M38 92c-2-14 6-26 18-30" />
                            <path d="M58 66c-2-12 6-22 16-25" />
                            <path d="M78 40c-2-10 4-18 12-20" />
                            <path d="M35 84c-4 2-10 2-14-2" />
                            <ellipse cx="54" cy="64" rx="3.5" ry="8" transform="rotate(-38 54 64)" />
                            <ellipse cx="74" cy="38" rx="3" ry="7" transform="rotate(-40 74 38)" />
                            <ellipse cx="37" cy="72" rx="3" ry="7" transform="rotate(-30 37 72)" />
                        </svg>
                    </div>
                @endif
            </div>

            {{-- UMKM PILIHAN label + heading --}}
            <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-4 max-w-2xl mx-auto">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#C26A4A]">Direktori</p>
                    <h2 class="mt-1 text-2xl font-bold tracking-tight text-[#3F2A22] sm:text-3xl lg:mt-0">UMKM Unggulan</h2>
                    <p class="mt-1 text-sm text-[#6F5D50] sm:mt-0">10 UMKM terverifikasi terbaik Desa Salamnunggal</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 rounded-full bg-[#3F2A22]/10 px-3 py-1.5 text-sm font-medium text-[#3F2A22]">
                        <svg class="h-4 w-4 text-[#C26A4A]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                            <path d="m9 12 2 2 4-4" />
                        </span>
                        UMKM PILIHAN
                    </span>
                </div>
            </div>

            {{-- Featured cards: compact, subtle overlap with banner --}}
            <div class="relative z-10 mt-8 lg:mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 {{ $umkmCols }}">
                @foreach ($featuredUmkms as $umkm)
                    <div class="lg:col-span-{{ $umkmSpan }}">
                        <x-umkm-card :umkm="$umkm" variant="warm" class="group relative rounded-2xl border border-[#ECE5D9] bg-white p-4 pb-6 shadow-[0_4px_12px_rgba(63,42,34,0.2)] transition-shadow duration-200 hover:shadow-[0_8px_20px_rgba(63,42,34,0.3)] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            {{-- Badge/label left side --}}
                            <div class="flex h-8 w-8 items-center justify-center rounded-xl mb-3 bg-[#F4EDE1] text-[#5C4033] shrink-0">
                                @php $umkmLogo = $umkm->media->first(); @endphp
                                @if ($umkmLogo)
                                    <img src="{{ Storage::disk($umkmLogo->disk)->url($umkmLogo->path) }}" alt="Logo {{ $umkm->name }}" class="h-6 w-6 rounded-xl object-cover">
                                @else
                                    <span class="flex h-6 w-6 items-center justify-center rounded-xl text-lg font-semibold text-[#5C4033]">{{ mb_strtoupper(mb_substr($umkm->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            {{-- Content: logo, name, category, location --}}
                            <div class="min-w-0">
                                <span class="block truncate text-sm font-bold text-[#3F2A22]">{{ $umkm->name }}</span>
                                @if ($umkm->category)
                                    <span class="mt-1 flex items-center gap-1 text-xs text-[#C26A4A]">
                                        <svg class="h-2.5 w-2.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0Z" />
                                            <circle cx="12" cy="10" r="3" />
                                        </svg>
                                        <span>{{ $umkm->category->name }}</span>
                                    </span>
                                @endif
                                @if ($umkm->address)
                                    <span class="mt-2 flex items-center gap-1 text-xs text-[#8A7464]">
                                        <svg class="h-3.5 w-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0Z" />
                                            <circle cx="12" cy="10" r="3" />
                                        </svg>
                                        <span>{{ $umkm->address }}</span>
                                    </span>
                                @endif
                                {{-- CTA: visit UMKM page --}}
                                <a href="{{ route('public.umkm.show', $umkm) }}" class="mt-3 inline-flex items-center gap-1.5 rounded-full border border-[#3F2A22]/20 px-4 text-sm font-semibold text-[#3F2A22] transition-colors duration-150 hover:bg-[#3F2A22]/10 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m9 18 6-6-6-6" />
                                    </svg>
                                    Baca Profil
                                </a>
                            </div>
                        </x-umkm-card>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ FEATURED PRODUCTS ============ --}}
    @if ($featuredProducts->isNotEmpty())
        @php
            $cols = match ($featuredProducts->count()) {
                1 => 'max-w-md mx-auto',
                2 => 'sm:grid-cols-2',
                default => 'sm:grid-cols-2 lg:grid-cols-3',
            };
        @endphp
        <section class="bg-[#FAF6F5]">
            <div class="container-page py-16 sm:py-20">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#C26A4A]">Produk</p>
                        <h2 class="mt-2 text-2xl font-bold tracking-tight text-[#3F2A22] sm:text-3xl">Produk Unggulan</h2>
                    </div>
                    <a href="{{ route('public.product.index') }}" class="group inline-flex min-h-11 items-center gap-1.5 rounded-full px-4 text-sm font-semibold text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] hover:text-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        Lihat Semua Produk
                        <svg class="h-4 w-4 transition-transform duration-150 group-hover:translate-x-0.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="m12 5 7 7-7 7" />
                        </svg>
                    </a>
                </div>
                <div class="mt-8 grid grid-cols-1 gap-6 {{ $cols }}">
                    @foreach ($featuredProducts as $product)
                        <x-product-card :product="$product" variant="warm" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ ABOUT ============ --}}
    <section>
        <div class="container-page py-16 sm:py-20">
            <div class="grid items-center gap-10 lg:grid-cols-12">
                <div class="lg:col-span-7">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#C26A4A]">Tentang Portal</p>
                    <h2 class="mt-2 text-2xl font-bold tracking-tight text-[#3F2A22] sm:text-3xl">Portal Digital Resmi UMKM Desa Salamnunggal</h2>
                    <p class="mt-4 max-w-xl text-base leading-relaxed text-[#6F5D50]">
                        Setiap UMKM dan produk ditampilkan setelah melalui pemeriksaan administrator, sehingga informasi yang Anda temukan berasal dari sumber resmi desa dan dapat dipercaya.
                    </p>
                    @if (!empty($settings['contact.address']))
                        <p class="mt-3 flex items-center gap-2 text-sm text-[#8A7464]">
                            <svg class="h-4 w-4 shrink-0 text-[#C26A4A]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0Z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                            {{ $settings['contact.address'] }}
                        </p>
                    @endif
                    <a href="{{ route('public.about') }}" class="group mt-6 inline-flex min-h-11 items-center gap-1.5 rounded-full px-4 text-sm font-semibold text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] hover:text-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        Pelajari lebih lanjut
                        <svg class="h-4 w-4 transition-transform duration-150 group-hover:translate-x-0.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="m12 5 7 7-7 7" />
                        </svg>
                    </a>
                </div>
                <div class="hidden lg:col-span-5 lg:block">
                    <div class="relative overflow-hidden rounded-2xl border border-[#ECE5D9] bg-[#FAF6F5] p-10">
                        <svg class="pointer-events-none absolute -right-6 -top-6 h-40 w-40 text-[#C26A4A] opacity-[0.2]" aria-hidden="true" viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 110C35 90 55 70 70 45S90 15 110 10" />
                            <path d="M38 92c-2-14 6-26 18-30" />
                            <path d="M58 66c-2-12 6-22 16-25" />
                            <ellipse cx="54" cy="64" rx="3.5" ry="8" transform="rotate(-38 54 64)" />
                            <ellipse cx="74" cy="38" rx="3" ry="7" transform="rotate(-40 74 38)" />
                        </svg>
                        <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-[#5C4033] text-white">
                            <x-application-logo class="h-8 w-8" />
                        </span>
                        <p class="mt-6 text-lg font-bold text-[#3F2A22]">{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}</p>
                        <p class="mt-1 text-sm text-[#8A7464]">{{ $settings['site.tagline'] ?? 'Desa Salamnunggal' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="container-page py-10 sm:py-14 bg-[#C26A4A]">
        {{-- Small decorative accent: minimal line illustration --}}
        <div class="relative overflow-hidden rounded-2xl border border-white/10 mb-8 sm:mb-10">
            <svg class="pointer-events-none absolute -right-6 -top-6 h-24 w-24 text-white opacity-[0.08]" aria-hidden="true" viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10 110C35 90 55 70 70 45S90 15 110 10" />
                <path d="M38 92c-2-14 6-26 18-30" />
                <path d="M58 66c-2-12 6-22 16-25" />
            </svg>
        </div>

        {{-- Content area: warm cream card on terracotta bg --}}
        <div class="relative mx-auto max-w-2xl text-center px-4 py-8 sm:px-6 sm:py-10">
            {{-- Heading: dominant, 1-2 lines only --}}
            <h2 class="text-2xl lg:text-3xl font-bold tracking-tight text-white mb-2">Punya usaha di Desa Salamnunggal?</h2>
            {{-- Description: concise, action-oriented --}}
            <p class="text-base lg:text-lg text-white/80 mb-6 max-w-xl mx-auto">Daftarkan UMKM Anda dan jadilihenalih terlihat oleh masyarakat desa dan pengunjung portal.</p>

            {{-- CTA buttons: primary more prominent, secondary subtle --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
                {{-- Primary CTA: "Daftarkan UMKM" --}}
                <a href="{{ route('register') }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-white px-7 text-sm font-semibold text-[#C26A4A] shadow-md transition-colors duration-150 hover:bg-[#FAF6F5] focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#C26A4A]">
                    <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                        <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                        <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                    </svg>
                    Daftarkan UMKM
                </a>

                {{-- Secondary CTA: "Sudah punya akun? Masuk" --}}
                <a href="{{ route('login') }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl border border-white/20 px-7 text-sm font-semibold text-white transition-colors duration-150 hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#C26A4A]">
                    <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14" />
                        <path d="m12 5 7 7-7 7" />
                    </svg>
                    Sudah punya akun? Masuk
                </a>
            </div>
        </div>
    </section>
</x-app-layout>