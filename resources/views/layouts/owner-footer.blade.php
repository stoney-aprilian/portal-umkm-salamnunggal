    {{-- ============ OWNER FOOTER ============ --}}
    <footer class="container-page mt-12 pb-6 sm:pb-8">
        <div class="relative overflow-hidden rounded-3xl bg-[#3F2A22] shadow-[0_24px_60px_-24px_rgba(63,42,34,0.6)]">
            <span aria-hidden="true" class="pointer-events-none absolute -bottom-16 -right-16 h-72 w-72 text-[#E8D8C8] opacity-[0.04]">
                <x-application-logo class="h-full w-full" />
            </span>

            <div class="relative hidden gap-10 px-6 py-10 sm:px-10 sm:py-12 lg:grid lg:grid-cols-12 lg:gap-8 lg:px-12 lg:py-14">
                <div class="lg:col-span-4">
                    <div class="flex items-center gap-3">
                        @if (!empty($settings['site.logo']))
                            <img src="{{ asset('storage/'.$settings['site.logo']) }}" alt="{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}" class="h-12 w-12 shrink-0 rounded-2xl object-contain">
                        @else
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#5C4033] text-[#FAF6F5]">
                                <x-application-logo class="h-6 w-6" />
                            </span>
                        @endif
                        <span class="flex flex-col justify-center leading-tight">
                            <span class="text-lg font-bold tracking-tight text-[#FAF6F5]">{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}</span>
                            <span class="text-sm font-medium text-[#E8D8C8]/70">{{ $settings['site.tagline'] ?? 'Desa Salamnunggal' }}</span>
                        </span>
                    </div>
                    <div class="mt-4 h-0.5 w-10 bg-[#C26A4A]" aria-hidden="true"></div>
                    <p class="mt-4 max-w-sm text-sm leading-relaxed text-[#E8D8C8]/75">{{ $settings['site.description'] ?? 'Media promosi dan publikasi UMKM Desa Salamnunggal untuk mempertemukan masyarakat dengan pelaku usaha lokal.' }}</p>
                    <div class="mt-5 flex items-center gap-2.5">
                        <a href="{{ route('dashboard') }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-[#E8D8C8]/25 text-[#E8D8C8] transition-colors duration-150 hover:border-[#C26A4A] hover:bg-[#C26A4A] hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 focus-visible:ring-offset-[#3F2A22]" aria-label="Dashboard">
                            <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="7" height="7" x="3" y="3" rx="1" />
                                <rect width="7" height="7" x="14" y="3" rx="1" />
                                <rect width="7" height="7" x="14" y="14" rx="1" />
                                <rect width="7" height="7" x="3" y="14" rx="1" />
                            </svg>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-[#E8D8C8]/25 text-[#E8D8C8] transition-colors duration-150 hover:border-[#C26A4A] hover:bg-[#C26A4A] hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 focus-visible:ring-offset-[#3F2A22]" aria-label="Profil">
                            <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </a>
                    </div>
                </div>

                <nav aria-label="Navigasi Owner" class="lg:col-span-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#FAF6F5]">Navigasi</p>
                    <div class="mt-2.5 h-0.5 w-8 bg-[#C26A4A]" aria-hidden="true"></div>
                    <ul class="mt-4 space-y-0.5">
                        <li><a href="{{ route('dashboard') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Dashboard</a></li>
                        @php $ownerUmkm = Auth::user()->umkm; @endphp
                        @if ($ownerUmkm === null)
                            <li><a href="{{ route('owner.umkm.create') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Ajukan UMKM</a></li>
                        @else
                            @if (in_array($ownerUmkm->status, ['draft', 'pending', 'needs_revision', 'rejected'], true))
                                <li><a href="{{ route('owner.umkm.edit', $ownerUmkm) }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">UMKM Saya</a></li>
                            @endif
                            @if ($ownerUmkm->status === 'approved')
                                <li><a href="{{ route('owner.products.index', $ownerUmkm) }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Produk</a></li>
                            @endif
                        @endif
                        <li><a href="{{ route('profile.edit') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Profil</a></li>
                    </ul>
                </nav>

                <div class="lg:col-span-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#FAF6F5]">Portal</p>
                    <div class="mt-2.5 h-0.5 w-8 bg-[#C26A4A]" aria-hidden="true"></div>
                    <ul class="mt-4 space-y-0.5">
                        <li><a href="{{ route('public.about') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Tentang</a></li>
                        <li><a href="{{ route('public.contact') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Kontak</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                                    Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="relative px-6 py-10 sm:px-10 lg:hidden">
                <div class="flex items-center gap-3">
                    @if (!empty($settings['site.logo']))
                        <img src="{{ asset('storage/'.$settings['site.logo']) }}" alt="{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}" class="h-10 w-10 shrink-0 rounded-xl object-contain">
                    @else
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#5C4033] text-[#FAF6F5]">
                            <x-application-logo class="h-5 w-5" />
                        </span>
                    @endif
                    <span class="flex flex-col justify-center leading-tight">
                        <span class="text-base font-bold tracking-tight text-[#FAF6F5]">{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}</span>
                        <span class="text-xs font-medium text-[#E8D8C8]/70">{{ $settings['site.tagline'] ?? 'Desa Salamnunggal' }}</span>
                    </span>
                </div>
                <div class="mt-4 h-0.5 w-8 bg-[#C26A4A]" aria-hidden="true"></div>
                <p class="mt-4 text-sm leading-relaxed text-[#E8D8C8]/75">{{ $settings['site.description'] ?? 'Media promosi dan publikasi UMKM Desa Salamnunggal untuk mempertemukan masyarakat dengan pelaku usaha lokal.' }}</p>

                <div class="mt-8 space-y-1">
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#FAF6F5]">Navigasi</p>
                    <div class="mt-2 h-0.5 w-6 bg-[#C26A4A]" aria-hidden="true"></div>
                    <ul class="mt-3 space-y-0.5">
                        <li><a href="{{ route('dashboard') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Dashboard</a></li>
                        @php $ownerUmkm = Auth::user()->umkm; @endphp
                        @if ($ownerUmkm === null)
                            <li><a href="{{ route('owner.umkm.create') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Ajukan UMKM</a></li>
                        @else
                            @if (in_array($ownerUmkm->status, ['draft', 'pending', 'needs_revision', 'rejected'], true))
                                <li><a href="{{ route('owner.umkm.edit', $ownerUmkm) }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">UMKM Saya</a></li>
                            @endif
                            @if ($ownerUmkm->status === 'approved')
                                <li><a href="{{ route('owner.products.index', $ownerUmkm) }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Produk</a></li>
                            @endif
                        @endif
                        <li><a href="{{ route('profile.edit') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Profil</a></li>
                    </ul>
                </div>

                <div class="mt-8 space-y-1">
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#FAF6F5]">Portal</p>
                    <div class="mt-2 h-0.5 w-6 bg-[#C26A4A]" aria-hidden="true"></div>
                    <ul class="mt-3 space-y-0.5">
                        <li><a href="{{ route('public.about') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Tentang</a></li>
                        <li><a href="{{ route('public.contact') }}" class="flex min-h-11 items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">Kontak</a></li>
                    </ul>
                </div>

                <div class="mt-8 space-y-1">
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#FAF6F5]">Akun</p>
                    <div class="mt-2 h-0.5 w-6 bg-[#C26A4A]" aria-hidden="true"></div>
                    <ul class="mt-3 space-y-0.5">
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="flex min-h-11 w-full items-center text-sm text-[#E8D8C8]/75 transition-colors duration-150 hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                                    Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="relative flex flex-col items-center gap-2 border-t border-[#E8D8C8]/15 px-6 py-6 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left sm:px-10 lg:px-12">
                <p class="text-sm text-[#E8D8C8]/60">
                    &copy; {{ now()->year }} {{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}. Seluruh hak cipta dilindungi.
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
