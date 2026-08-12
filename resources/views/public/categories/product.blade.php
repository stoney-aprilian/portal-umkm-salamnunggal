<x-app-layout :title="$category->name . ' — Produk'">
    <div class="py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="{{ route('public.product.index') }}" class="text-sm font-medium text-emerald-600 hover:underline">&larr; Kembali ke daftar Produk</a>

            <h1 class="mt-4 text-3xl font-semibold text-slate-900">Produk Kategori {{ $category->name }}</h1>

            @if ($category->description)
                <p class="mt-2 text-slate-600">{{ $category->description }}</p>
            @endif

            @if ($products->isNotEmpty())
                <p class="mt-2 text-sm text-slate-500">Menampilkan {{ $products->count() }} produk dalam kategori ini.</p>

                @php
                    $cols = match ($products->count()) {
                        1 => 'max-w-md mx-auto',
                        2 => 'sm:grid-cols-2',
                        default => 'sm:grid-cols-2 lg:grid-cols-3',
                    };
                @endphp
                <div class="mt-8 grid grid-cols-1 gap-6 {{ $cols }}">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            @else
                <div class="mt-8 rounded-2xl bg-white p-12 text-center shadow-sm">
                    <svg class="mx-auto h-12 w-12 text-slate-300" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m7.5 4.27 9 5.15" />
                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                        <path d="m3.3 7 8.7 5 8.7-5" />
                        <path d="M12 22V12" />
                    </svg>
                    <h2 class="mt-4 text-xl font-semibold text-slate-900">Belum ada produk dalam kategori ini.</h2>
                    <p class="mt-2 text-sm text-slate-600">Produk dalam kategori ini belum tersedia untuk umum. Silakan kembali lagi nanti.</p>
                    <div class="mt-6 flex flex-col items-center justify-center gap-3 sm:flex-row">
                        <a href="{{ route('public.product.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition duration-300 hover:bg-emerald-500">
                            Lihat Semua Produk
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
