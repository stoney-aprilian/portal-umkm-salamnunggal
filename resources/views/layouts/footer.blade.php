@php
    $isPublicContext = ! Auth::user()?->hasRole('administrator')
        && (Auth::guest() || request()->routeIs('home') || request()->routeIs('public.*'));
@endphp

@if ($isPublicContext)
    {{-- ============ PUBLIC FOOTER (guest + authenticated user on public pages) ============ --}}
    @php
        $siteName = $settings['site.name'] ?? 'Portal UMKM Salamnunggal';
        $siteTagline = $settings['site.tagline'] ?? 'Desa Salamnunggal';
        $siteDescription = $settings['site.description'] ?? 'Media promosi dan informasi UMKM Desa Salamnunggal untuk mempertemukan masyarakat dengan pelaku usaha lokal.';
        $address = $settings['contact.address'] ?? null;
        $phone = $settings['contact.phone'] ?? null;
        $whatsapp = $settings['contact.whatsapp'] ?? null;
        $email = $settings['contact.email'] ?? null;
        $hours = $settings['contact.hours'] ?? null;
        $mapsUrl = $settings['contact.maps_url'] ?? null;
        $instagram = $settings['social.instagram'] ?? null;
        $facebook = $settings['social.facebook'] ?? null;
        $whatsappDigits = $whatsapp ? preg_replace('/\D+/', '', $whatsapp) : null;
        $hasContactRows = $address || $phone || $email || $hours;
    @endphp

    <footer class="container-page mt-12 pb-6 sm:pb-8">
        <div class="relative overflow-hidden rounded-3xl bg-[#3F2A22] shadow-[0_24px_60px_-24px_rgba(63,42,34,0.6)]">
            <!-- Brand watermark -->
            <span aria-hidden="true" class="pointer-events-none absolute -bottom-16 -right-16 h-72 w-72 text-[#E8D8C8] opacity-[0.04]">
                <x-application-logo class="h-full w-full" />
            </span>

            <!-- ===== Desktop: multi-column ===== -->
            <div class="relative hidden gap-10 px-6 py-10 sm:px-10 sm:py-12 lg:grid lg:grid-cols-12 lg:gap-8 lg:px-12 lg:py-14">
                <!-- Brand Area -->
                <div class="lg:col-span-4">
                    <div class="flex items-center gap-3">
                        @if (!empty($settings['site.logo']))
                            <img src="{{ asset('storage/'.$settings['site.logo']) }}" alt="{{ $siteName }}" class="h-12 w-12 shrink-0 rounded-2xl object-contain">
                        @else
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#5C4033] text-[#FAF6F5]">
                                <x-application-logo class="h-6 w-6" />
                            </span>
                        @endif
                        <span class="flex flex-col justify-center leading-tight">
                            <span class="text-lg font-bold tracking-tight text-[#FAF6F5]">{{ $siteName }}</span>
                            <span class="text-sm font-medium text-[#E8D8C8]/70">{{ $siteTagline }}</span>
                        </span>
                    </div>
                    <div class="mt-4 h-0.5 w-10 bg-[#C26A4A]" aria-hidden="true"></div>
                    <p class="mt-4 max-w-sm text-sm leading-relaxed text-[#E8D8C8]/75">{{ $siteDescription }}</p>
                    <div class="mt-5 flex items-center gap-2.5">
                        @if ($whatsappDigits)
                            <a href="https://wa.me/{{ $whatsappDigits }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp {{ $siteName }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-[#E8D8C8]/25 text-[#E8D8C8] transition-colors duration-150 hover:border-[#C26A4A] hover:bg-[#C26A4A] hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 focus-visible:ring-offset-[#3F2A22]">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z" />
                                </svg>
                            </a>
                        @endif
                        @if ($instagram)
                            <a href="{{ $instagram }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram {{ $siteName }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-[#E8D8C8]/25 text-[#E8D8C8] transition-colors duration-150 hover:border-[#C26A4A] hover:bg-[#C26A4A] hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 focus-visible:ring-offset-[#3F2A22]">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="20" height="20" x="2" y="2" rx="5" ry="5" />
                                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                                    <line x1="17.5" x2="17.51" y1="6.5" y2="6.5" />
                                </svg>
                            </a>
                        @endif
                        @if ($facebook)
                            <a href="{{ $facebook }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook {{ $siteName }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-[#E8D8C8]/25 text-[#E8D8C8] transition-colors duration-150 hover:border-[#C26A4A] hover:bg-[#C26A4A] hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 focus-visible:ring-offset-[#3F2A22]">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                </svg>
                            </a>
                        @endif
                        @if ($mapsUrl)
                            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Lokasi {{ $siteName }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-[#E8D8C8]/25 text-[#E8D8C8] transition-colors duration-150 hover:border-[#C26A4A] hover:bg-[#C26A4A] hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 focus-visible:ring-offset-[#3F2A22]">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Jelajahi -->
                <nav aria-label="Jelajahi" class="lg:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#FAF6F5]">Jelajahi</p>
                    <div class="mt-2.5 h-0.5 w-8 bg-[#C26A4A]" aria-hidden="true"></div>
                    <ul class="mt-4 space-y-0.5">
                        <li><a href="{{ url('/') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Beranda</a></li>
                        <li><a href="{{ route('public.umkm.index') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">UMKM</a></li>
                        <li><a href="{{ route('public.product.index') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Produk</a></li>
                        <li><a href="{{ route('public.search') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Cari</a></li>
                    </ul>
                </nav>

                <!-- Portal -->
                <nav aria-label="Portal" class="lg:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#FAF6F5]">Portal</p>
                    <div class="mt-2.5 h-0.5 w-8 bg-[#C26A4A]" aria-hidden="true"></div>
                    <ul class="mt-4 space-y-0.5">
                        <li><a href="{{ route('public.about') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Tentang</a></li>
                        <li>
                            <a href="{{ route('register') }}" class="group flex min-h-11 items-center gap-1.5 text-sm font-semibold text-[#C26A4A] transition-colors duration-150 hover:text-[#E8D8C8] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                                Daftarkan UMKM
                                <svg class="h-4 w-4 shrink-0 transition-transform duration-150 group-hover:translate-x-0.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14" />
                                    <path d="m12 5 7 7-7 7" />
                                </svg>
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Hubungi -->
                <nav aria-label="Hubungi" class="lg:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#FAF6F5]">Hubungi</p>
                    <div class="mt-2.5 h-0.5 w-8 bg-[#C26A4A]" aria-hidden="true"></div>
                    <ul class="mt-4 space-y-0.5">
                        <li><a href="{{ route('public.contact') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Kontak</a></li>
                        @if ($whatsappDigits)
                            <li><a href="https://wa.me/{{ $whatsappDigits }}" target="_blank" rel="noopener noreferrer" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">WhatsApp</a></li>
                        @endif
                    </ul>
                </nav>

                <!-- Kontak Kami -->
                <div class="lg:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#FAF6F5]">Kontak Kami</p>
                    <div class="mt-2.5 h-0.5 w-8 bg-[#C26A4A]" aria-hidden="true"></div>
                    @if ($hasContactRows)
                        <ul class="mt-4 space-y-0.5">
                            @if ($address)
                                <li class="flex min-h-11 items-start gap-2.5 text-sm text-[#E8D8C8]/75">
                                    <svg class="mt-1 h-4 w-4 shrink-0 text-[#C26A4A]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                    <span class="pt-2">{{ $address }}</span>
                                </li>
                            @endif
                            @if ($phone)
                                <li>
                                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="flex min-h-11 items-center gap-2.5 text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                                        <svg class="h-4 w-4 shrink-0 text-[#C26A4A]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                        </svg>
                                        {{ $phone }}
                                    </a>
                                </li>
                            @endif
                            @if ($email)
                                <li>
                                    <a href="mailto:{{ $email }}" class="flex min-h-11 items-center gap-2.5 text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                                        <svg class="h-4 w-4 shrink-0 text-[#C26A4A]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="20" height="16" x="2" y="4" rx="2" />
                                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                                        </svg>
                                        {{ $email }}
                                    </a>
                                </li>
                            @endif
                            @if ($hours)
                                <li class="flex min-h-11 items-center gap-2.5 text-sm text-[#E8D8C8]/75">
                                    <svg class="h-4 w-4 shrink-0 text-[#C26A4A]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10" />
                                        <polyline points="12 6 12 12 16 14" />
                                    </svg>
                                    <span class="pt-0.5">{{ $hours }}</span>
                                </li>
                            @endif
                        </ul>
                    @else
                        <p class="mt-4 text-sm leading-relaxed text-[#E8D8C8]/75">Informasi kontak belum tersedia.</p>
                    @endif
                </div>
            </div>

            <!-- ===== Mobile: brand + accordion ===== -->
            <div class="relative px-6 py-10 sm:px-10 lg:hidden">
                <!-- Brand Area (always visible) -->
                <div class="flex items-center gap-3">
                    @if (!empty($settings['site.logo']))
                        <img src="{{ asset('storage/'.$settings['site.logo']) }}" alt="{{ $siteName }}" class="h-12 w-12 shrink-0 rounded-2xl object-contain">
                    @else
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#5C4033] text-[#FAF6F5]">
                            <x-application-logo class="h-6 w-6" />
                        </span>
                    @endif
                    <span class="flex flex-col justify-center leading-tight">
                        <span class="text-lg font-bold tracking-tight text-[#FAF6F5]">{{ $siteName }}</span>
                        <span class="text-sm font-medium text-[#E8D8C8]/70">{{ $siteTagline }}</span>
                    </span>
                </div>
                <div class="mt-4 h-0.5 w-10 bg-[#C26A4A]" aria-hidden="true"></div>
                <p class="mt-4 text-sm leading-relaxed text-[#E8D8C8]/75">{{ $siteDescription }}</p>
                @if ($whatsappDigits || $instagram || $facebook || $mapsUrl)
                    <div class="mt-5 flex items-center gap-2.5">
                        @if ($whatsappDigits)
                            <a href="https://wa.me/{{ $whatsappDigits }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp {{ $siteName }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-[#E8D8C8]/25 text-[#E8D8C8] transition-colors duration-150 hover:border-[#C26A4A] hover:bg-[#C26A4A] hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 focus-visible:ring-offset-[#3F2A22]">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z" />
                                </svg>
                            </a>
                        @endif
                        @if ($instagram)
                            <a href="{{ $instagram }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram {{ $siteName }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-[#E8D8C8]/25 text-[#E8D8C8] transition-colors duration-150 hover:border-[#C26A4A] hover:bg-[#C26A4A] hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 focus-visible:ring-offset-[#3F2A22]">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="20" height="20" x="2" y="2" rx="5" ry="5" />
                                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                                    <line x1="17.5" x2="17.51" y1="6.5" y2="6.5" />
                                </svg>
                            </a>
                        @endif
                        @if ($facebook)
                            <a href="{{ $facebook }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook {{ $siteName }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-[#E8D8C8]/25 text-[#E8D8C8] transition-colors duration-150 hover:border-[#C26A4A] hover:bg-[#C26A4A] hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 focus-visible:ring-offset-[#3F2A22]">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                </svg>
                            </a>
                        @endif
                        @if ($mapsUrl)
                            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Lokasi {{ $siteName }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-[#E8D8C8]/25 text-[#E8D8C8] transition-colors duration-150 hover:border-[#C26A4A] hover:bg-[#C26A4A] hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 focus-visible:ring-offset-[#3F2A22]">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                            </a>
                        @endif
                    </div>
                @endif

                <!-- Accordion -->
                <div x-data="{ openSection: null }" class="mt-8">
                    {{-- Jelajahi --}}
                    <div class="border-t border-[#E8D8C8]/15">
                        <button @click="openSection = (openSection === 'jelajahi' ? null : 'jelajahi')" type="button" :aria-expanded="openSection === 'jelajahi' ? 'true' : 'false'" aria-controls="footer-section-jelajahi" class="flex min-h-11 w-full items-center justify-between gap-3 py-3 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                            <span class="flex items-center gap-2.5 text-sm font-semibold uppercase tracking-wider text-[#FAF6F5]">
                                <svg class="h-4 w-4 shrink-0 text-[#C26A4A]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" />
                                </svg>
                                Jelajahi
                            </span>
                            <svg class="h-4 w-4 shrink-0 text-[#C26A4A] transition-transform duration-200" :class="openSection === 'jelajahi' ? 'rotate-180' : ''" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                        <div id="footer-section-jelajahi" x-show="openSection === 'jelajahi'" x-cloak
                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            <ul class="space-y-0.5 pb-3">
                                <li><a href="{{ url('/') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Beranda</a></li>
                                <li><a href="{{ route('public.umkm.index') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">UMKM</a></li>
                                <li><a href="{{ route('public.product.index') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Produk</a></li>
                                <li><a href="{{ route('public.search') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Cari</a></li>
                            </ul>
                        </div>
                    </div>

                    {{-- Portal --}}
                    <div class="border-t border-[#E8D8C8]/15">
                        <button @click="openSection = (openSection === 'portal' ? null : 'portal')" type="button" :aria-expanded="openSection === 'portal' ? 'true' : 'false'" aria-controls="footer-section-portal" class="flex min-h-11 w-full items-center justify-between gap-3 py-3 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                            <span class="flex items-center gap-2.5 text-sm font-semibold uppercase tracking-wider text-[#FAF6F5]">
                                <svg class="h-4 w-4 shrink-0 text-[#C26A4A]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                                    <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                                    <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                                </svg>
                                Portal
                            </span>
                            <svg class="h-4 w-4 shrink-0 text-[#C26A4A] transition-transform duration-200" :class="openSection === 'portal' ? 'rotate-180' : ''" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                        <div id="footer-section-portal" x-show="openSection === 'portal'" x-cloak
                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            <ul class="space-y-0.5 pb-3">
                                <li><a href="{{ route('public.about') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Tentang</a></li>
                                <li><a href="{{ route('register') }}" class="flex min-h-11 items-center gap-1.5 text-sm font-semibold text-[#C26A4A] transition-colors duration-150 hover:text-[#E8D8C8] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Daftarkan UMKM
                                    <svg class="h-4 w-4 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12h14" />
                                        <path d="m12 5 7 7-7 7" />
                                    </svg>
                                </a></li>
                            </ul>
                        </div>
                    </div>

                    {{-- Hubungi --}}
                    <div class="border-t border-[#E8D8C8]/15">
                        <button @click="openSection = (openSection === 'hubungi' ? null : 'hubungi')" type="button" :aria-expanded="openSection === 'hubungi' ? 'true' : 'false'" aria-controls="footer-section-hubungi" class="flex min-h-11 w-full items-center justify-between gap-3 py-3 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                            <span class="flex items-center gap-2.5 text-sm font-semibold uppercase tracking-wider text-[#FAF6F5]">
                                <svg class="h-4 w-4 shrink-0 text-[#C26A4A]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                </svg>
                                Hubungi
                            </span>
                            <svg class="h-4 w-4 shrink-0 text-[#C26A4A] transition-transform duration-200" :class="openSection === 'hubungi' ? 'rotate-180' : ''" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                        <div id="footer-section-hubungi" x-show="openSection === 'hubungi'" x-cloak
                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            <ul class="space-y-0.5 pb-3">
                                <li><a href="{{ route('public.contact') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Kontak</a></li>
                                @if ($whatsappDigits)
                                    <li><a href="https://wa.me/{{ $whatsappDigits }}" target="_blank" rel="noopener noreferrer" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">WhatsApp</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    {{-- Kontak Kami --}}
                    <div class="border-t border-b border-[#E8D8C8]/15">
                        <button @click="openSection = (openSection === 'kontak' ? null : 'kontak')" type="button" :aria-expanded="openSection === 'kontak' ? 'true' : 'false'" aria-controls="footer-section-kontak" class="flex min-h-11 w-full items-center justify-between gap-3 py-3 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                            <span class="flex items-center gap-2.5 text-sm font-semibold uppercase tracking-wider text-[#FAF6F5]">
                                <svg class="h-4 w-4 shrink-0 text-[#C26A4A]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                                Kontak Kami
                            </span>
                            <svg class="h-4 w-4 shrink-0 text-[#C26A4A] transition-transform duration-200" :class="openSection === 'kontak' ? 'rotate-180' : ''" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                        <div id="footer-section-kontak" x-show="openSection === 'kontak'" x-cloak
                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            @if ($hasContactRows)
                                <ul class="space-y-0.5 pb-3">
                                    @if ($address)
                                        <li class="flex min-h-11 items-start gap-2.5 text-sm text-[#E8D8C8]/75">
                                            <svg class="mt-1 h-4 w-4 shrink-0 text-[#C26A4A]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                                <circle cx="12" cy="10" r="3" />
                                            </svg>
                                            <span class="pt-2">{{ $address }}</span>
                                        </li>
                                    @endif
                                    @if ($phone)
                                        <li>
                                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="flex min-h-11 items-center gap-2.5 text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                                                <svg class="h-4 w-4 shrink-0 text-[#C26A4A]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                                </svg>
                                                {{ $phone }}
                                            </a>
                                        </li>
                                    @endif
                                    @if ($email)
                                        <li>
                                            <a href="mailto:{{ $email }}" class="flex min-h-11 items-center gap-2.5 text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                                                <svg class="h-4 w-4 shrink-0 text-[#C26A4A]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect width="20" height="16" x="2" y="4" rx="2" />
                                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                                                </svg>
                                                {{ $email }}
                                            </a>
                                        </li>
                                    @endif
                                    @if ($hours)
                                        <li class="flex min-h-11 items-center gap-2.5 text-sm text-[#E8D8C8]/75">
                                            <svg class="h-4 w-4 shrink-0 text-[#C26A4A]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10" />
                                                <polyline points="12 6 12 12 16 14" />
                                            </svg>
                                            <span class="pt-0.5">{{ $hours }}</span>
                                        </li>
                                    @endif
                                </ul>
                            @else
                                <p class="pb-3 text-sm leading-relaxed text-[#E8D8C8]/75">Informasi kontak belum tersedia.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom bar -->
            <div class="relative flex flex-col items-center gap-2 border-t border-[#E8D8C8]/15 px-6 py-6 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left sm:px-10 lg:px-12">
                <p class="text-sm text-[#E8D8C8]/60">
                    &copy; {{ now()->year }} {{ $siteName }}. Seluruh hak cipta dilindungi.
                </p>
                <p class="flex items-center gap-1.5 text-sm text-[#E8D8C8]/60">
                    <svg class="h-4 w-4 shrink-0 text-[#C26A4A]" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
                    </svg>
                    Dibangun untuk kemajuan ekonomi desa, oleh Desa Salamnunggal.
                </p>
            </div>
        </div>
    </footer>
@elseif (Auth::user()?->hasRole('administrator'))
    {{-- ============ ADMIN FOOTER ============ --}}
    @php
        $siteName = $settings['site.name'] ?? 'Portal UMKM Salamnunggal';
        $siteTagline = $settings['site.tagline'] ?? 'Desa Salamnunggal';
        $siteDescription = $settings['site.description'] ?? 'Media promosi dan informasi UMKM Desa Salamnunggal untuk mempertemukan masyarakat dengan pelaku usaha lokal.';
        $address = $settings['contact.address'] ?? null;
        $phone = $settings['contact.phone'] ?? null;
        $whatsapp = $settings['contact.whatsapp'] ?? null;
        $email = $settings['contact.email'] ?? null;
        $hours = $settings['contact.hours'] ?? null;
        $mapsUrl = $settings['contact.maps_url'] ?? null;
        $instagram = $settings['social.instagram'] ?? null;
        $facebook = $settings['social.facebook'] ?? null;
        $whatsappDigits = $whatsapp ? preg_replace('/\D+/', '', $whatsapp) : null;
        $hasContactRows = $address || $phone || $email || $hours;
    @endphp

    <footer class="container-page mt-12 pb-6 sm:pb-8">
        <div class="relative overflow-hidden rounded-3xl bg-[#3F2A22] shadow-[0_24px_60px_-24px_rgba(63,42,34,0.6)]">
            <span aria-hidden="true" class="pointer-events-none absolute -bottom-16 -right-16 h-72 w-72 text-[#E8D8C8] opacity-[0.04]">
                <x-application-logo class="h-full w-full" />
            </span>

            <div class="relative hidden gap-10 px-6 py-10 sm:px-10 sm:py-12 lg:grid lg:grid-cols-12 lg:gap-8 lg:px-12 lg:py-14">
                <div class="lg:col-span-4">
                    <div class="flex items-center gap-3">
                        @if (!empty($settings['site.logo']))
                            <img src="{{ asset('storage/'.$settings['site.logo']) }}" alt="{{ $siteName }}" class="h-12 w-12 shrink-0 rounded-2xl object-contain">
                        @else
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#5C4033] text-[#FAF6F5]">
                                <x-application-logo class="h-6 w-6" />
                            </span>
                        @endif
                        <span class="flex flex-col justify-center leading-tight">
                            <span class="text-lg font-bold tracking-tight text-[#FAF6F5]">{{ $siteName }}</span>
                            <span class="text-sm font-medium text-[#E8D8C8]/70">Panel Administrasi</span>
                        </span>
                    </div>
                    <div class="mt-4 h-0.5 w-10 bg-[#C26A4A]" aria-hidden="true"></div>
                    <p class="mt-4 max-w-sm text-sm leading-relaxed text-[#E8D8C8]/75">{{ $siteDescription }}</p>
                    <div class="mt-5 flex items-center gap-2.5">
                        @if ($whatsappDigits)
                            <a href="https://wa.me/{{ $whatsappDigits }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp {{ $siteName }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-[#E8D8C8]/25 text-[#E8D8C8] transition-colors duration-150 hover:border-[#C26A4A] hover:bg-[#C26A4A] hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 focus-visible:ring-offset-[#3F2A22]">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z" />
                                </svg>
                            </a>
                        @endif
                        @if ($instagram)
                            <a href="{{ $instagram }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram {{ $siteName }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-[#E8D8C8]/25 text-[#E8D8C8] transition-colors duration-150 hover:border-[#C26A4A] hover:bg-[#C26A4A] hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 focus-visible:ring-offset-[#3F2A22]">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="20" height="20" x="2" y="2" rx="5" ry="5" />
                                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                                    <line x1="17.5" x2="17.51" y1="6.5" y2="6.5" />
                                </svg>
                            </a>
                        @endif
                        @if ($facebook)
                            <a href="{{ $facebook }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook {{ $siteName }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-[#E8D8C8]/25 text-[#E8D8C8] transition-colors duration-150 hover:border-[#C26A4A] hover:bg-[#C26A4A] hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 focus-visible:ring-offset-[#3F2A22]">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                </svg>
                            </a>
                        @endif
                        @if ($mapsUrl)
                            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Lokasi {{ $siteName }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-[#E8D8C8]/25 text-[#E8D8C8] transition-colors duration-150 hover:border-[#C26A4A] hover:bg-[#C26A4A] hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 focus-visible:ring-offset-[#3F2A22]">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>

                <nav aria-label="Dashboard" class="lg:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#FAF6F5]">Dashboard</p>
                    <div class="mt-2.5 h-0.5 w-8 bg-[#C26A4A]" aria-hidden="true"></div>
                    <ul class="mt-4 space-y-0.5">
                        <li><a href="{{ route('admin.dashboard') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Dashboard</a></li>
                    </ul>
                </nav>

                <nav aria-label="Verifikasi" class="lg:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#FAF6F5]">Verifikasi</p>
                    <div class="mt-2.5 h-0.5 w-8 bg-[#C26A4A]" aria-hidden="true"></div>
                    <ul class="mt-4 space-y-0.5">
                        <li><a href="{{ route('admin.umkm.verification.index') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">UMKM</a></li>
                        <li><a href="{{ route('admin.products.verification.index') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Produk</a></li>
                        <li><a href="{{ route('admin.owner-verification.index') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Owner</a></li>
                    </ul>
                </nav>

                <nav aria-label="Data" class="lg:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#FAF6F5]">Data</p>
                    <div class="mt-2.5 h-0.5 w-8 bg-[#C26A4A]" aria-hidden="true"></div>
                    <ul class="mt-4 space-y-0.5">
                        <li><a href="{{ route('admin.umkms.index') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">UMKM</a></li>
                        <li><a href="{{ route('admin.products.index') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Produk</a></li>
                        <li><a href="{{ route('admin.users.index') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Pengguna</a></li>
                        <li><a href="{{ route('admin.categories.index') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Kategori</a></li>
                    </ul>
                </nav>

                <nav aria-label="Sistem" class="lg:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#FAF6F5]">Sistem</p>
                    <div class="mt-2.5 h-0.5 w-8 bg-[#C26A4A]" aria-hidden="true"></div>
                    <ul class="mt-4 space-y-0.5">
                        <li><a href="{{ route('admin.settings.index') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Pengaturan Portal</a></li>
                    </ul>
                </nav>
            </div>

            <div class="relative px-6 py-10 sm:px-10 lg:hidden">
                <div class="flex items-center gap-3">
                    @if (!empty($settings['site.logo']))
                        <img src="{{ asset('storage/'.$settings['site.logo']) }}" alt="{{ $siteName }}" class="h-12 w-12 shrink-0 rounded-2xl object-contain">
                    @else
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#5C4033] text-[#FAF6F5]">
                            <x-application-logo class="h-6 w-6" />
                        </span>
                    @endif
                    <span class="flex flex-col justify-center leading-tight">
                        <span class="text-lg font-bold tracking-tight text-[#FAF6F5]">{{ $siteName }}</span>
                        <span class="text-sm font-medium text-[#E8D8C8]/70">Panel Administrasi</span>
                    </span>
                </div>
                <div class="mt-4 h-0.5 w-10 bg-[#C26A4A]" aria-hidden="true"></div>
                <p class="mt-4 text-sm leading-relaxed text-[#E8D8C8]/75">{{ $siteDescription }}</p>

                <div x-data="{ openSection: null }" class="mt-8">
                    <div class="border-t border-[#E8D8C8]/15">
                        <button @click="openSection = (openSection === 'dashboard' ? null : 'dashboard')" type="button" :aria-expanded="openSection === 'dashboard' ? 'true' : 'false'" aria-controls="footer-section-dashboard" class="flex min-h-11 w-full items-center justify-between gap-3 py-3 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                            <span class="flex items-center gap-2.5 text-sm font-semibold uppercase tracking-wider text-[#FAF6F5]">
                                <svg class="h-4 w-4 shrink-0 text-[#C26A4A]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="7" height="7" rx="1" />
                                    <rect x="14" y="3" width="7" height="7" rx="1" />
                                    <rect x="14" y="14" width="7" height="7" rx="1" />
                                    <rect x="3" y="14" width="7" height="7" rx="1" />
                                </svg>
                                Dashboard
                            </span>
                            <svg class="h-4 w-4 shrink-0 text-[#C26A4A] transition-transform duration-200" :class="openSection === 'dashboard' ? 'rotate-180' : ''" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                        <div id="footer-section-dashboard" x-show="openSection === 'dashboard'" x-cloak
                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            <ul class="space-y-0.5 pb-3">
                                <li><a href="{{ route('admin.dashboard') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Dashboard</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="border-t border-[#E8D8C8]/15">
                        <button @click="openSection = (openSection === 'verifikasi' ? null : 'verifikasi')" type="button" :aria-expanded="openSection === 'verifikasi' ? 'true' : 'false'" aria-controls="footer-section-verifikasi" class="flex min-h-11 w-full items-center justify-between gap-3 py-3 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                            <span class="flex items-center gap-2.5 text-sm font-semibold uppercase tracking-wider text-[#FAF6F5]">
                                <svg class="h-4 w-4 shrink-0 text-[#C26A4A]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 12l2 2 4-4" />
                                    <circle cx="12" cy="12" r="10" />
                                </svg>
                                Verifikasi
                            </span>
                            <svg class="h-4 w-4 shrink-0 text-[#C26A4A] transition-transform duration-200" :class="openSection === 'verifikasi' ? 'rotate-180' : ''" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                        <div id="footer-section-verifikasi" x-show="openSection === 'verifikasi'" x-cloak
                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            <ul class="space-y-0.5 pb-3">
                                <li><a href="{{ route('admin.umkm.verification.index') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">UMKM</a></li>
                                <li><a href="{{ route('admin.products.verification.index') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Produk</a></li>
                                <li><a href="{{ route('admin.owner-verification.index') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Owner</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="border-t border-[#E8D8C8]/15">
                        <button @click="openSection = (openSection === 'data' ? null : 'data')" type="button" :aria-expanded="openSection === 'data' ? 'true' : 'false'" aria-controls="footer-section-data" class="flex min-h-11 w-full items-center justify-between gap-3 py-3 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                            <span class="flex items-center gap-2.5 text-sm font-semibold uppercase tracking-wider text-[#FAF6F5]">
                                <svg class="h-4 w-4 shrink-0 text-[#C26A4A]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="7" height="7" x="3" y="3" rx="1" />
                                    <rect width="7" height="7" x="14" y="3" rx="1" />
                                    <rect width="7" height="7" x="14" y="14" rx="1" />
                                    <rect width="7" height="7" x="3" y="14" rx="1" />
                                </svg>
                                Data
                            </span>
                            <svg class="h-4 w-4 shrink-0 text-[#C26A4A] transition-transform duration-200" :class="openSection === 'data' ? 'rotate-180' : ''" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                        <div id="footer-section-data" x-show="openSection === 'data'" x-cloak
                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            <ul class="space-y-0.5 pb-3">
                                <li><a href="{{ route('admin.umkms.index') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">UMKM</a></li>
                                <li><a href="{{ route('admin.products.index') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Produk</a></li>
                                <li><a href="{{ route('admin.users.index') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Pengguna</a></li>
                                <li><a href="{{ route('admin.categories.index') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Kategori</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="border-t border-[#E8D8C8]/15">
                        <button @click="openSection = (openSection === 'sistem' ? null : 'sistem')" type="button" :aria-expanded="openSection === 'sistem' ? 'true' : 'false'" aria-controls="footer-section-sistem" class="flex min-h-11 w-full items-center justify-between gap-3 py-3 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                            <span class="flex items-center gap-2.5 text-sm font-semibold uppercase tracking-wider text-[#FAF6F5]">
                                <svg class="h-4 w-4 shrink-0 text-[#C26A4A]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                Sistem
                            </span>
                            <svg class="h-4 w-4 shrink-0 text-[#C26A4A] transition-transform duration-200" :class="openSection === 'sistem' ? 'rotate-180' : ''" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                        <div id="footer-section-sistem" x-show="openSection === 'sistem'" x-cloak
                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            <ul class="space-y-0.5 pb-3">
                                <li><a href="{{ route('admin.settings.index') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Pengaturan Portal</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative flex flex-col items-center gap-2 border-t border-[#E8D8C8]/15 px-6 py-6 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left sm:px-10 lg:px-12">
                <p class="text-sm text-[#E8D8C8]/60">
                    &copy; {{ now()->year }} {{ $siteName }}. Seluruh hak cipta dilindungi.
                </p>
                <p class="flex items-center gap-1.5 text-sm text-[#E8D8C8]/60">
                    <svg class="h-4 w-4 shrink-0 text-[#C26A4A]" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
                    </svg>
                    Dibangun untuk kemajuan ekonomi desa, oleh Desa Salamnunggal.
                </p>
            </div>
        </div>
    </footer>
@else
    @include('layouts.owner-footer')
@endif