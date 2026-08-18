<x-app-layout :title="$product->name">
    @php
        $photo = $product->media->first();
        $whatsapp = \App\Support\WhatsApp::waNumber($product->umkm->phone);
    @endphp

    <div class="py-8 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- ============ BREADCRUMB ============ --}}
            <a href="{{ route('public.product.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-[#C26A4A] hover:text-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                Kembali ke daftar Produk
            </a>

            {{-- ============ PRODUCT HERO ============ --}}
            <div class="mt-6 grid gap-6 lg:gap-10 lg:grid-cols-2">
                <div class="min-w-0">
                    @if ($photo)
                        <img src="{{ Storage::disk($photo->disk)->url($photo->path) }}" alt="Foto {{ $product->name }}" class="aspect-[4/3] w-full rounded-2xl object-cover">
                    @else
                        <div class="flex aspect-[4/3] w-full items-center justify-center rounded-2xl bg-[#F4EDE1] text-3xl font-semibold text-[#5C4033]">
                            {{ mb_strtoupper(mb_substr($product->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="min-w-0">
                    <h1 class="break-words text-2xl font-semibold leading-tight tracking-tight text-[#3F2A22] sm:text-3xl">{{ $product->name }}</h1>
                    <p class="mt-2 text-2xl font-semibold text-[#C26A4A]">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>

                    @if ($product->category)
                        <span class="mt-3 inline-flex items-center rounded-full bg-[#F4EDE1] px-3 py-1 text-xs font-semibold text-[#5C4033]">
                            {{ $product->category->name }}
                        </span>
                    @endif

                    @if ($product->description)
                        <p class="mt-4 max-w-2xl break-words whitespace-pre-line leading-relaxed text-[#6F5D50]">{{ $product->description }}</p>
                    @endif

                    <div class="mt-5 rounded-2xl border border-[#ECE5D9] bg-white p-4 shadow-[0_2px_12px_rgba(63,42,34,0.06)] sm:p-5">
                        <p class="text-xs font-semibold uppercase tracking-wider text-[#A99A8C]">Dijual oleh</p>
                        <div class="mt-2 flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#F4EDE1] text-sm font-semibold text-[#5C4033]">
                                {{ mb_strtoupper(mb_substr($product->umkm->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="break-words font-medium text-[#3F2A22]">{{ $product->umkm->name }}</p>
                                @if ($product->umkm->category)
                                    <p class="text-xs text-[#8A7464]">{{ $product->umkm->category->name }}</p>
                                @endif
                            </div>
                        </div>
                        @if ($product->umkm->address)
                            <p class="mt-2 flex items-center gap-2 text-sm text-[#6F5D50]">
                                <svg class="h-4 w-4 shrink-0 text-[#8A7464]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0Z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                                <span>{{ $product->umkm->address }}</span>
                            </p>
                        @endif
                        <a href="{{ route('public.umkm.show', $product->umkm) }}" class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-[#C26A4A] transition-colors duration-150 hover:text-[#5C4033] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            Lihat Profil UMKM
                            <svg class="h-4 w-4 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </a>
                    </div>

                    @if ($whatsapp !== '')
                        <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" class="mt-4 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-[#5C4033] px-6 text-sm font-semibold text-white transition duration-300 hover:bg-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" />
                            </svg>
                            Hubungi via WhatsApp
                        </a>
                    @endif
                </div>
            </div>

            {{-- ============ SIMILAR PRODUCTS ============ --}}
            @if ($similarProducts->isNotEmpty())
                @php
                    $cols = match ($similarProducts->count()) {
                        1 => 'max-w-md mx-auto',
                        2 => 'sm:grid-cols-2',
                        3 => 'sm:grid-cols-2 lg:grid-cols-3',
                        default => 'sm:grid-cols-2 lg:grid-cols-4',
                    };
                @endphp
                <section class="mt-12">
                    <h2 class="text-xl font-semibold tracking-tight text-[#3F2A22]">Produk Serupa</h2>
                    <div class="mt-4 grid grid-cols-1 gap-6 {{ $cols }}">
                        @foreach ($similarProducts as $similar)
                            <x-product-card :product="$similar" />
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- ============ CTA ============ --}}
            <section class="mt-12 overflow-hidden rounded-2xl bg-[#E8D8C8] px-6 py-10 sm:px-8 sm:py-12">
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