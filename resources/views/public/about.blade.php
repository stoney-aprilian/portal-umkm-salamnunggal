<x-app-layout title="Tentang">
    <div class="py-12 sm:py-16">
        <div class="container-page">
            <div class="mx-auto max-w-3xl">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 rounded-md text-sm font-medium text-emerald-600 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                    <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                    Kembali ke Beranda
                </a>

                <h1 class="mt-6 text-3xl font-semibold tracking-tight text-slate-900">Tentang Portal</h1>

                <p class="mt-4 max-w-2xl leading-relaxed text-slate-600">
                    Portal UMKM Salamnunggal adalah portal digital UMKM Desa yang berfungsi sebagai media promosi,
                    pendataan, serta publikasi UMKM secara terpusat.
                </p>

                <p class="mt-5 max-w-2xl leading-relaxed text-slate-600">
                    Portal ini bertujuan mempertemukan masyarakat dengan pelaku UMKM melalui penyajian informasi
                    yang akurat, terpercaya, dan mudah diakses.
                </p>

                <section class="mt-12">
                    <h2 class="text-xl font-semibold tracking-tight text-slate-900">Apa yang dapat Anda lakukan di sini?</h2>
                    <ul class="mt-5 space-y-4">
                        <li class="flex items-start gap-3">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="min-w-0 max-w-2xl leading-relaxed text-slate-600">Menjelajahi UMKM di Desa Salamnunggal beserta produk unggulannya.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="min-w-0 max-w-2xl leading-relaxed text-slate-600">Menemukan UMKM dan produk melalui pencarian maupun kategori.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="min-w-0 max-w-2xl leading-relaxed text-slate-600">Menghubungi pemilik UMKM secara langsung melalui kontak yang tersedia.</span>
                        </li>
                    </ul>
                </section>

                <div class="mt-12 max-w-2xl rounded-2xl bg-emerald-50 px-5 py-4 sm:px-6 sm:py-5">
                    <p class="text-center text-sm leading-relaxed text-emerald-800">
                        Portal ini merupakan media promosi dan direktori UMKM, bukan marketplace. Transaksi
                        dilakukan di luar sistem.
                    </p>
                </div>

                <div class="mt-12 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <a href="{{ route('public.umkm.index') }}" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 text-sm font-semibold text-white transition duration-300 hover:bg-emerald-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 sm:w-auto">
                        Jelajahi UMKM
                    </a>
                    <a href="{{ route('public.product.index') }}" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-6 text-sm font-semibold text-emerald-700 transition duration-300 hover:border-emerald-300 hover:bg-emerald-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 sm:w-auto">
                        Lihat Produk
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-6 text-sm font-semibold text-emerald-700 transition duration-300 hover:border-emerald-300 hover:bg-emerald-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 sm:w-auto">
                        Daftarkan UMKM
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>