<x-app-layout title="Kontak">
    @php
        $hasContact = $settings->has('contact.address')
            || $settings->has('contact.phone')
            || $settings->has('contact.email')
            || $settings->has('contact.hours');
    @endphp

    <div class="py-12 sm:py-16">
        <div class="container-page">
            <div class="mx-auto max-w-3xl">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 rounded-md text-sm font-medium text-emerald-600 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                    <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                    Kembali ke Beranda
                </a>

                <h1 class="mt-6 text-3xl font-semibold tracking-tight text-slate-900">Kontak</h1>

                <p class="mt-4 max-w-2xl leading-relaxed text-slate-600">
                    Hubungi pengelola Portal UMKM Desa Salamnunggal untuk informasi lebih lanjut, pendataan usaha, atau pendampingan.
                </p>

                @if ($hasContact)
                    <section class="mt-12">
                        <h2 class="text-xl font-semibold tracking-tight text-slate-900">Informasi Kontak</h2>
                        <div class="mt-5 overflow-hidden rounded-2xl bg-white shadow-sm">
                            <dl class="divide-y divide-slate-100">
                                @if ($settings->get('contact.address'))
                                    <div class="flex items-start gap-3 p-5 sm:px-6">
                                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0Z" />
                                            <circle cx="12" cy="10" r="3" />
                                        </svg>
                                        <div class="min-w-0">
                                            <dt class="text-sm font-medium text-slate-500">Alamat</dt>
                                            <dd class="mt-1 break-words leading-relaxed text-slate-900">{{ $settings->get('contact.address') }}</dd>
                                        </div>
                                    </div>
                                @endif

                                @if ($settings->get('contact.phone'))
                                    <div class="flex items-start gap-3 p-5 sm:px-6">
                                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92Z" />
                                        </svg>
                                        <div class="min-w-0">
                                            <dt class="text-sm font-medium text-slate-500">Telepon</dt>
                                            <dd class="mt-1">
                                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $settings->get('contact.phone')) }}" class="break-words font-medium text-emerald-600 hover:underline focus:outline-none focus-visible:underline">{{ $settings->get('contact.phone') }}</a>
                                            </dd>
                                        </div>
                                    </div>
                                @endif

                                @if ($settings->get('contact.email'))
                                    <div class="flex items-start gap-3 p-5 sm:px-6">
                                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="4" width="20" height="16" rx="2" />
                                            <path d="m22 7-10 6L2 7" />
                                        </svg>
                                        <div class="min-w-0">
                                            <dt class="text-sm font-medium text-slate-500">Email</dt>
                                            <dd class="mt-1">
                                                <a href="mailto:{{ $settings->get('contact.email') }}" class="break-words font-medium text-emerald-600 hover:underline focus:outline-none focus-visible:underline">{{ $settings->get('contact.email') }}</a>
                                            </dd>
                                        </div>
                                    </div>
                                @endif

                                @if ($settings->get('contact.hours'))
                                    <div class="flex items-start gap-3 p-5 sm:px-6">
                                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10" />
                                            <path d="M12 6v6l4 2" />
                                        </svg>
                                        <div class="min-w-0">
                                            <dt class="text-sm font-medium text-slate-500">Jam Layanan</dt>
                                            <dd class="mt-1 break-words leading-relaxed text-slate-900">{{ $settings->get('contact.hours') }}</dd>
                                        </div>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </section>
                @else
                    <div class="mt-12 rounded-2xl bg-white px-5 py-8 text-center shadow-sm sm:py-10">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">
                            <svg class="h-6 w-6 text-slate-500" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2" />
                                <path d="m22 7-10 6L2 7" />
                            </svg>
                        </div>
                        <h2 class="mt-4 text-lg font-semibold tracking-tight text-slate-900">Informasi kontak belum tersedia.</h2>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-slate-600">
                            Pengelola belum melengkapi informasi kontak. Anda tetap dapat menjelajahi UMKM dan produk di portal ini.
                        </p>
                    </div>
                @endif

                <div class="mt-12 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <a href="{{ route('public.umkm.index') }}" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 text-sm font-semibold text-white transition duration-300 hover:bg-emerald-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 sm:w-auto">
                        Lihat UMKM
                    </a>
                    <a href="{{ route('public.product.index') }}" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-6 text-sm font-semibold text-emerald-700 transition duration-300 hover:border-emerald-300 hover:bg-emerald-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 sm:w-auto">
                        Lihat Produk
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>