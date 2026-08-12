<x-app-layout :title="$category->name . ' — UMKM'">
    <div class="py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="{{ route('public.umkm.index') }}" class="text-sm font-medium text-emerald-600 hover:underline">&larr; Kembali ke daftar UMKM</a>

            <h1 class="mt-4 text-3xl font-semibold text-slate-900">UMKM Kategori {{ $category->name }}</h1>

            @if ($category->description)
                <p class="mt-2 text-slate-600">{{ $category->description }}</p>
            @endif

            @if ($umkms->isNotEmpty())
                <p class="mt-2 text-sm text-slate-500">Menampilkan {{ $umkms->count() }} UMKM dalam kategori ini.</p>

                @php
                    $cols = match ($umkms->count()) {
                        1 => 'max-w-md mx-auto',
                        2 => 'sm:grid-cols-2',
                        default => 'sm:grid-cols-2 lg:grid-cols-3',
                    };
                @endphp
                <div class="mt-8 grid grid-cols-1 gap-6 {{ $cols }}">
                    @foreach ($umkms as $umkm)
                        <x-umkm-card :umkm="$umkm" />
                    @endforeach
                </div>
            @else
                <div class="mt-8 rounded-2xl bg-white p-12 text-center shadow-sm">
                    <svg class="mx-auto h-12 w-12 text-slate-300" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                        <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                        <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                        <path d="M2 7h20" />
                    </svg>
                    <h2 class="mt-4 text-xl font-semibold text-slate-900">Belum ada UMKM dalam kategori ini.</h2>
                    <p class="mt-2 text-sm text-slate-600">UMKM dalam kategori ini belum tersedia untuk umum. Silakan kembali lagi nanti.</p>
                    <div class="mt-6 flex flex-col items-center justify-center gap-3 sm:flex-row">
                        <a href="{{ route('public.umkm.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition duration-300 hover:bg-emerald-500">
                            Lihat Semua UMKM
                        </a>
                        <a href="{{ url('/') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50">
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
