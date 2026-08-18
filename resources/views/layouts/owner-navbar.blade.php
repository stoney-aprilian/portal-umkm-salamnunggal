        {{-- ============ OWNER/USER NAVBAR ============ --}}
        <div class="container-page">
            <div class="flex h-16 items-center justify-between gap-4 rounded-2xl border border-[#ECE5D9] bg-white px-4 shadow-[0_2px_12px_rgba(63,42,34,0.06)] sm:px-5">
                <!-- Brand -->
                <a href="{{ route('dashboard') }}" aria-label="{{ config('app.name', 'Portal UMKM Salamnunggal') }}" class="flex shrink-0 items-center gap-2.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                    @if (!empty($settings['site.logo']))
                        <img src="{{ asset('storage/'.$settings['site.logo']) }}" alt="{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}" class="h-9 w-9 shrink-0 rounded-lg object-contain">
                    @else
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#5C4033] text-white">
                            <x-application-logo class="h-5 w-5" />
                        </span>
                    @endif
                    <span class="flex flex-col justify-center leading-tight">
                        <span class="text-[15px] font-bold tracking-tight text-[#3F2A22]">{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}</span>
                        <span class="text-[11px] font-medium text-[#8A7464]">Dashboard Owner</span>
                    </span>
                    <span class="hidden sm:inline-flex rounded-full bg-[#F4EDE1] px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide text-[#5C4033]">Owner</span>
                </a>

                <!-- Desktop Navigation -->
                <nav aria-label="Navigasi utama" class="hidden lg:flex lg:items-center lg:gap-0.5">
                    <a href="{{ route('dashboard') }}" @class([
                        'inline-flex min-h-10 items-center gap-1.5 rounded-full px-3 text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2',
                        'bg-[#F4EDE1] text-[#5C4033]' => request()->routeIs('dashboard'),
                        'text-[#6F5D50] hover:bg-[#F4EDE1] hover:text-[#3F2A22]' => !request()->routeIs('dashboard'),
                    ])>
                        <svg class="h-4 w-4 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="7" height="7" x="3" y="3" rx="1" />
                            <rect width="7" height="7" x="14" y="3" rx="1" />
                            <rect width="7" height="7" x="14" y="14" rx="1" />
                            <rect width="7" height="7" x="3" y="14" rx="1" />
                        </svg>
                        {{ __('Dashboard') }}
                    </a>
                    @php $ownerUmkm = Auth::user()->umkm; @endphp
                    @if ($ownerUmkm === null)
                        <a href="{{ route('owner.umkm.create') }}" @class([
                            'inline-flex min-h-10 items-center gap-1.5 rounded-full px-3 text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2',
                            'bg-[#F4EDE1] text-[#5C4033]' => request()->routeIs('owner.umkm.*'),
                            'text-[#6F5D50] hover:bg-[#F4EDE1] hover:text-[#3F2A22]' => !request()->routeIs('owner.umkm.*'),
                        ])>
                            <svg class="h-4 w-4 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                                <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                                <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                            </svg>
                            {{ __('Ajukan UMKM') }}
                        </a>
                    @else
                        @if (in_array($ownerUmkm->status, ['draft', 'pending', 'needs_revision', 'rejected'], true))
                            <a href="{{ route('owner.umkm.edit', $ownerUmkm) }}" @class([
                                'inline-flex min-h-10 items-center gap-1.5 rounded-full px-3 text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2',
                                'bg-[#F4EDE1] text-[#5C4033]' => request()->routeIs('owner.umkm.*'),
                                'text-[#6F5D50] hover:bg-[#F4EDE1] hover:text-[#3F2A22]' => !request()->routeIs('owner.umkm.*'),
                            ])>
                                <svg class="h-4 w-4 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                                    <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                                    <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                                </svg>
                                {{ __('UMKM Saya') }}
                            </a>
                        @endif
                        @if ($ownerUmkm->status === 'approved')
                            <a href="{{ route('owner.products.index', $ownerUmkm) }}" @class([
                                'inline-flex min-h-10 items-center gap-1.5 rounded-full px-3 text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2',
                                'bg-[#F4EDE1] text-[#5C4033]' => request()->routeIs('owner.products.*'),
                                'text-[#6F5D50] hover:bg-[#F4EDE1] hover:text-[#3F2A22]' => !request()->routeIs('owner.products.*'),
                            ])>
                                <svg class="h-4 w-4 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m7.5 4.27 9 5.15" />
                                    <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                                    <path d="m3.3 7 8.7 5 8.7-5" />
                                    <path d="M12 22V12" />
                                </svg>
                                {{ __('Produk') }}
                            </a>
                        @endif
                    @endif
                </nav>

                <!-- Desktop Right Side -->
                <div class="hidden shrink-0 items-center gap-3 lg:flex">
                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button class="inline-flex min-h-10 items-center gap-2.5 rounded-full border border-[#ECE5D9] px-2.5 transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 hover:border-[#C26A4A]/30 hover:bg-[#F4EDE1]">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#5C4033] text-xs font-bold text-white">{{ \Illuminate\Support\Str::initials(Auth::user()->name) }}</span>
                                <span class="max-w-[8rem] truncate text-sm font-medium text-[#3F2A22]">{{ Auth::user()->name }}</span>
                                <svg class="h-3.5 w-3.5 shrink-0 text-[#8A7464]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="px-4 py-3 border-b border-[#ECE5D9]">
                                <div class="text-sm font-semibold text-[#3F2A22]">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-[#8A7464]">{{ Auth::user()->email }}</div>
                            </div>
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profil') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Keluar') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Mobile Header -->
                <div class="flex shrink-0 items-center gap-1.5 lg:hidden">
                    <a href="{{ route('profile.edit') }}" aria-label="Profil {{ Auth::user()->name }}" class="flex h-11 w-11 items-center justify-center rounded-full bg-[#5C4033] text-sm font-semibold text-white transition-colors duration-150 hover:bg-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        {{ \Illuminate\Support\Str::initials(Auth::user()->name) }}
                    </a>
                    <button @click="open = ! open" type="button" :aria-label="open ? 'Tutup menu' : 'Buka menu'" :aria-expanded="open ? 'true' : 'false'" aria-controls="owner-mobile-navigation" class="inline-flex h-11 w-11 items-center justify-center rounded-full text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': ! open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': ! open, 'inline-flex': open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div id="owner-mobile-navigation" x-show="open" x-cloak class="relative lg:hidden">
            <div class="fixed inset-0 -z-10 bg-[#3F2A22]/40" @click="open = false" aria-hidden="true"></div>
            <div class="absolute inset-x-3 top-full z-50 mt-2 rounded-2xl border border-[#ECE5D9] bg-white shadow-[0_16px_40px_-16px_rgba(63,42,34,0.4)]"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2">
                <div class="flex items-center justify-between gap-3 p-3">
                    <span class="flex items-center gap-2.5">
                        @if (!empty($settings['site.logo']))
                            <img src="{{ asset('storage/'.$settings['site.logo']) }}" alt="" class="h-8 w-8 rounded-lg object-contain">
                        @else
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#5C4033] text-white">
                                <x-application-logo class="h-4 w-4" />
                            </span>
                        @endif
                        <span class="flex flex-col justify-center leading-tight">
                            <span class="text-sm font-bold tracking-tight text-[#3F2A22]">{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}</span>
                            <span class="text-[11px] font-medium text-[#8A7464]">Dashboard Owner</span>
                        </span>
                    </span>
                    <button @click="open = false" type="button" aria-label="Tutup menu" class="inline-flex h-11 w-11 items-center justify-center rounded-full text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mx-3 border-t border-[#ECE5D9]" aria-hidden="true"></div>

                <div class="p-2 space-y-0.5">
                    <a href="{{ route('dashboard') }}" @class([
                        'flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors duration-150',
                        'bg-[#F4EDE1] text-[#5C4033]' => request()->routeIs('dashboard'),
                        'text-[#5F524A] hover:bg-[#FAF6F5]' => !request()->routeIs('dashboard'),
                    ])>
                        <svg class="h-4 w-4 shrink-0 text-[#8A7464]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="7" height="7" x="3" y="3" rx="1" />
                            <rect width="7" height="7" x="14" y="3" rx="1" />
                            <rect width="7" height="7" x="14" y="14" rx="1" />
                            <rect width="7" height="7" x="3" y="14" rx="1" />
                        </svg>
                        {{ __('Dashboard') }}
                    </a>
                    @php $ownerUmkm = Auth::user()->umkm; @endphp
                    @if ($ownerUmkm === null)
                        <a href="{{ route('owner.umkm.create') }}" @class([
                            'flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors duration-150',
                            'bg-[#F4EDE1] text-[#5C4033]' => request()->routeIs('owner.umkm.*'),
                            'text-[#5F524A] hover:bg-[#FAF6F5]' => !request()->routeIs('owner.umkm.*'),
                        ])>
                            <svg class="h-4 w-4 shrink-0 text-[#8A7464]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                                <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                                <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                            </svg>
                            {{ __('Ajukan UMKM') }}
                        </a>
                    @else
                        @if (in_array($ownerUmkm->status, ['draft', 'pending', 'needs_revision', 'rejected'], true))
                            <a href="{{ route('owner.umkm.edit', $ownerUmkm) }}" @class([
                                'flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors duration-150',
                                'bg-[#F4EDE1] text-[#5C4033]' => request()->routeIs('owner.umkm.*'),
                                'text-[#5F524A] hover:bg-[#FAF6F5]' => !request()->routeIs('owner.umkm.*'),
                            ])>
                                <svg class="h-4 w-4 shrink-0 text-[#8A7464]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                                    <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                                    <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                                </svg>
                                {{ __('UMKM Saya') }}
                            </a>
                        @endif
                        @if ($ownerUmkm->status === 'approved')
                            <a href="{{ route('owner.products.index', $ownerUmkm) }}" @class([
                                'flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors duration-150',
                                'bg-[#F4EDE1] text-[#5C4033]' => request()->routeIs('owner.products.*'),
                                'text-[#5F524A] hover:bg-[#FAF6F5]' => !request()->routeIs('owner.products.*'),
                            ])>
                                <svg class="h-4 w-4 shrink-0 text-[#8A7464]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m7.5 4.27 9 5.15" />
                                    <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                                    <path d="m3.3 7 8.7 5 8.7-5" />
                                    <path d="M12 22V12" />
                                </svg>
                                {{ __('Produk') }}
                            </a>
                        @endif
                    @endif
                </div>

                <div class="mx-3 border-t border-[#ECE5D9]" aria-hidden="true"></div>

                <div class="p-2 space-y-0.5">
                    <a href="{{ route('profile.edit') }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-[#5F524A] transition-colors duration-150 hover:bg-[#FAF6F5] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                        <svg class="h-4 w-4 shrink-0 text-[#8A7464]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        {{ __('Profil') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex min-h-11 w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-[#5F524A] transition-colors duration-150 hover:bg-[#FAF6F5] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                            <svg class="h-4 w-4 shrink-0 text-[#8A7464]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <polyline points="16 17 21 12 16 7" />
                                <line x1="21" y2="12" x2="9" y3="12" />
                            </svg>
                            {{ __('Keluar') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
