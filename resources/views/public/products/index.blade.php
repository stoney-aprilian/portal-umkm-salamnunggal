<x-app-layout title="Katalog Produk">
    <div class="py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="border-b border-slate-200 pb-6">
                <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-3">
                    <div class="min-w-0">
                        <h1 class="text-3xl font-semibold tracking-tight text-slate-900">Produk</h1>
                        <p class="mt-2 max-w-2xl text-slate-600">Temukan produk unggulan dari UMKM di Desa Salamnunggal.</p>
                    </div>
                    @if ($products->isNotEmpty())
                        <p class="text-sm text-slate-500">Menampilkan {{ $products->count() }} produk terverifikasi.</p>
                    @endif
                </div>
            </div>

            @if ($products->isNotEmpty())
                @php
                    $cols = match ($products->count()) {
                        1 => 'max-w-md mx-auto',
                        2 => 'sm:grid-cols-2',
                        default => 'sm:grid-cols-2 lg:grid-cols-3',
                    };
                @endphp
                <div class="mt-6 grid grid-cols-1 gap-6 {{ $cols }}">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" refined />
                    @endforeach
                </div>
            @else
                <div class="mt-6 rounded-2xl bg-white p-12 text-center shadow-sm">
                    <svg class="mx-auto h-12 w-12 text-slate-300" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m7.5 4.27 9 5.15" />
                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                        <path d="m3.3 7 8.7 5 8.7-5" />
                        <path d="M12 22V12" />
                    </svg>
                    <h2 class="mt-4 text-xl font-semibold text-slate-900">Belum ada produk yang terdaftar.</h2>
                    <p class="mt-2 text-sm text-slate-600">Produk yang telah terverifikasi akan tampil di sini. Silakan kembali lagi nanti.</p>
                    <a href="{{ route('public.umkm.index') }}" class="mt-6 inline-flex min-h-11 items-center justify-center rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition duration-300 hover:bg-emerald-500">
                        Lihat UMKM
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
