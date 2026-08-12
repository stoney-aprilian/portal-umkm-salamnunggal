<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-slate-200 bg-white">
    <!-- Primary Navigation Menu -->
    <div class="container-page">
        <div class="flex h-14 items-center justify-between gap-4">
            <!-- Brand -->
            <a href="{{ Auth::check() ? route('dashboard') : url('/') }}" aria-label="{{ config('app.name', 'Portal UMKM Salamnunggal') }}" class="flex shrink-0 items-center gap-2.5">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white">
                    <x-application-logo class="h-5 w-5" />
                </span>
                <span class="flex flex-col justify-center leading-tight">
                    <span class="text-[15px] font-semibold tracking-tight text-slate-900">Portal UMKM</span>
                    <span class="text-xs font-medium text-slate-500">Desa Salamnunggal</span>
                </span>
            </a>

            <!-- Navigation Links -->
            <div class="hidden lg:flex lg:items-center lg:gap-1">
                @if (Auth::user()?->hasRole('administrator'))
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.umkm.verification.index')" :active="request()->routeIs('admin.umkm.verification.*')">
                        {{ __('Verifikasi UMKM') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.products.verification.index')" :active="request()->routeIs('admin.products.verification.*')">
                        {{ __('Verifikasi Produk') }}
                    </x-nav-link>
                    <x-nav-link :href="route('public.umkm.index')" :active="request()->routeIs('public.*')">
                        {{ __('Lihat Portal') }}
                    </x-nav-link>
                @elseif (Auth::check())
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @php $ownerUmkm = Auth::user()->umkm; @endphp
                    @if ($ownerUmkm === null)
                        <x-nav-link :href="route('owner.umkm.create')" :active="request()->routeIs('owner.umkm.*')">
                            {{ __('Ajukan UMKM') }}
                        </x-nav-link>
                    @else
                        @if (in_array($ownerUmkm->status, ['draft', 'pending', 'needs_revision', 'rejected'], true))
                            <x-nav-link :href="route('owner.umkm.edit', $ownerUmkm)" :active="request()->routeIs('owner.umkm.*')">
                                {{ __('UMKM Saya') }}
                            </x-nav-link>
                        @endif
                        @if ($ownerUmkm->status === 'approved')
                            <x-nav-link :href="route('owner.products.index', $ownerUmkm)" :active="request()->routeIs('owner.products.*')">
                                {{ __('Produk') }}
                            </x-nav-link>
                        @endif
                    @endif
                @else
                    <x-nav-link :href="url('/')" :active="request()->is('/')">
                        {{ __('Beranda') }}
                    </x-nav-link>
                    <x-nav-link :href="route('public.umkm.index')" :active="request()->routeIs('public.umkm.*')">
                        {{ __('UMKM') }}
                    </x-nav-link>
                    <x-nav-link :href="route('public.product.index')" :active="request()->routeIs('public.product.*')">
                        {{ __('Produk') }}
                    </x-nav-link>
                    <x-nav-link :href="route('public.search')" :active="request()->routeIs('public.search')">
                        {{ __('Cari') }}
                    </x-nav-link>
                    <x-nav-link :href="route('public.about')" :active="request()->routeIs('public.about')">
                        {{ __('Tentang') }}
                    </x-nav-link>
                    <x-nav-link :href="route('public.contact')" :active="request()->routeIs('public.contact')">
                        {{ __('Kontak') }}
                    </x-nav-link>
                @endif
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden lg:flex lg:items-center lg:gap-1">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex min-h-11 items-center gap-1.5 rounded-xl px-3 text-sm font-medium text-slate-600 transition duration-150 hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                                <span class="max-w-[12rem] truncate">{{ Auth::user()->name }}</span>

                                <svg class="h-4 w-4 shrink-0 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profil') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
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
                    <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center rounded-xl px-3 text-sm font-medium text-slate-600 transition duration-150 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                        {{ __('Masuk') }}
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex min-h-11 items-center whitespace-nowrap rounded-xl bg-emerald-600 px-5 text-sm font-semibold text-white transition duration-300 hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        {{ __('Daftarkan UMKM') }}
                    </a>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-1 flex items-center lg:hidden">
                <button @click="open = ! open" type="button" aria-label="Buka menu" :aria-expanded="open ? 'true' : 'false'" aria-controls="mobile-navigation" class="inline-flex items-center justify-center p-2.5 rounded-xl text-slate-500 hover:text-slate-700 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div id="mobile-navigation" :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-slate-100 bg-white lg:hidden">
        <div class="container-page py-3">
            <div class="space-y-1">
                @if (Auth::user()?->hasRole('administrator'))
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.umkm.verification.index')" :active="request()->routeIs('admin.umkm.verification.*')">
                        {{ __('Verifikasi UMKM') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.products.verification.index')" :active="request()->routeIs('admin.products.verification.*')">
                        {{ __('Verifikasi Produk') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('public.umkm.index')" :active="request()->routeIs('public.*')">
                        {{ __('Lihat Portal') }}
                    </x-responsive-nav-link>
                @elseif (Auth::check())
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    @php $ownerUmkm = Auth::user()->umkm; @endphp
                    @if ($ownerUmkm === null)
                        <x-responsive-nav-link :href="route('owner.umkm.create')" :active="request()->routeIs('owner.umkm.*')">
                            {{ __('Ajukan UMKM') }}
                        </x-responsive-nav-link>
                    @else
                        @if (in_array($ownerUmkm->status, ['draft', 'pending', 'needs_revision', 'rejected'], true))
                            <x-responsive-nav-link :href="route('owner.umkm.edit', $ownerUmkm)" :active="request()->routeIs('owner.umkm.*')">
                                {{ __('UMKM Saya') }}
                            </x-responsive-nav-link>
                        @endif
                        @if ($ownerUmkm->status === 'approved')
                            <x-responsive-nav-link :href="route('owner.products.index', $ownerUmkm)" :active="request()->routeIs('owner.products.*')">
                                {{ __('Produk') }}
                            </x-responsive-nav-link>
                        @endif
                    @endif
                @else
                    <x-responsive-nav-link :href="url('/')" :active="request()->is('/')">
                        {{ __('Beranda') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('public.umkm.index')" :active="request()->routeIs('public.umkm.*')">
                        {{ __('UMKM') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('public.product.index')" :active="request()->routeIs('public.product.*')">
                        {{ __('Produk') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('public.search')" :active="request()->routeIs('public.search')">
                        {{ __('Cari') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('public.about')" :active="request()->routeIs('public.about')">
                        {{ __('Tentang') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('public.contact')" :active="request()->routeIs('public.contact')">
                        {{ __('Kontak') }}
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
                        <a href="{{ route('register') }}" class="flex min-h-11 w-full items-center justify-center rounded-xl bg-emerald-600 px-4 text-sm font-semibold text-white transition duration-300 hover:bg-emerald-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                            {{ __('Daftarkan UMKM') }}
                        </a>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</nav>
