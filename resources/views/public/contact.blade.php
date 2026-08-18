<x-app-layout title="Kontak">
    @php
        $hasContact = $settings->has('contact.address')
            || $settings->has('contact.phone')
            || $settings->has('contact.whatsapp')
            || $settings->has('contact.email')
            || $settings->has('contact.website')
            || $settings->has('contact.hours');
    @endphp

    <div class="py-8 sm:py-10">
        <div class="container-page">
            {{-- ============ PAGE HEADER ============ --}}
            <div class="border-b border-[#E8D8C8] pb-6">
                <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-3">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#C26A4A]">Kontak</p>
                        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-[#3F2A22]">Hubungi Kami</h1>
                        <p class="mt-2 max-w-2xl text-[#6F5D50]">Hubungi pengelola Portal UMKM Desa Salamnunggal untuk informasi lebih lanjut, pendataan usaha, atau pendampingan.</p>
                    </div>
                </div>
            </div>

            {{-- ============ CONTACT INFORMATION ============ --}}
            @if ($hasContact)
                <div class="mt-8 grid gap-5 sm:grid-cols-2">
                    @if ($settings->get('contact.whatsapp'))
                        <a href="https://wa.me/{{ \App\Support\WhatsApp::waNumber($settings->get('contact.whatsapp')) }}" target="_blank" rel="noopener noreferrer" class="flex items-start gap-4 rounded-2xl border border-[#ECE5D9] bg-white p-5 shadow-[0_2px_12px_rgba(63,42,34,0.06)] transition-colors duration-150 hover:border-[#C26A4A] hover:bg-[#FAF6F5] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#C26A4A]">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-[#3F2A22]">WhatsApp</p>
                                <p class="mt-1 text-sm text-[#C26A4A]">{{ $settings->get('contact.whatsapp') }}</p>
                            </div>
                        </a>
                    @endif

                    @if ($settings->get('contact.phone'))
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $settings->get('contact.phone')) }}" class="flex items-start gap-4 rounded-2xl border border-[#ECE5D9] bg-white p-5 shadow-[0_2px_12px_rgba(63,42,34,0.06)] transition-colors duration-150 hover:border-[#C26A4A] hover:bg-[#FAF6F5] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-[#3F2A22]">Telepon</p>
                                <p class="mt-1 text-sm text-[#5C4033]">{{ $settings->get('contact.phone') }}</p>
                            </div>
                        </a>
                    @endif

                    @if ($settings->get('contact.email'))
                        <a href="mailto:{{ $settings->get('contact.email') }}" class="flex items-start gap-4 rounded-2xl border border-[#ECE5D9] bg-white p-5 shadow-[0_2px_12px_rgba(63,42,34,0.06)] transition-colors duration-150 hover:border-[#C26A4A] hover:bg-[#FAF6F5] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="4" width="20" height="16" rx="2" />
                                    <path d="m22 7-10 6L2 7" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-[#3F2A22]">Email</p>
                                <p class="mt-1 text-sm text-[#5C4033]">{{ $settings->get('contact.email') }}</p>
                            </div>
                        </a>
                    @endif

                    @if ($settings->get('contact.address'))
                        <div class="flex items-start gap-4 rounded-2xl border border-[#ECE5D9] bg-white p-5 shadow-[0_2px_12px_rgba(63,42,34,0.06)]">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0Z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-[#3F2A22]">Alamat</p>
                                <p class="mt-1 text-sm text-[#5C4033]">{{ $settings->get('contact.address') }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($settings->get('contact.hours'))
                        <div class="flex items-start gap-4 rounded-2xl border border-[#ECE5D9] bg-white p-5 shadow-[0_2px_12px_rgba(63,42,34,0.06)]">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M12 6v6l4 2" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-[#3F2A22]">Jam Layanan</p>
                                <p class="mt-1 text-sm text-[#5C4033]">{{ $settings->get('contact.hours') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="mt-8 rounded-2xl bg-[#FAF6F5] px-6 py-10 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-[#E8D8C8]">
                        <svg class="h-6 w-6 text-[#5C4033]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2" />
                            <path d="m22 7-10 6L2 7" />
                        </svg>
                    </div>
                    <h2 class="mt-4 text-lg font-semibold tracking-tight text-[#3F2A22]">Informasi kontak belum tersedia.</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-[#6F5D50]">Pengelola belum melengkapi informasi kontak. Anda tetap dapat menjelajahi UMKM dan produk di portal ini.</p>
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
                    <h2 class="mt-4 text-xl font-bold tracking-tight text-[#3F2A22] sm:text-2xl">Jelajahi UMKM dan Produk</h2>
                    <p class="mx-auto mt-2 max-w-xl text-sm text-[#6F5D50]">Temukan usaha lokal dan produk unggulan Desa Salamnunggal di portal ini.</p>
                    <div class="mt-5 flex flex-col items-center justify-center gap-2.5 sm:flex-row">
                        <a href="{{ route('public.umkm.index') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#5C4033] px-6 text-sm font-semibold text-white shadow-sm transition-colors duration-150 hover:bg-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#C26A4A]">
                            Jelajahi UMKM
                        </a>
                        <a href="{{ route('public.product.index') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-[#3F2A22]/20 bg-white px-6 text-sm font-semibold text-[#3F2A22] transition-colors duration-150 hover:border-[#3F2A22] hover:bg-[#FAF6F5] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#3F2A22] focus-visible:ring-offset-2">
                            Lihat Produk
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>