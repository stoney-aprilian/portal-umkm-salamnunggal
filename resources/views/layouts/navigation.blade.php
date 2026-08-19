@php
    $isPublicContext = ! Auth::user()?->hasRole('administrator')
        && (Auth::guest() || request()->routeIs('home') || request()->routeIs('public.*'));
@endphp

<nav x-data="{ open: false, scrolled: false, verifikasiOpen: false, dataOpen: false, sistemOpen: false }"
     @scroll.window="scrolled = window.scrollY > 8"
     @keydown.escape.window="open = false"
     class="sticky top-0 z-40 {{ $isPublicContext ? 'transition-shadow duration-300' : 'border-b border-[#ECE5D9] bg-white' }}"
     :class="{{ $isPublicContext ? 'scrolled' : 'false' }} ? 'shadow-[0_10px_30px_-15px_rgba(63,42,34,0.35)]' : ''">

    @if ($isPublicContext)
        {{-- ============ PUBLIC NAVBAR (guest + authenticated user on public pages) ============ --}}
        <div class="container-page">
            <div class="flex h-16 items-center justify-between gap-3 rounded-2xl border border-white/60 bg-white/80 px-4 shadow-[0_2px_12px_rgba(63,42,34,0.08)] backdrop-blur-md sm:px-5">
                <!-- Brand -->
                <a href="{{ Auth::check() ? route('dashboard') : url('/') }}" aria-label="{{ config('app.name', 'Portal UMKM Salamnunggal') }}" class="flex shrink-0 items-center gap-2.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                    @if (!empty($settings['site.logo']))
                        <img src="{{ asset('storage/'.$settings['site.logo']) }}" alt="{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}" class="h-10 w-10 shrink-0 rounded-xl object-contain">
                    @else
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#5C4033] text-white">
                            <x-application-logo class="h-5 w-5" />
                        </span>
                    @endif
                    <span class="flex flex-col justify-center leading-tight">
                        <span class="text-[15px] font-bold tracking-tight text-[#3F2A22]">{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}</span>
                        <span class="text-xs font-medium text-[#8A7464]">{{ $settings['site.tagline'] ?? 'Desa Salamnunggal' }}</span>
                    </span>
                </a>

                <!-- Desktop Navigation -->
                <nav aria-label="Navigasi utama" class="hidden lg:flex lg:items-center lg:gap-1">
                    <a href="{{ url('/') }}" @class([
                        'inline-flex min-h-11 items-center rounded-full px-3.5 text-sm transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2',
                        'font-semibold text-[#5C4033] underline decoration-[#C26A4A] decoration-2 underline-offset-2' => request()->routeIs('home'),
                        'font-medium text-[#6F5D50] hover:bg-[#F4EDE1] hover:text-[#3F2A22]' => ! request()->routeIs('home'),
                    ])">
                        {{ __('Beranda') }}
                    </a>
                    <a href="{{ route('public.umkm.index') }}" @class([
                        'inline-flex min-h-11 items-center rounded-full px-3.5 text-sm transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2',
                        'font-semibold text-[#5C4033] underline decoration-[#C26A4A] decoration-2 underline-offset-2' => request()->routeIs('public.umkm.*'),
                        'font-medium text-[#6F5D50] hover:bg-[#F4EDE1] hover:text-[#3F2A22]' => ! request()->routeIs('public.umkm.*'),
                    ])">
                        {{ __('UMKM') }}
                    </a>
                    <a href="{{ route('public.product.index') }}" @class([
                        'inline-flex min-h-11 items-center rounded-full px-3.5 text-sm transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2',
                        'font-semibold text-[#5C4033] underline decoration-[#C26A4A] decoration-2 underline-offset-2' => request()->routeIs('public.product.*'),
                        'font-medium text-[#6F5D50] hover:bg-[#F4EDE1] hover:text-[#3F2A22]' => ! request()->routeIs('public.product.*'),
                    ])">
                        {{ __('Produk') }}
                    </a>
                    <a href="{{ route('public.search') }}" @class([
                        'inline-flex min-h-11 items-center gap-1.5 rounded-full px-3.5 text-sm transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2',
                        'font-semibold text-[#5C4033] underline decoration-[#C26A4A] decoration-2 underline-offset-2' => request()->routeIs('public.search'),
                        'font-medium text-[#6F5D50] hover:bg-[#F4EDE1] hover:text-[#3F2A22]' => ! request()->routeIs('public.search'),
                    ])">
                        <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.35-4.35" />
                        </svg>
                        {{ __('Cari') }}
                    </a>
                    <a href="{{ route('public.about') }}" @class([
                        'inline-flex min-h-11 items-center rounded-full px-3.5 text-sm transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2',
                        'font-semibold text-[#5C4033] underline decoration-[#C26A4A] decoration-2 underline-offset-2' => request()->routeIs('public.about'),
                        'font-medium text-[#6F5D50] hover:bg-[#F4EDE1] hover:text-[#3F2A22]' => ! request()->routeIs('public.about'),
                    ])">
                        {{ __('Tentang') }}
                    </a>
                    <a href="{{ route('public.contact') }}" @class([
                        'inline-flex min-h-11 items-center rounded-full px-3.5 text-sm transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2',
                        'font-semibold text-[#5C4033] underline decoration-[#C26A4A] decoration-2 underline-offset-2' => request()->routeIs('public.contact'),
                        'font-medium text-[#6F5D50] hover:bg-[#F4EDE1] hover:text-[#3F2A22]' => ! request()->routeIs('public.contact'),
                    ])">
                        {{ __('Kontak') }}
                    </a>
                </nav>

                <!-- Desktop Right Side -->
                <div class="hidden shrink-0 items-center gap-1.5 lg:flex">
                    @auth
                        <span class="flex h-11 w-11 items-center justify-center rounded-full text-[#6F5D50] transition-colors duration-150 hover:bg-[#F4EDE1]" aria-hidden="true">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
                                <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
                            </svg>
                        </span>
                        <span class="hidden h-5 w-px bg-[#EDE8DC] sm:block" aria-hidden="true"></span>
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex min-h-11 items-center gap-2 rounded-full px-2.5 transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 hover:bg-[#F4EDE1]">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#5C4033] text-sm font-semibold text-white">{{ \Illuminate\Support\Str::initials(Auth::user()->name) }}</span>
                                    <span class="max-w-[10rem] truncate text-sm font-medium text-[#3F2A22]">{{ Auth::user()->name }}</span>
                                    <svg class="h-4 w-4 shrink-0 text-[#8A7464]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m6 9 6 6 6-6" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
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
                    @else
                        <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center rounded-full px-4 text-sm font-semibold text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] hover:text-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            {{ __('Masuk') }}
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex min-h-11 items-center gap-2 whitespace-nowrap rounded-full bg-[#3F2A22] px-5 text-sm font-semibold text-white shadow-sm transition-colors duration-150 hover:bg-[#5C4033] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            <svg class="h-4 w-4 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                                <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                                <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                            </svg>
                            {{ __('Daftarkan UMKM') }}
                            <svg class="h-4 w-4 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14" />
                                <path d="m12 5 7 7-7 7" />
                            </svg>
                        </a>
                    @endauth
                </div>

                <!-- Mobile Header -->
                <div class="flex shrink-0 items-center gap-1.5 lg:hidden">
                    @auth
                        <a href="{{ route('profile.edit') }}" aria-label="Profil {{ Auth::user()->name }}" class="flex h-11 w-11 items-center justify-center rounded-full bg-[#5C4033] text-sm font-semibold text-white transition-colors duration-150 hover:bg-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            {{ \Illuminate\Support\Str::initials(Auth::user()->name) }}
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex min-h-11 items-center gap-1.5 whitespace-nowrap rounded-full bg-[#3F2A22] px-4 text-sm font-semibold text-white transition-colors duration-150 hover:bg-[#5C4033] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            {{ __('Daftar') }}
                        </a>
                    @endauth
                    <button @click="open = ! open" type="button" :aria-label="open ? 'Tutup menu' : 'Buka menu'" :aria-expanded="open ? 'true' : 'false'" aria-controls="mobile-navigation" class="inline-flex h-11 w-11 items-center justify-center rounded-full text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': ! open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': ! open, 'inline-flex': open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div id="mobile-navigation" x-show="open" x-cloak class="relative lg:hidden">
            <div class="fixed inset-0 -z-10 bg-[#3F2A22]/40" @click="open = false" aria-hidden="true"></div>
             <div class="absolute inset-x-3 top-full z-50 mt-2 rounded-2xl border border-white/60 bg-white/90 p-3 shadow-[0_16px_40px_-16px_rgba(63,42,34,0.4)] backdrop-blur-md"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2">
                <div class="flex items-center justify-between gap-2">
                    <span class="flex items-center gap-2.5 px-1">
                        @if (!empty($settings['site.logo']))
                            <img src="{{ asset('storage/'.$settings['site.logo']) }}" alt="" class="h-8 w-8 rounded-lg object-contain">
                        @else
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#5C4033] text-white">
                                <x-application-logo class="h-4 w-4" />
                            </span>
                        @endif
                        <span class="flex flex-col justify-center leading-tight">
                            <span class="text-sm font-bold tracking-tight text-[#3F2A22]">{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}</span>
                            <span class="text-[11px] font-medium text-[#8A7464]">{{ $settings['site.tagline'] ?? 'Desa Salamnunggal' }}</span>
                        </span>
                    </span>
                    <button @click="open = false" type="button" aria-label="Tutup menu" class="inline-flex h-11 w-11 items-center justify-center rounded-full text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        <svg class="h-6 w-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mt-2 space-y-1" @click="open = false">
                    <a href="{{ url('/') }}" @class([
                        'flex min-h-11 items-center gap-3 rounded-xl px-3 text-sm transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]',
                        'bg-[#F4EDE1] font-semibold text-[#5C4033]' => request()->routeIs('home'),
                        'font-medium text-[#5F524A] hover:bg-[#FAF6F5]' => ! request()->routeIs('home'),
                    ])">
                        <svg class="h-5 w-5 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            <path d="M9 22V12h6v10" />
                        </svg>
                        {{ __('Beranda') }}
                    </a>
                    <a href="{{ route('public.umkm.index') }}" @class([
                        'flex min-h-11 items-center gap-3 rounded-xl px-3 text-sm transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]',
                        'bg-[#F4EDE1] font-semibold text-[#5C4033]' => request()->routeIs('public.umkm.*'),
                        'font-medium text-[#5F524A] hover:bg-[#FAF6F5]' => ! request()->routeIs('public.umkm.*'),
                    ])">
                        <svg class="h-5 w-5 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                            <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                            <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                        </svg>
                        {{ __('UMKM') }}
                    </a>
                    <a href="{{ route('public.product.index') }}" @class([
                        'flex min-h-11 items-center gap-3 rounded-xl px-3 text-sm transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]',
                        'bg-[#F4EDE1] font-semibold text-[#5C4033]' => request()->routeIs('public.product.*'),
                        'font-medium text-[#5F524A] hover:bg-[#FAF6F5]' => ! request()->routeIs('public.product.*'),
                    ])">
                        <svg class="h-5 w-5 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m7.5 4.27 9 5.15" />
                            <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                            <path d="m3.3 7 8.7 5 8.7-5" />
                            <path d="M12 22V12" />
                        </svg>
                        {{ __('Produk') }}
                    </a>
                    <a href="{{ route('public.search') }}" @class([
                        'flex min-h-11 items-center gap-3 rounded-xl px-3 text-sm transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]',
                        'bg-[#F4EDE1] font-semibold text-[#5C4033]' => request()->routeIs('public.search'),
                        'font-medium text-[#5F524A] hover:bg-[#FAF6F5]' => ! request()->routeIs('public.search'),
                    ])">
                        <svg class="h-5 w-5 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.35-4.35" />
                        </svg>
                        {{ __('Cari') }}
                    </a>
                    <a href="{{ route('public.about') }}" @class([
                        'flex min-h-11 items-center gap-3 rounded-xl px-3 text-sm transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]',
                        'bg-[#F4EDE1] font-semibold text-[#5C4033]' => request()->routeIs('public.about'),
                        'font-medium text-[#5F524A] hover:bg-[#FAF6F5]' => ! request()->routeIs('public.about'),
                    ])">
                        <svg class="h-5 w-5 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 16v-4" />
                            <path d="M12 8h.01" />
                        </svg>
                        {{ __('Tentang') }}
                    </a>
                    <a href="{{ route('public.contact') }}" @class([
                        'flex min-h-11 items-center gap-3 rounded-xl px-3 text-sm transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]',
                        'bg-[#F4EDE1] font-semibold text-[#5C4033]' => request()->routeIs('public.contact'),
                        'font-medium text-[#5F524A] hover:bg-[#FAF6F5]' => ! request()->routeIs('public.contact'),
                    ])">
                        <svg class="h-5 w-5 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                        </svg>
                        {{ __('Kontak') }}
                    </a>
                </div>

                <div class="my-3 border-t border-[#EDE8DC]" aria-hidden="true"></div>

                @auth
                    <div class="space-y-1" @click="open = false">
                        <div class="px-3 py-2">
                            <div class="text-sm font-semibold text-[#3F2A22]">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-[#8A7464]">{{ Auth::user()->email }}</div>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 text-sm font-medium text-[#5F524A] transition-colors duration-150 hover:bg-[#FAF6F5] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                            {{ __('Profil') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex min-h-11 w-full items-center gap-3 rounded-xl px-3 text-sm font-medium text-[#5F524A] transition-colors duration-150 hover:bg-[#FAF6F5] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                                {{ __('Keluar') }}
                            </button>
                        </form>
                    </div>
                @else
                    <div class="space-y-1" @click="open = false">
                        <a href="{{ route('login') }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 text-sm font-semibold text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]">
                            <svg class="h-5 w-5 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                <path d="m10 17 5-5-5-5" />
                                <path d="M15 12H3" />
                            </svg>
                            {{ __('Masuk') }}
                        </a>
                        <div class="pt-1">
                            <a href="{{ route('register') }}" class="flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-[#3F2A22] px-4 text-sm font-semibold text-white transition-colors duration-150 hover:bg-[#5C4033] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                <svg class="h-4 w-4 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                                    <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                                    <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                                </svg>
                                {{ __('Daftarkan UMKM') }}
                            </a>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    @elseif (Auth::user()?->hasRole('administrator'))
        {{-- ============ ADMIN NAVBAR ============ --}}
        <div class="container-page pt-3 sm:pt-4">
            <div class="flex h-16 items-center justify-between gap-4 rounded-2xl border border-[#ECE5D9] bg-white px-4 shadow-[0_2px_12px_rgba(63,42,34,0.06)] sm:px-5">
                <!-- Brand -->
                <a href="{{ route('admin.dashboard') }}" aria-label="{{ config('app.name', 'Portal UMKM Salamnunggal') }}" class="flex shrink-0 items-center gap-2.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                    @if (!empty($settings['site.logo']))
                        <img src="{{ asset('storage/'.$settings['site.logo']) }}" alt="{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}" class="h-10 w-10 shrink-0 rounded-xl object-contain">
                    @else
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#5C4033] text-white">
                            <x-application-logo class="h-5 w-5" />
                        </span>
                    @endif
                    <span class="flex flex-col justify-center leading-tight">
                        <span class="text-[15px] font-bold tracking-tight text-[#3F2A22]">{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}</span>
                        <span class="text-xs font-medium text-[#8A7464]">Panel Administrasi</span>
                    </span>
                    <span class="hidden sm:inline-flex rounded-full bg-[#F4EDE1] px-2.5 py-0.5 text-xs font-semibold text-[#5C4033]">ADMIN</span>
                </a>

                <!-- Desktop Navigation -->
                <nav aria-label="Navigasi utama" class="hidden lg:flex lg:items-center lg:gap-0.5">

{{-- Dashboard --}}
                        <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Dashboard') }}
                        </x-responsive-nav-link>

                        {{-- Verifikasi Dropdown --}}
                        <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                            <button @click="open = ! open" @class([
                                'inline-flex min-h-11 items-center gap-1.5 rounded-full px-3 text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2',
                                'bg-[#F4EDE1] text-[#C26A4A]' => request()->routeIs('admin.umkm.verification.*') || request()->routeIs('admin.products.verification.*') || request()->routeIs('admin.owner-verification.*'),
                                'text-[#6F5D50] hover:bg-[#F4EDE1] hover:text-[#3F2A22]' => !request()->routeIs('admin.umkm.verification.*') && !request()->routeIs('admin.products.verification.*') && !request()->routeIs('admin.owner-verification.*'),
                            ])>
                                <svg class="h-4 w-4 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 12l2 2 4-4" />
                                    <circle cx="12" cy="12" r="10" />
                                </svg>
                                Verifikasi
                                @if($totalPending > 0)
                                    <span class="ml-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-[#C26A4A] px-1.5 text-xs font-bold text-white">{{ $totalPending }}</span>
                                @endif
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-95" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-95" x-transition:leave-end="opacity-0 scale-95" class="absolute z-50 mt-2 w-56 rounded-2xl border border-[#ECE5D9] bg-white shadow-[0_8px_24px_-8px_rgba(63,42,34,0.2)]" style="display: none;" @click="open = false">
                                <div class="p-2">
                                    <a href="{{ route('admin.umkm.verification.index') }}" class="flex items-center gap-3 rounded-full px-3 py-2 text-sm font-medium text-slate-700 transition-colors duration-150 hover:bg-[#F4EDE1] hover:text-[#3F2A22]">
                                        <svg class="h-4 w-4 text-[#8A7464]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                                            <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                                            <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                                        </svg>
                                        UMKM
                                        @if($pendingUmkm > 0)
                                            <span class="ml-auto inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-[#F4EDE1] px-1.5 text-xs font-bold text-[#5C4033]">{{ $pendingUmkm }}</span>
                                        @endif
                                    </a>
                                    <a href="{{ route('admin.products.verification.index') }}" class="flex items-center gap-3 rounded-full px-3 py-2 text-sm font-medium text-slate-700 transition-colors duration-150 hover:bg-[#F4EDE1] hover:text-[#C26A4A]">
                                        <svg class="h-5 w-5 text-[#8A7464]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m7.5 4.27 9 5.15" />
                                            <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                                            <path d="m3.3 7 8.7 5 8.7-5" />
                                            <path d="M12 22V12" />
                                        </svg>
                                        Produk
                                        @if($pendingProduct > 0)
                                            <span class="ml-auto inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-[#F4EDE1] px-1.5 text-xs font-bold text-[#5C4033]">{{ $pendingProduct }}</span>
                                        @endif
                                    </a>
                                    <a href="{{ route('admin.owner-verification.index') }}" class="flex items-center gap-3 rounded-full px-3 py-2 text-sm font-medium text-slate-700 transition-colors duration-150 hover:bg-[#F4EDE1] hover:text-[#C26A4A]">
                                        <svg class="h-5 w-5 text-[#8A7464]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                            <circle cx="9" cy="7" r="4" />
                                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                        </svg>
                                        Owner
                                        @if($pendingOwner > 0)
                                            <span class="ml-auto inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-[#F4EDE1] px-1.5 text-xs font-bold text-[#5C4033]">{{ $pendingOwner }}</span>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Data Dropdown --}}
                        <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                            <button @click="open = ! open" @class([
                                'inline-flex min-h-11 items-center gap-1.5 rounded-full px-3 text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2',
                                'bg-[#F4EDE1] text-[#C26A4A]' => request()->routeIs('admin.umkms.*') || request()->routeIs('admin.products.*') || request()->routeIs('admin.users.*') || request()->routeIs('admin.categories.*'),
                                'text-[#6F5D50] hover:bg-[#F4EDE1] hover:text-[#3F2A22]' => !request()->routeIs('admin.umkms.*') && !request()->routeIs('admin.products.*') && !request()->routeIs('admin.users.*') && !request()->routeIs('admin.categories.*'),
                            ])>
                                <svg class="h-4 w-4 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="7" height="7" x="3" y="3" rx="1" />
                                    <rect width="7" height="7" x="14" y="3" rx="1" />
                                    <rect width="7" height="7" x="14" y="14" rx="1" />
                                    <rect width="7" height="7" x="3" y="14" rx="1" />
                                </svg>
                                Data
                                <svg class="h-3.5 w-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-95" x-transition:leave-end="opacity-0 scale-95" class="absolute z-50 mt-2 w-56 rounded-2xl border border-[#ECE5D9] bg-white shadow-[0_8px_24px_-8px_rgba(63,42,34,0.2)]" style="display: none;" @click="open = false">
                                <div class="p-2">
                                    <a href="{{ route('admin.umkms.index') }}" class="flex items-center gap-3 rounded-full px-3 py-2 text-sm font-medium text-slate-700 transition-colors duration-150 hover:bg-[#F4EDE1] hover:text-[#C26A4A]">
                                        <svg class="h-4 w-4 text-[#8A7464]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                                            <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                                            <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                                        </svg>
                                        UMKM
                                    </a>
                                    <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 rounded-full px-3 py-2 text-sm font-medium text-slate-700 transition-colors duration-150 hover:bg-[#F4EDE1] hover:text-[#C26A4A]">
                                        <svg class="h-4 w-4 text-[#8A7464]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m7.5 4.27 9 5.15" />
                                            <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                                            <path d="m3.3 7 8.7 5 8.7-5" />
                                            <path d="M12 22V12" />
                                        </svg>
                                        Produk
                                    </a>
                                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 rounded-full px-3 py-2 text-sm font-medium text-slate-700 transition-colors duration-150 hover:bg-[#F4EDE1] hover:text-[#C26A4A]">
                                        <svg class="h-4 w-4 text-[#8A7464]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                            <circle cx="9" cy="7" r="4" />
                                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                        </svg>
                                        Pengguna
                                    </a>
                                    <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 rounded-full px-3 py-2 text-sm font-medium text-slate-700 transition-colors duration-150 hover:bg-[#F4EDE1] hover:text-[#C26A4A]">
                                        <svg class="h-4 w-4 text-[#8A7464]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 1 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z" />
                                        </svg>
                                        Kategori
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Sistem Dropdown --}}
                        <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                            <button @click="open = ! open" @class([
                                'inline-flex min-h-11 items-center gap-1.5 rounded-full px-3 text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2',
                                'bg-[#F4EDE1] text-[#C26A4A]' => request()->routeIs('admin.settings.*'),
                                'text-[#6F5D50] hover:bg-[#F4EDE1] hover:text-[#3F2A22]' => !request()->routeIs('admin.settings.*'),
                            ])>
                                <svg class="h-4 w-4 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                Sistem
                                <svg class="h-3.5 w-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="absolute z-50 mt-2 w-56 rounded-2xl border border-[#ECE5D9] bg-white shadow-[0_8px_24px_-8px_rgba(63,42,34,0.2)]" style="display: none;">
                                <div class="p-2">
                                    <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 rounded-full px-3 py-2 text-sm text-slate-700 transition-colors duration-150 hover:bg-[#F4EDE1] hover:text-[#C26A4A]">
                                        <svg class="h-4 w-4 text-[#8A7464]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        Pengaturan Portal
                                    </a>
                                </div>
                            </div>
                        </div>
                </nav>

                {{-- Settings Dropdown (Admin) --}}
                <div class="hidden lg:flex lg:items-center lg:gap-3">
                    <a href="{{ route('home') }}" class="inline-flex min-h-10 items-center gap-1.5 rounded-lg border border-[#ECE5D9] px-3.5 text-sm font-medium text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] hover:text-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                            <polyline points="15 3 21 3 21 9" />
                            <line x1="10" y2="14" x2="21" y3="9" />
                        </svg>
                        Lihat Portal
                    </a>

                    {{-- Administrator Profile Dropdown --}}
                    <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                        <button @click="open = ! open" class="inline-flex min-h-10 items-center gap-2 rounded-full px-2.5 transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 hover:bg-[#F4EDE1]">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#5C4033] text-sm font-semibold text-white">{{ \Illuminate\Support\Str::initials(Auth::user()->name) }}</span>
                            <span class="hidden sm:inline text-sm font-medium text-slate-700">Administrator</span>
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="absolute z-50 mt-2 w-56 rounded-2xl border border-[#ECE5D9] bg-white shadow-[0_8px_24px_-8px_rgba(63,42,34,0.2)]" style="display: none;">
                            <div class="p-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="flex-shrink-0 rounded-full bg-[#F4EDE1] px-2 py-1 text-xs font-semibold text-[#5C4033]">ADMIN</div>
                                    <div>
                                        <div class="font-medium text-base text-slate-900">{{ Auth::user()->name }}</div>
                                        <div class="text-xs text-[#8A7464]">{{ Auth::user()->email }}</div>
                                    </div>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 rounded-full px-3 py-2 text-sm font-medium text-[#5F524A] transition-colors duration-150 hover:bg-[#FAF6F5] hover:text-[#C26A4A]">
                                    <svg class="h-4 w-4 text-[#8A7464]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                    Profil Saya
                                </a>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 rounded-full px-3 py-2 text-sm font-medium text-[#5F524A] transition-colors duration-150 hover:bg-[#FAF6F5] hover:text-[#C26A4A]">
                                    <svg class="h-4 w-4 text-[#8A7464]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>
                                    Ubah Password
                                </a>
                                <div class="my-1 border-t border-[#ECE5D9]"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full justify-center rounded-full px-3 py-2 text-sm font-medium text-[#5F524A] transition-colors duration-150 hover:bg-[#FAF6F5] hover:text-[#C26A4A]">
                                        <svg class="h-4 w-4 text-[#8A7464]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                            <polyline points="16 17 21 12 16 7" />
                                            <line x1="21" y2="12" x2="9" y3="12" />
                                        </svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>

            </div>
            </div>

                <!-- Hamburger -->
                <div class="-me-1 flex items-center lg:hidden">
                    <button @click="open = ! open" type="button" :aria-label="open ? 'Tutup menu' : 'Buka menu'" :aria-expanded="open ? 'true' : 'false'" aria-controls="admin-mobile-navigation" class="inline-flex items-center justify-center p-2.5 rounded-xl bg-[#5C4033] text-white transition-colors duration-150 hover:bg-[#F4EDE1] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 transition duration-150 ease-in-out">
                        <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': ! open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': ! open, 'inline-flex': open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div id="admin-mobile-navigation" x-show="open" x-cloak class="border-t border-slate-100 bg-white lg:hidden">
            <div class="container-page py-3">
                <div class="space-y-1">
                    @if (Auth::user()?->hasRole('administrator'))
                        <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Dashboard') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.umkm.verification.index')" :active="request()->routeIs('admin.umkm.verification.*')">
                            {{ __('Verifikasi UMKM') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.umkms.index')" :active="request()->routeIs('admin.umkms.*')">
                            {{ __('Kelola UMKM') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">
                            {{ __('Kelola Produk') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.products.verification.index')" :active="request()->routeIs('admin.products.verification.*')">
                            {{ __('Verifikasi Produk') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.owner-verification.index')" :active="request()->routeIs('admin.owner-verification.*')">
                            {{ __('Verifikasi Owner') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('Kelola Pengguna') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">
                            {{ __('Kelola Kategori') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')">
                            {{ __('Pengaturan Portal') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                            {{ __('Lihat Portal') }}
                        </x-responsive-nav-link>
                    @endif
                </div>

                <!-- Responsive Settings Options -->
                @auth
                    <div class="mt-3 space-y-1 border-t border-slate-100 pt-3">
                        <div class="px-4 py-2">
                            <div class="font-medium text-base text-slate-900">{{ Auth::user()->name }}</div>
                            <div class="font-medium text-sm text-slate-500">{{ Auth::user()->email }}</div>
                        </div>

                        <x-responsive-nav-link :href="route('profile.edit')">
                            {{ __('Profil') }}
                        </x-responsive-nav-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-responsive-nav-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Keluar') }}
                            </x-responsive-nav-link>
                        </form>
                    </div>
                @else
                    <div class="mt-3 space-y-1 border-t border-slate-100 pt-3">
                        <x-responsive-nav-link :href="route('login')">
                            {{ __('Masuk') }}
                        </x-responsive-nav-link>
                        <div class="px-1 pt-1">
                            <a href="{{ route('register') }}" class="flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-[#3F2A22] px-4 text-sm font-semibold text-white transition-colors duration-150 hover:bg-[#5C4033] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                {{ __('Daftarkan UMKM') }}
                            </a>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    @else
        @include('layouts.owner-navbar')
    @endif
</nav>

