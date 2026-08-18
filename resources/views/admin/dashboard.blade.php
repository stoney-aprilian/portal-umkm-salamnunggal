<x-app-layout title="Dashboard Administrator">
    <div class="py-8 sm:py-10">
        <div class="container-page">
            @if (session('status'))
                <x-alert type="success" class="mb-6">{{ session('status') }}</x-alert>
            @endif
            @if (session('error'))
                <x-alert type="error" class="mb-6">{{ session('error') }}</x-alert>
            @endif

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Dashboard</h1>
                <p class="mt-1 text-sm text-slate-600">Selamat datang kembali, {{ Auth::user()->name }}.</p>
                <p class="mt-0.5 text-sm text-slate-500">Pantau pengajuan dan data Portal UMKM Salamnunggal dari satu tempat.</p>
            </div>

            <!-- KPI Overview -->
            <section class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5" aria-label="Ringkasan KPI">
                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <dl>
                        <dt class="text-xs font-medium text-slate-500">Total UMKM</dt>
                        <dd class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">{{ $totalUmkm }}</dd>
                    </dl>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <dl>
                        <dt class="text-xs font-medium text-slate-500">Total Produk</dt>
                        <dd class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">{{ $totalProduct }}</dd>
                    </dl>
                </div>
                <div class="rounded-2xl bg-[#C26A4A] p-5 shadow-sm">
                    <dl>
                        <dt class="text-xs font-medium text-white/80">Menunggu Verifikasi</dt>
                        <dd class="mt-1 text-2xl font-semibold tracking-tight text-white">{{ $totalCount }}</dd>
                    </dl>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <dl>
                        <dt class="text-xs font-medium text-slate-500">Total Pengguna/Owner</dt>
                        <dd class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">{{ $totalUser }}</dd>
                    </dl>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <dl>
                        <dt class="text-xs font-medium text-slate-500">Terverifikasi</dt>
                        <dd class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">{{ $verifiedCount }}</dd>
                    </dl>
                </div>
            </section>

            <!-- Perlu Perhatian + Quick Actions -->
            <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Perlu Perhatian -->
                <div class="lg:col-span-2">
                    <h2 class="text-lg font-semibold tracking-tight text-slate-900">Perlu Perhatian</h2>
                    @if ($totalCount > 0)
                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div class="rounded-2xl bg-white p-5 shadow-sm">
                                <dl>
                                    <dt class="text-sm font-medium text-slate-500">Owner menunggu verifikasi</dt>
                                    <dd class="mt-1.5 text-3xl font-semibold tracking-tight text-slate-900">{{ $ownerCount }}</dd>
                                    <p class="mt-1 text-sm text-slate-500">akun owner Self-Service menunggu pemeriksaan Anda.</p>
                                </dl>
                                <a href="{{ route('admin.owner-verification.index') }}" class="mt-5 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-[#C26A4A] px-5 text-sm font-semibold text-white transition duration-300 hover:bg-[#A8563A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                    Periksa Owner
                                </a>
                            </div>

                            <div class="rounded-2xl bg-white p-5 shadow-sm">
                                <dl>
                                    <dt class="text-sm font-medium text-slate-500">UMKM menunggu verifikasi</dt>
                                    <dd class="mt-1.5 text-3xl font-semibold tracking-tight text-slate-900">{{ $umkmCount }}</dd>
                                    <p class="mt-1 text-sm text-slate-500">pengajuan UMKM menunggu pemeriksaan Anda.</p>
                                </dl>
                                <a href="{{ route('admin.umkm.verification.index') }}" class="mt-5 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-[#C26A4A] px-5 text-sm font-semibold text-white transition duration-300 hover:bg-[#A8563A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                    Periksa UMKM
                                </a>
                            </div>

                            <div class="rounded-2xl bg-white p-5 shadow-sm">
                                <dl>
                                    <dt class="text-sm font-medium text-slate-500">Produk menunggu verifikasi</dt>
                                    <dd class="mt-1.5 text-3xl font-semibold tracking-tight text-slate-900">{{ $productCount }}</dd>
                                    <p class="mt-1 text-sm text-slate-500">pengajuan produk menunggu pemeriksaan Anda.</p>
                                </dl>
                                <a href="{{ route('admin.products.verification.index') }}" class="mt-5 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-[#C26A4A] px-5 text-sm font-semibold text-white transition duration-300 hover:bg-[#A8563A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                    Periksa Produk
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="mt-4 flex items-start gap-3 rounded-2xl bg-white px-5 py-6 shadow-sm">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600" aria-hidden="true">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6 9 17l-5-5" />
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Semua pengajuan sudah ditangani.</p>
                                <p class="mt-0.5 text-sm text-slate-600">Tidak ada pengajuan yang membutuhkan tindakan saat ini.</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="lg:col-span-1">
                    <h2 class="text-lg font-semibold tracking-tight text-slate-900">Aksi Cepat</h2>
                    <div class="mt-4 grid grid-cols-1 gap-3">
                        <a href="{{ route('admin.umkm.verification.index') }}" class="flex items-center gap-4 rounded-2xl bg-white p-4 shadow-sm transition-shadow duration-300 hover:shadow-md">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                                    <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                                    <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-900">Verifikasi UMKM</p>
                                <p class="text-xs text-slate-500">Periksa UMKM baru</p>
                            </div>
                            <svg class="h-4 w-4 shrink-0 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </a>

                        <a href="{{ route('admin.products.verification.index') }}" class="flex items-center gap-4 rounded-2xl bg-white p-4 shadow-sm transition-shadow duration-300 hover:shadow-md">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m7.5 4.27 9 5.15" />
                                    <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                                    <path d="m3.3 7 8.7 5 8.7-5" />
                                    <path d="M12 22V12" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-900">Verifikasi Produk</p>
                                <p class="text-xs text-slate-500">Periksa produk baru</p>
                            </div>
                            <svg class="h-4 w-4 shrink-0 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </a>

                        <a href="{{ route('admin.owner-verification.index') }}" class="flex items-center gap-4 rounded-2xl bg-white p-4 shadow-sm transition-shadow duration-300 hover:shadow-md">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-900">Verifikasi Owner</p>
                                <p class="text-xs text-slate-500">Periksa owner baru</p>
                            </div>
                            <svg class="h-4 w-4 shrink-0 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </a>

                        <a href="{{ route('admin.umkms.index') }}" class="flex items-center gap-4 rounded-2xl bg-white p-4 shadow-sm transition-shadow duration-300 hover:shadow-md">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="7" height="7" x="3" y="3" rx="1" />
                                    <rect width="7" height="7" x="14" y="3" rx="1" />
                                    <rect width="7" height="7" x="14" y="14" rx="1" />
                                    <rect width="7" height="7" x="3" y="14" rx="1" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-900">Kelola Data</p>
                                <p class="text-xs text-slate-500">Akses data UMKM, produk, pengguna, dan kategori</p>
                            </div>
                            <svg class="h-4 w-4 shrink-0 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pengajuan Terbaru + Ringkasan Data -->
            <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Pengajuan Terbaru -->
                <div class="lg:col-span-2">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold tracking-tight text-slate-900">Pengajuan Terbaru</h2>
                        <span class="text-sm font-medium text-[#C26A4A]">Lihat semua pengajuan →</span>
                    </div>
                    @if ($recent->isEmpty())
                        <div class="mt-4 rounded-2xl bg-white px-5 py-6 shadow-sm">
                            <p class="text-sm text-slate-600">Belum ada pengajuan terbaru.</p>
                        </div>
                    @else
                        <div class="mt-4 overflow-hidden rounded-2xl bg-white shadow-sm">
                            <ul class="divide-y divide-slate-100">
                                @foreach ($recent as $item)
                                    <li class="min-w-0">
                                        <div class="flex items-center gap-4 px-5 py-4 sm:px-6">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">
                                                        {{ $item['label'] }}
                                                    </span>
                                                    <p class="truncate text-sm font-medium text-slate-900">{{ $item['name'] }}</p>
                                                </div>
                                                <p class="mt-1 text-xs text-slate-500">
                                                    @if ($item['umkmName'] !== null)
                                                        UMKM: {{ $item['umkmName'] }}
                                                        &middot; Pemilik: {{ $item['ownerName'] }}
                                                    @else
                                                        Pemilik: {{ $item['ownerName'] }}
                                                    @endif
                                                    &middot; Diajukan: {{ $item['submittedAt'] }}
                                                </p>
                                            </div>
                                            <a href="{{ $item['reviewUrl'] }}" class="inline-flex min-h-10 shrink-0 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-[#5C4033] transition duration-300 hover:border-[#C26A4A] hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                                Periksa
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <!-- Ringkasan Data -->
                <div class="lg:col-span-1">
                    <h2 class="text-lg font-semibold tracking-tight text-slate-900">Ringkasan Data</h2>
                    <div class="mt-4 rounded-2xl bg-white p-5 shadow-sm">
                        <dl class="space-y-4">
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-slate-500">UMKM</dt>
                                <dd class="text-sm font-semibold text-slate-900">{{ $totalUmkm }}</dd>
                            </div>
                            <div class="h-px bg-slate-100" aria-hidden="true"></div>
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-slate-500">Produk</dt>
                                <dd class="text-sm font-semibold text-slate-900">{{ $totalProduct }}</dd>
                            </div>
                            <div class="h-px bg-slate-100" aria-hidden="true"></div>
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-slate-500">Owner</dt>
                                <dd class="text-sm font-semibold text-slate-900">{{ $totalUser }}</dd>
                            </div>
                            <div class="h-px bg-slate-100" aria-hidden="true"></div>
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-slate-500">Kategori</dt>
                                <dd class="text-sm font-semibold text-slate-900">{{ $totalCategory }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Aktivitas -->
            <section class="mt-8">
                <h2 class="text-lg font-semibold tracking-tight text-slate-900">Aktivitas</h2>
                <div class="mt-4 rounded-2xl bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400" aria-hidden="true">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 3v18h18" />
                                <path d="M7 16l4-4 4 4 6-6" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-medium text-slate-900">Grafik aktivitas segera hadir</p>
                        <p class="mt-1 text-sm text-slate-500">Data aktivitas dan pengajuan akan ditampilkan dalam bentuk grafik di sini.</p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
