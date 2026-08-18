<x-app-layout :title="$umkm->name">
    @php
        $banner = $umkm->media->firstWhere('collection', 'banner');
        $logo = $umkm->media->firstWhere('collection', 'logo');
        $gallery = $umkm->media->where('collection', 'gallery')->values();
        $whatsapp = \App\Support\WhatsApp::waNumber($umkm->phone);
        $hasAsideInfo = $umkm->operational_hours
            || $umkm->phone || $umkm->email || $umkm->website
            || $umkm->instagram || $umkm->facebook || $umkm->tiktok;
    @endphp

    <div class="py-8 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- ============ BREADCRUMB ============ --}}
            <a href="{{ route('public.umkm.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-[#C26A4A] hover:text-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                Kembali ke daftar UMKM
            </a>

            {{-- ============ BANNER ============ --}}
            @if ($banner)
                <div class="mt-4 overflow-hidden rounded-2xl">
                    <img src="{{ Storage::disk($banner->disk)->url($banner->path) }}" alt="Banner {{ $umkm->name }}" class="aspect-[3/1] w-full object-cover sm:aspect-[5/1]">
                </div>
            @endif

            {{-- ============ PROFILE HEADER ============ --}}
            <div class="mt-6 rounded-2xl border border-[#ECE5D9] bg-white p-5 shadow-[0_2px_12px_rgba(63,42,34,0.06)] sm:p-6">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:gap-6">
                    <div class="flex items-center gap-4">
                        @if ($logo)
                            <img src="{{ Storage::disk($logo->disk)->url($logo->path) }}" alt="Logo {{ $umkm->name }}" class="h-14 w-14 shrink-0 rounded-xl object-cover sm:h-16 sm:w-16">
                        @else
                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-[#F4EDE1] text-2xl font-semibold text-[#5C4033] sm:h-16 sm:w-16">
                                {{ mb_strtoupper(mb_substr($umkm->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <h1 class="text-2xl font-semibold leading-tight text-[#3F2A22] sm:text-3xl">{{ $umkm->name }}</h1>
                            <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1.5">
                                @if ($umkm->category)
                                    <span class="text-sm font-medium text-[#C26A4A]">{{ $umkm->category->name }}</span>
                                @endif
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                    <svg class="h-3.5 w-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 12l2 2 4-4" />
                                        <circle cx="12" cy="12" r="10" />
                                    </svg>
                                    Terverifikasi
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3 sm:ml-auto sm:items-end">
                        @if ($umkm->address)
                            <p class="flex items-center gap-2 text-sm text-[#6F5D50]">
                                <svg class="h-4 w-4 shrink-0 text-[#8A7464]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0Z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                                <span class="min-w-0">{{ $umkm->address }}</span>
                            </p>
                        @endif
                        <div class="flex flex-wrap items-center gap-2">
                            @if ($whatsapp !== '')
                                <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#5C4033] px-5 text-sm font-semibold text-white transition duration-300 hover:bg-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                    <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" />
                                    </svg>
                                    Hubungi via WhatsApp
                                </a>
                            @endif
                            @if ($umkm->phone)
                                <button type="button" data-phone="{{ $umkm->phone }}" class="copy-contact inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-[#E8D8C8] bg-white px-4 text-sm font-semibold text-[#5C4033] transition-colors duration-150 hover:border-[#C26A4A] hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                    <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1" />
                                    </svg>
                                    Salin Kontak
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ MAIN CONTENT ============ --}}
            <div class="mt-8 grid gap-8 lg:grid-cols-3">
                <div class="min-w-0 lg:col-span-2">
                    @if ($umkm->description)
                        <section>
                            <h2 class="text-xl font-semibold tracking-tight text-[#3F2A22]">Tentang {{ $umkm->name }}</h2>
                            <div class="prose prose-slate mt-3 max-w-none text-[#6F5D50]">
                                @foreach(explode("\n", $umkm->description) as $paragraph)
                                    @if(trim($paragraph) !== '')
                                        <p class="mb-3 leading-relaxed">{{ $paragraph }}</p>
                                    @endif
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($gallery->isNotEmpty())
                        <section class="mt-10">
                            <h2 class="text-xl font-semibold tracking-tight text-[#3F2A22]">Galeri</h2>
                            <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4">
                                @foreach ($gallery as $image)
                                    <img src="{{ Storage::disk($image->disk)->url($image->path) }}" alt="Galeri {{ $umkm->name }}" class="aspect-[4/3] w-full rounded-xl object-cover">
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($umkm->products->isNotEmpty())
                        <section class="mt-10">
                            <div class="flex items-center justify-between gap-4">
                                <h2 class="text-xl font-semibold tracking-tight text-[#3F2A22]">Produk dari UMKM ini</h2>
                                <a href="{{ route('public.product.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-[#C26A4A] transition-colors duration-150 hover:text-[#5C4033] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                    Lihat semua produk
                                    <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12h14" />
                                        <path d="m12 5 7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($umkm->products as $product)
                                    <x-product-card :product="$product" variant="warm" />
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($umkm->google_maps)
                        <section class="mt-10">
                            <h2 class="text-xl font-semibold tracking-tight text-[#3F2A22]">Lokasi</h2>
                            <div class="mt-3 space-y-3">
                                @if ($umkm->address)
                                    <p class="flex items-center gap-2 text-sm text-[#6F5D50]">
                                        <svg class="h-4 w-4 shrink-0 text-[#8A7464]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0Z" />
                                            <circle cx="12" cy="10" r="3" />
                                        </svg>
                                        <span>{{ $umkm->address }}</span>
                                    </p>
                                @endif
                                <a href="{{ $umkm->google_maps }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sm font-medium text-[#C26A4A] hover:text-[#5C4033] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                    <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0Z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                    Lihat Lokasi di Google Maps
                                </a>
                            </div>
                        </section>
                    @endif
                </div>

                @if ($hasAsideInfo)
                    <aside class="min-w-0">
                        <div class="lg:sticky lg:top-24">
                            <div class="rounded-2xl border border-[#ECE5D9] bg-white p-5 shadow-[0_2px_12px_rgba(63,42,34,0.06)] sm:p-6">
                                <h2 class="text-lg font-semibold tracking-tight text-[#3F2A22]">Informasi Usaha</h2>
                                <div class="mt-4 space-y-4">
                                    @if ($umkm->operational_hours)
                                        <div class="flex items-start gap-3">
                                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-[#8A7464]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="9" />
                                                <path d="M12 7v5l3 3" />
                                            </svg>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-[#3F2A22]">Jam Operasional</p>
                                                <p class="mt-1 whitespace-pre-line text-sm leading-relaxed text-[#6F5D50]">{{ $umkm->operational_hours }}</p>
                                            </div>
                                        </div>
                                        <div class="border-t border-[#E8D8C8]"></div>
                                    @endif

                                    @if ($umkm->phone || $umkm->email || $umkm->website)
                                        <div class="flex items-start gap-3">
                                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-[#8A7464]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                                            </svg>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-semibold text-[#3F2A22]">Kontak</p>
                                                <ul class="mt-1 space-y-1 break-words text-sm leading-relaxed text-[#6F5D50]">
                                                    @if ($umkm->phone)
                                                        <li><a href="tel:{{ $umkm->phone }}" class="hover:text-[#3F2A22] focus:outline-none focus-visible:underline">{{ $umkm->phone }}</a></li>
                                                    @endif
                                                    @if ($umkm->email)
                                                        <li><a href="mailto:{{ $umkm->email }}" class="hover:text-[#3F2A22] focus:outline-none focus-visible:underline">{{ $umkm->email }}</a></li>
                                                    @endif
                                                    @if ($umkm->website)
                                                        <li><a href="{{ $umkm->website }}" target="_blank" rel="noopener" class="hover:text-[#3F2A22] focus:outline-none focus-visible:underline">{{ $umkm->website }}</a></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="border-t border-[#E8D8C8]"></div>
                                    @endif

                                    @if ($umkm->instagram || $umkm->facebook || $umkm->tiktok)
                                        <div class="flex items-start gap-3">
                                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-[#8A7464]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="4" />
                                                <path d="M16 8v-2a2 2 0 0 1 2-2h1" />
                                                <path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-4 8" />
                                            </svg>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-semibold text-[#3F2A22]">Media Sosial</p>
                                                <ul class="mt-1 space-y-1 break-words text-sm leading-relaxed text-[#6F5D50]">
                                                    @if ($umkm->instagram)
                                                        <li>Instagram: {{ $umkm->instagram }}</li>
                                                    @endif
                                                    @if ($umkm->facebook)
                                                        <li>Facebook: {{ $umkm->facebook }}</li>
                                                    @endif
                                                    @if ($umkm->tiktok)
                                                        <li>TikTok: {{ $umkm->tiktok }}</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </aside>
                @endif
            </div>

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

            {{-- ============ SIMILAR UMKM ============ --}}
            @if ($similarUmkms->isNotEmpty())
                @php
                    $cols = match ($similarUmkms->count()) {
                        1 => 'max-w-md mx-auto',
                        2 => 'sm:grid-cols-2',
                        3 => 'sm:grid-cols-2 lg:grid-cols-3',
                        default => 'sm:grid-cols-2 lg:grid-cols-4',
                    };
                @endphp
                <section class="mt-12">
                    <h2 class="text-xl font-semibold tracking-tight text-[#3F2A22]">UMKM Serupa</h2>
                    <div class="mt-4 grid grid-cols-1 gap-6 {{ $cols }}">
                        @foreach ($similarUmkms as $similar)
                            <x-umkm-card :umkm="$similar" variant="warm" />
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>

    @push('scripts')
    @if ($umkm->phone)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.copy-contact').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const phone = this.dataset.phone;
                    navigator.clipboard.writeText(phone).then(function() {
                        const original = btn.innerHTML;
                        btn.innerHTML = '<svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg> Tersalin';
                        setTimeout(function() {
                            btn.innerHTML = original;
                        }, 2000);
                    });
                });
            });
        });
    </script>
    @endif
    @endpush
</x-app-layout>