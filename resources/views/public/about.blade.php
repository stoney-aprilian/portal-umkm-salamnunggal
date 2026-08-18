<x-app-layout title="Tentang">
    <div class="py-10 sm:py-14">
        <div class="container-page">
            {{-- ============ HERO ============ --}}
            <section class="grid items-center gap-8 lg:grid-cols-12">
                <div class="lg:col-span-7">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#C26A4A]">Tentang Portal</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-[#3F2A22] sm:text-4xl">Memperkenalkan UMKM Lokal Salamnunggal ke Lebih Banyak Orang</h1>
                    <p class="mt-4 max-w-xl text-base leading-relaxed text-[#6F5D50]">Portal UMKM Salamnunggal adalah ruang digital untuk menemukan, mengenal, dan terhubung dengan UMKM serta produk lokal Desa Salamnunggal.</p>
                </div>
                <div class="lg:col-span-5">
                    <div class="relative overflow-hidden rounded-2xl border border-[#ECE5D9] bg-[#FAF6F5] p-8 sm:p-10">
                        <svg class="pointer-events-none absolute -right-6 -top-6 h-40 w-40 text-[#C26A4A] opacity-[0.15]" aria-hidden="true" viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 110C35 90 55 70 70 45S90 15 110 10" />
                            <path d="M38 92c-2-14 6-26 18-30" />
                            <path d="M58 66c-2-12 6-22 16-25" />
                            <ellipse cx="54" cy="64" rx="3.5" ry="8" transform="rotate(-38 54 64)" />
                            <ellipse cx="74" cy="38" rx="3" ry="7" transform="rotate(-40 74 38)" />
                        </svg>
                        <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#5C4033] text-white">
                            <svg class="h-7 w-7" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                                <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                                <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                            </svg>
                        </span>
                        <p class="mt-5 text-lg font-bold text-[#3F2A22]">{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}</p>
                        <p class="mt-1 text-sm text-[#8A7464]">{{ $settings['site.tagline'] ?? 'Desa Salamnunggal' }}</p>
                        <div class="mt-5 flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-xs font-medium text-[#5C4033] border border-[#ECE5D9]">Promosi</span>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-xs font-medium text-[#5C4033] border border-[#ECE5D9]">Direktori</span>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-xs font-medium text-[#5C4033] border border-[#ECE5D9]">Verifikasi</span>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ============ APA ITU PORTAL ============ --}}
            <section class="mt-16 sm:mt-20">
                <div class="grid items-start gap-10 lg:grid-cols-12">
                    <div class="lg:col-span-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#C26A4A]">Apa itu Portal</p>
                        <h2 class="mt-2 text-2xl font-bold tracking-tight text-[#3F2A22] sm:text-3xl">Apa itu Portal UMKM Salamnunggal?</h2>
                        <p class="mt-4 text-base leading-relaxed text-[#6F5D50]">Portal ini adalah ruang digital yang mempertemukan masyarakat dengan pelaku UMKM Desa Salamnunggal. Semua informasi usaha dan produk ditampilkan dalam satu platform yang mudah diakses.</p>
                        <p class="mt-3 text-base leading-relaxed text-[#6F5D50]">Portal ini dibangun untuk menjadi jembatan informasi antara masyarakat dan pelaku usaha lokal, sekaligus mendukung promosi dan pertumbuhan ekonomi desa.</p>
                    </div>
                    <div class="lg:col-span-7">
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="rounded-2xl border border-[#ECE5D9] bg-white p-5 shadow-[0_2px_12px_rgba(63,42,34,0.06)]">
                                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#C26A4A]">
                                    <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                    </svg>
                                </span>
                                <p class="mt-3 text-sm font-semibold text-[#3F2A22]">Terhubung</p>
                                <p class="mt-1 text-sm leading-relaxed text-[#6F5D50]">Mempertemukan masyarakat dengan pelaku UMKM lokal.</p>
                            </div>
                            <div class="rounded-2xl border border-[#ECE5D9] bg-white p-5 shadow-[0_2px_12px_rgba(63,42,34,0.06)]">
                                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#C26A4A]">
                                    <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10" />
                                    </svg>
                                </span>
                                <p class="mt-3 text-sm font-semibold text-[#3F2A22]">Terpercaya</p>
                                <p class="mt-1 text-sm leading-relaxed text-[#6F5D50]">Informasi UMKM dan produk melalui proses verifikasi.</p>
                            </div>
                            <div class="rounded-2xl border border-[#ECE5D9] bg-white p-5 shadow-[0_2px_12px_rgba(63,42,34,0.06)]">
                                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#C26A4A]">
                                    <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 2v20M2 12h20" />
                                    </svg>
                                </span>
                                <p class="mt-3 text-sm font-semibold text-[#3F2A22]">Berkembang</p>
                                <p class="mt-1 text-sm leading-relaxed text-[#6F5D50]">Mendukung promosi dan pertumbuhan ekonomi lokal.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ============ NILAI UTAMA ============ --}}
            <section class="mt-16 sm:mt-20">
                <div class="text-center">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#C26A4A]">Nilai Utama</p>
                    <h2 class="mt-2 text-2xl font-bold tracking-tight text-[#3F2A22] sm:text-3xl">Menghubungkan, Menguatkan, Membangun Bersama</h2>
                </div>
                <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-2xl border border-[#ECE5D9] bg-white p-6 shadow-[0_2px_12px_rgba(63,42,34,0.06)]">
                        <span class="text-xs font-semibold text-[#C26A4A]">01</span>
                        <div class="mt-3 flex h-10 w-10 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]">
                            <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.3-4.3" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-[#3F2A22]">Menghubungkan</p>
                        <p class="mt-1 text-sm leading-relaxed text-[#6F5D50]">Mempermudah masyarakat menemukan UMKM dan produk lokal.</p>
                    </div>
                    <div class="rounded-2xl border border-[#ECE5D9] bg-white p-6 shadow-[0_2px_12px_rgba(63,42,34,0.06)]">
                        <span class="text-xs font-semibold text-[#C26A4A]">02</span>
                        <div class="mt-3 flex h-10 w-10 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]">
                            <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2v20M2 12h20" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-[#3F2A22]">Menguatkan</p>
                        <p class="mt-1 text-sm leading-relaxed text-[#6F5D50]">Memberikan ruang promosi bagi pelaku UMKM Salamnunggal.</p>
                    </div>
                    <div class="rounded-2xl border border-[#ECE5D9] bg-white p-6 shadow-[0_2px_12px_rgba(63,42,34,0.06)]">
                        <span class="text-xs font-semibold text-[#C26A4A]">03</span>
                        <div class="mt-3 flex h-10 w-10 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]">
                            <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-[#3F2A22]">Membangun Bersama</p>
                        <p class="mt-1 text-sm leading-relaxed text-[#6F5D50]">Mendorong pertumbuhan ekonomi dan kemandirian desa.</p>
                    </div>
                    <div class="rounded-2xl border border-[#ECE5D9] bg-white p-6 shadow-[0_2px_12px_rgba(63,42,34,0.06)]">
                        <span class="text-xs font-semibold text-[#C26A4A]">04</span>
                        <div class="mt-3 flex h-10 w-10 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]">
                            <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 12l2 2 4-4" />
                                <circle cx="12" cy="12" r="10" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-[#3F2A22]">Informasi Terpercaya</p>
                        <p class="mt-1 text-sm leading-relaxed text-[#6F5D50]">Menyajikan informasi UMKM dan produk yang telah melalui proses verifikasi.</p>
                    </div>
                </div>
            </section>

            {{-- ============ BAGAIMANA PORTAL BEKERJA ============ --}}
            <section class="mt-16 sm:mt-20">
                <div class="text-center">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#C26A4A]">Cara Kerja</p>
                    <h2 class="mt-2 text-2xl font-bold tracking-tight text-[#3F2A22] sm:text-3xl">Bagaimana Portal Ini Bekerja?</h2>
                </div>
                <div class="mt-10 relative">
                    <div class="hidden lg:block absolute inset-0 top-1/2 -translate-y-1/2">
                        <div class="h-0.5 w-full bg-[#E8D8C8]"></div>
                    </div>
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-5">
                        <div class="relative rounded-2xl border border-[#ECE5D9] bg-white p-5 shadow-[0_2px_12px_rgba(63,42,34,0.06)]">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#5C4033] text-sm font-bold text-white">01</span>
                                <p class="text-sm font-semibold text-[#3F2A22]">UMKM Mendaftar</p>
                            </div>
                            <p class="mt-2 text-xs leading-relaxed text-[#6F5D50]">Pemilik usaha mengisi formulir pendaftaran UMKM dan produk.</p>
                        </div>
                        <div class="relative rounded-2xl border border-[#ECE5D9] bg-white p-5 shadow-[0_2px_12px_rgba(63,42,34,0.06)]">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#5C4033] text-sm font-bold text-white">02</span>
                                <p class="text-sm font-semibold text-[#3F2A22]">Data Diverifikasi Admin</p>
                            </div>
                            <p class="mt-2 text-xs leading-relaxed text-[#6F5D50]">Admin memeriksa kelengkapan dan keabsahan data yang diajukan.</p>
                        </div>
                        <div class="relative rounded-2xl border border-[#ECE5D9] bg-white p-5 shadow-[0_2px_12px_rgba(63,42,34,0.06)]">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#5C4033] text-sm font-bold text-white">03</span>
                                <p class="text-sm font-semibold text-[#3F2A22]">UMKM & Produk Ditampilkan</p>
                            </div>
                            <p class="mt-2 text-xs leading-relaxed text-[#6F5D50]">Data yang terverifikasi ditampilkan di portal untuk umum.</p>
                        </div>
                        <div class="relative rounded-2xl border border-[#ECE5D9] bg-white p-5 shadow-[0_2px_12px_rgba(63,42,34,0.06)]">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#5C4033] text-sm font-bold text-white">04</span>
                                <p class="text-sm font-semibold text-[#3F2A22]">Masyarakat Menemukan</p>
                            </div>
                            <p class="mt-2 text-xs leading-relaxed text-[#6F5D50]">Pengunjung mencari, menjelajahi, dan mengenal UMKM lokal.</p>
                        </div>
                        <div class="relative rounded-2xl border border-[#ECE5D9] bg-white p-5 shadow-[0_2px_12px_rgba(63,42,34,0.06)]">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#5C4033] text-sm font-bold text-white">05</span>
                                <p class="text-sm font-semibold text-[#3F2A22]">Hubungi UMKM</p>
                            </div>
                            <p class="mt-2 text-xs leading-relaxed text-[#6F5D50]">Pengunjung dapat menghubungi pemilik usaha melalui kontak yang tersedia.</p>
                        </div>
                    </div>
                    {{-- Mobile connector line --}}
                    <div class="pointer-events-none absolute inset-x-6 top-8 hidden sm:block lg:hidden">
                        <div class="h-0.5 w-full bg-[#E8D8C8]"></div>
                    </div>
                </div>
            </section>

            {{-- ============ BUKAN MARKETPLACE ============ --}}
            <section class="mt-16 sm:mt-20">
                <div class="rounded-2xl border border-[#ECE5D9] bg-[#FAF6F5] px-6 py-8 sm:px-8 sm:py-10">
                    <div class="mx-auto max-w-3xl">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#C26A4A]">Tentang Portal</p>
                        <h2 class="mt-2 text-2xl font-bold tracking-tight text-[#3F2A22] sm:text-3xl">Portal Promosi & Direktori, Bukan Marketplace</h2>
                        <p class="mt-4 text-base leading-relaxed text-[#6F5D50]">Portal UMKM Salamnunggal berfungsi sebagai media promosi dan direktori UMKM lokal. Pengunjung dapat menemukan informasi usaha dan produk serta menghubungi pemilik usaha melalui kontak yang tersedia. Transaksi dilakukan secara langsung dengan pelaku usaha.</p>
                    </div>
                </div>
            </section>

            {{-- ============ CTA ============ --}}
            <section class="mt-16 sm:mt-20">
                <div class="relative overflow-hidden rounded-3xl bg-[#C26A4A] px-6 py-14 sm:px-12 sm:py-16">
                    <svg class="pointer-events-none absolute -left-8 -top-8 h-56 w-56 text-white opacity-[0.12]" aria-hidden="true" viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 110C35 90 55 70 70 45S90 15 110 10" />
                        <path d="M38 92c-2-14 6-26 18-30" />
                        <path d="M58 66c-2-12 6-22 16-25" />
                        <ellipse cx="54" cy="64" rx="3.5" ry="8" transform="rotate(-38 54 64)" />
                        <ellipse cx="74" cy="38" rx="3" ry="7" transform="rotate(-40 74 38)" />
                    </svg>
                    <svg class="pointer-events-none absolute -bottom-10 -right-10 h-64 w-64 text-white opacity-[0.12]" aria-hidden="true" viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 110C35 90 55 70 70 45S90 15 110 10" />
                        <path d="M58 66c-2-12 6-22 16-25" />
                        <ellipse cx="74" cy="38" rx="3" ry="7" transform="rotate(-40 74 38)" />
                    </svg>
                    <div class="relative mx-auto max-w-2xl text-center">
                        <h2 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">Temukan UMKM Lokal Salamnunggal</h2>
                        <p class="mx-auto mt-3 max-w-xl text-white/85">Jelajahi usaha dan produk lokal yang tersedia di Portal UMKM Salamnunggal.</p>
                        <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                            <a href="{{ route('public.umkm.index') }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-white px-7 text-sm font-semibold text-[#C26A4A] shadow-sm transition-colors duration-150 hover:bg-[#FAF6F5] focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#C26A4A]">
                                Jelajahi UMKM
                            </a>
                            <a href="{{ route('public.product.index') }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl border border-white/40 px-7 text-sm font-semibold text-white transition-colors duration-150 hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#C26A4A]">
                                Lihat Produk
                            </a>
                        </div>
                        <a href="{{ route('register') }}" class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-white/90 transition-colors duration-150 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#C26A4A]">
                            Daftarkan UMKM Anda
                            <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14" />
                                <path d="m12 5 7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>