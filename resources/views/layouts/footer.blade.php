<footer class="mt-12 border-t border-slate-200 bg-white">
    <div class="container-page py-10 sm:py-12">
        <div class="grid gap-10 lg:grid-cols-12 lg:gap-8">
            <!-- Brand -->
            <div class="lg:col-span-5">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5" aria-label="{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white">
                        <x-application-logo class="h-5 w-5" />
                    </span>
                    <span class="flex flex-col justify-center leading-tight">
                        <span class="text-[15px] font-semibold tracking-tight text-slate-900">{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}</span>
                        <span class="text-xs font-medium text-slate-500">Desa Salamnunggal</span>
                    </span>
                </a>
                <p class="mt-3 max-w-sm text-sm leading-relaxed text-slate-500">
                    Media promosi dan publikasi UMKM Desa Salamnunggal untuk mempertemukan masyarakat dengan pelaku usaha lokal.
                </p>
                @if (!empty($settings['contact.address']))
                    <p class="mt-3 text-sm text-slate-600">{{ $settings['contact.address'] }}</p>
                @endif
            </div>

            <!-- Jelajahi + Informasi -->
            <div class="grid grid-cols-2 gap-x-8 sm:gap-x-10 lg:col-span-4">
                <nav aria-label="Jelajahi">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Jelajahi</p>
                    <ul class="mt-3 flex flex-wrap gap-2.5 text-sm">
                        <li><a href="{{ url('/') }}" class="inline-flex min-h-11 items-center rounded-lg bg-slate-50 px-2.5 font-medium text-slate-700 transition duration-150 hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">Beranda</a></li>
                        <li><a href="{{ route('public.umkm.index') }}" class="inline-flex min-h-11 items-center rounded-lg bg-slate-50 px-2.5 font-medium text-slate-700 transition duration-150 hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">UMKM</a></li>
                        <li><a href="{{ route('public.product.index') }}" class="inline-flex min-h-11 items-center rounded-lg bg-slate-50 px-2.5 font-medium text-slate-700 transition duration-150 hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">Produk</a></li>
                        <li><a href="{{ route('public.search') }}" class="inline-flex min-h-11 items-center rounded-lg bg-slate-50 px-2.5 font-medium text-slate-700 transition duration-150 hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">Cari</a></li>
                    </ul>
                </nav>
                <nav aria-label="Informasi">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Informasi</p>
                    <ul class="mt-3 flex flex-wrap gap-2.5 text-sm">
                        <li><a href="{{ route('public.about') }}" class="inline-flex min-h-11 items-center rounded-lg bg-slate-50 px-2.5 font-medium text-slate-700 transition duration-150 hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">Tentang</a></li>
                        <li><a href="{{ route('public.contact') }}" class="inline-flex min-h-11 items-center rounded-lg bg-slate-50 px-2.5 font-medium text-slate-700 transition duration-150 hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">Kontak</a></li>
                        <li><a href="{{ route('register') }}" class="inline-flex min-h-11 items-center rounded-lg bg-slate-50 px-2.5 font-medium text-slate-700 transition duration-150 hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">Daftarkan UMKM</a></li>
                    </ul>
                </nav>
            </div>

            <!-- Kontak -->
            <div class="lg:col-span-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kontak</p>
                @php
                    $hasContactInfo = !empty($settings['contact.phone'])
                        || !empty($settings['contact.email'])
                        || !empty($settings['contact.hours']);
                @endphp
                @if ($hasContactInfo)
                    <ul class="mt-3 flex flex-wrap gap-2.5 text-sm">
                        @if (!empty($settings['contact.phone']))
                            <li><a href="tel:{{ preg_replace('/[^\d+]/', '', (string) $settings['contact.phone']) }}" class="inline-flex min-h-11 items-center rounded-lg bg-slate-50 px-2.5 font-medium text-slate-700 transition duration-150 hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">{{ $settings['contact.phone'] }}</a></li>
                        @endif
                        @if (!empty($settings['contact.email']))
                            <li><a href="mailto:{{ $settings['contact.email'] }}" class="inline-flex min-h-11 items-center rounded-lg bg-slate-50 px-2.5 font-medium text-slate-700 transition duration-150 hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">{{ $settings['contact.email'] }}</a></li>
                        @endif
                        @if (!empty($settings['contact.hours']))
                            <li class="inline-flex min-h-11 items-center font-normal text-slate-500">{{ $settings['contact.hours'] }}</li>
                        @endif
                    </ul>
                @else
                    <p class="mt-3 max-w-xs text-sm leading-relaxed text-slate-500">Informasi kontak belum tersedia. Silakan kunjungi halaman Kontak untuk detail lebih lanjut.</p>
                @endif
            </div>
        </div>

        <div class="mt-12">
            <div class="h-0.5 w-8 bg-emerald-600" aria-hidden="true"></div>
            <p class="mt-4 text-sm text-slate-500">
                &copy; {{ now()->year }} {{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }} &middot; Seluruh hak cipta dilindungi.
            </p>
        </div>
    </div>
</footer>
