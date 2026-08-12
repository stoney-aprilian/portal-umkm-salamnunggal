<x-app-layout title="Katalog UMKM">
    <div class="py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="border-b border-slate-200 pb-6">
                <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-3">
                    <div class="min-w-0">
                        <h1 class="text-3xl font-semibold tracking-tight text-slate-900">UMKM</h1>
                        <p class="mt-2 max-w-2xl text-slate-600">
                            Jelajahi usaha mikro, kecil, dan menengah di Desa Salamnunggal untuk menemukan produk lokal, profil usaha, dan cara menghubungi pemiliknya.
                        </p>
                    </div>
                    @if ($umkms->isNotEmpty())
                        <p class="text-sm text-slate-500">Menampilkan {{ $umkms->count() }} UMKM terverifikasi.</p>
                    @endif
                </div>
            </div>

            @if ($umkms->isNotEmpty())
                @php
                    $cols = match ($umkms->count()) {
                        1 => 'max-w-md mx-auto',
                        2 => 'sm:grid-cols-2',
                        default => 'sm:grid-cols-2 lg:grid-cols-3',
                    };
                @endphp
                <div class="mt-6 grid grid-cols-1 gap-6 {{ $cols }}">
                    @foreach ($umkms as $umkm)
                        <x-umkm-card :umkm="$umkm" />
                    @endforeach
                </div>
            @else
                <div class="mt-6 rounded-2xl bg-white px-5 py-8 text-center shadow-sm sm:py-10">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">
                        <svg class="h-6 w-6 text-slate-500" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                            <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                            <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                            <path d="M2 7h20" />
                        </svg>
                    </div>
                    <h2 class="mt-4 text-lg font-semibold tracking-tight text-slate-900">Belum ada UMKM yang terdaftar.</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-slate-600">UMKM yang telah terverifikasi akan tampil di sini. Silakan kembali lagi nanti.</p>
                    <a href="{{ url('/') }}" class="mt-6 inline-flex min-h-11 items-center justify-center rounded-xl bg-emerald-600 px-5 text-sm font-semibold text-white transition duration-300 hover:bg-emerald-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                        Kembali ke Beranda
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
