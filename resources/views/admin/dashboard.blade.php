<x-app-layout title="Dashboard Administrator">
    <div class="py-12 sm:py-16">
        <div class="container-page">
            @if (session('status'))
                <x-alert type="success" class="mb-6">{{ session('status') }}</x-alert>
            @endif

            @if (session('error'))
                <x-alert type="error" class="mb-6">{{ session('error') }}</x-alert>
            @endif

            <h1 class="text-3xl font-semibold tracking-tight text-slate-900">Dashboard Administrator</h1>
            <p class="mt-2 text-slate-600">Selamat datang, {{ Auth::user()->name }}.</p>
            <p class="mt-1 text-sm text-slate-500">
                Anda memiliki {{ $totalCount }} pengajuan yang menunggu pemeriksaan.
            </p>

            <!-- Perlu Perhatian -->
            <section class="mt-10">
                <h2 class="text-xl font-semibold tracking-tight text-slate-900">Perlu Perhatian</h2>

                @if ($totalCount > 0)
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-white p-5 shadow-sm sm:p-6">
                            <p class="text-sm font-medium text-slate-500">UMKM menunggu verifikasi</p>
                            <p class="mt-1.5 text-3xl font-semibold tracking-tight text-slate-900">{{ $umkmCount }}</p>
                            <p class="mt-1 text-sm text-slate-500">pengajuan UMKM menunggu pemeriksaan Anda.</p>
                            <a href="{{ route('admin.umkm.verification.index') }}" class="mt-5 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 text-sm font-semibold text-white transition duration-300 hover:bg-emerald-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 sm:w-auto">
                                Periksa UMKM
                            </a>
                        </div>

                        <div class="rounded-2xl bg-white p-5 shadow-sm sm:p-6">
                            <p class="text-sm font-medium text-slate-500">Produk menunggu verifikasi</p>
                            <p class="mt-1.5 text-3xl font-semibold tracking-tight text-slate-900">{{ $productCount }}</p>
                            <p class="mt-1 text-sm text-slate-500">pengajuan produk menunggu pemeriksaan Anda.</p>
                            <a href="{{ route('admin.products.verification.index') }}" class="mt-5 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 text-sm font-semibold text-white transition duration-300 hover:bg-emerald-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 sm:w-auto">
                                Periksa Produk
                            </a>
                        </div>
                    </div>
                @else
                    <div class="mt-4 rounded-2xl bg-white px-5 py-6 shadow-sm sm:px-6">
                        <p class="text-sm leading-relaxed text-slate-600">Semua pengajuan sudah ditangani.</p>
                    </div>
                @endif
            </section>

            <!-- Aksi Cepat -->
            <section class="mt-10">
                <h2 class="text-xl font-semibold tracking-tight text-slate-900">Aksi Cepat</h2>
                <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('admin.umkm.verification.index') }}" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 text-sm font-semibold text-white transition duration-300 hover:bg-emerald-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 sm:w-auto">
                        Verifikasi UMKM
                    </a>
                    <a href="{{ route('admin.products.verification.index') }}" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-6 text-sm font-semibold text-emerald-700 transition duration-300 hover:border-emerald-300 hover:bg-emerald-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 sm:w-auto">
                        Verifikasi Produk
                    </a>
                </div>
            </section>

            <!-- Pengajuan Terbaru -->
            <section class="mt-10">
                <h2 class="text-xl font-semibold tracking-tight text-slate-900">Pengajuan Terbaru</h2>

                @if ($recent->isEmpty())
                    <p class="mt-4 max-w-2xl rounded-2xl bg-white px-5 py-6 text-sm leading-relaxed text-slate-600 shadow-sm sm:px-6">
                        Belum ada pengajuan terbaru.
                    </p>
                @else
                    <ul class="mt-4 overflow-hidden rounded-2xl bg-white shadow-sm">
                        @foreach ($recent as $item)
                            <li class="min-w-0 border-b border-slate-100 px-5 py-5 last:border-b-0 sm:px-6">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                                {{ $item['label'] }}
                                            </span>
                                            <p class="break-words text-base font-medium text-slate-900">{{ $item['name'] }}</p>
                                        </div>
                                        <p class="mt-1.5 break-words text-sm leading-relaxed text-slate-500">
                                            @if ($item['umkmName'] !== null)
                                                UMKM: {{ $item['umkmName'] }}
                                                &middot; Pemilik: {{ $item['ownerName'] }}
                                            @else
                                                Pemilik: {{ $item['ownerName'] }}
                                            @endif
                                            &middot; Diajukan: {{ $item['submittedAt'] }}
                                        </p>
                                    </div>
                                    <a href="{{ $item['reviewUrl'] }}" class="inline-flex min-h-11 w-full shrink-0 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-emerald-700 transition duration-300 hover:border-emerald-300 hover:bg-emerald-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 sm:w-auto">
                                        Periksa
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <!-- Ringkasan Pengajuan -->
            <section class="mt-10">
                <h2 class="text-xl font-semibold tracking-tight text-slate-900">Ringkasan Pengajuan</h2>
                <div class="mt-4 rounded-2xl bg-white p-5 shadow-sm sm:p-6">
                    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Pengajuan UMKM</dt>
                            <dd class="mt-0.5 text-2xl font-semibold tracking-tight text-slate-900">{{ $umkmCount }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Pengajuan Produk</dt>
                            <dd class="mt-0.5 text-2xl font-semibold tracking-tight text-slate-900">{{ $productCount }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Total</dt>
                            <dd class="mt-0.5 text-2xl font-semibold tracking-tight text-slate-900">{{ $totalCount }}</dd>
                        </div>
                    </dl>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>