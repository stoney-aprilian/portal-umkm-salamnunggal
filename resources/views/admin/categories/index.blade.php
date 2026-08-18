<x-app-layout title="Kelola Kategori">
    <div class="py-8 sm:py-10">
        <div class="container-page">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-[#8A7464] transition-colors duration-150 hover:text-[#5C4033] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                Kembali ke Dashboard
            </a>

            <div class="mt-6 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Kelola Kategori</h1>
                    <p class="mt-1 text-sm text-slate-600">Kelola kategori yang digunakan untuk mengelompokkan UMKM dan produk.</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <a href="{{ route('admin.categories.create', 'umkm') }}" class="inline-flex min-h-11 w-full shrink-0 items-center justify-center gap-2 rounded-xl bg-[#C26A4A] px-5 text-sm font-semibold text-white transition duration-300 hover:bg-[#A8563A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 sm:w-auto">
                        + Tambah Kategori UMKM
                    </a>
                    <a href="{{ route('admin.categories.create', 'product') }}" class="inline-flex min-h-11 w-full shrink-0 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 sm:w-auto">
                        + Tambah Kategori Produk
                    </a>
                </div>
            </div>

            @if (session('status'))
                <x-alert type="success" class="mt-6">{{ session('status') }}</x-alert>
            @endif
            @if (session('error'))
                <x-alert type="error" class="mt-6">{{ session('error') }}</x-alert>
            @endif

            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3">
                <div class="rounded-2xl bg-white p-4 shadow-sm sm:p-5">
                    <dl>
                        <dt class="text-xs font-medium text-slate-500">Total Kategori</dt>
                        <dd class="mt-1 text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">{{ $totalCount }}</dd>
                    </dl>
                </div>
                <div class="rounded-2xl bg-white p-4 shadow-sm sm:p-5">
                    <dl>
                        <dt class="text-xs font-medium text-slate-500">Kategori UMKM</dt>
                        <dd class="mt-1 text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">{{ $totalUmkm }}</dd>
                    </dl>
                </div>
                <div class="rounded-2xl bg-white p-4 shadow-sm sm:p-5">
                    <dl>
                        <dt class="text-xs font-medium text-slate-500">Kategori Produk</dt>
                        <dd class="mt-1 text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">{{ $totalProduct }}</dd>
                    </dl>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <form method="GET" action="{{ route('admin.categories.index') }}" class="flex-1 sm:max-w-sm">
                    <label for="search" class="sr-only">Cari kategori</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-4 w-4 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.35-4.35" />
                            </svg>
                        </div>
                        <input
                            type="search"
                            id="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari kategori..."
                            class="block w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2"
                        >
                    </div>
                </form>
                <div class="flex items-center gap-2">
                    @if ($search !== '')
                        <a href="{{ route('admin.categories.index') }}" class="inline-flex min-h-10 items-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            Reset
                        </a>
                    @endif
                </div>
            </div>

            <section class="mt-6" aria-label="Kategori UMKM">
                <h2 class="text-lg font-semibold tracking-tight text-slate-900">Kategori UMKM ({{ $umkmCategories->count() }})</h2>
                @if ($umkmCategories->isNotEmpty())
                    <div class="mt-4 overflow-hidden rounded-2xl bg-white shadow-sm">
                        <ul class="divide-y divide-slate-100">
                            @foreach ($umkmCategories as $category)
                                <li class="px-5 py-4 sm:px-6">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-slate-900">{{ $category->name }}</p>
                                            <p class="mt-0.5 text-xs text-slate-500">
                                                {{ $category->umkms_count }} UMKM &middot; {{ $category->products_count }} Produk
                                            </p>
                                        </div>
                                        <div class="flex shrink-0 flex-col items-start gap-2 sm:flex-row sm:items-center">
                                            <a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex min-h-10 w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-[#5C4033] transition duration-300 hover:border-[#C26A4A] hover:text-[#C26A4A] sm:w-auto">
                                                Kelola
                                            </a>
                                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Yakin ingin menghapus kategori {{ $category->name }}? Kategori yang masih digunakan tidak dapat dihapus.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-red-700 transition duration-300 hover:border-red-300 hover:bg-red-50 sm:w-auto">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="mt-4 flex flex-col items-center justify-center rounded-2xl bg-white px-5 py-8 text-center shadow-sm sm:px-6">
                        <p class="text-sm text-slate-600">Belum ada kategori UMKM.</p>
                    </div>
                @endif
            </section>

            <section class="mt-8" aria-label="Kategori Produk">
                <h2 class="text-lg font-semibold tracking-tight text-slate-900">Kategori Produk ({{ $productCategories->count() }})</h2>
                @if ($productCategories->isNotEmpty())
                    <div class="mt-4 overflow-hidden rounded-2xl bg-white shadow-sm">
                        <ul class="divide-y divide-slate-100">
                            @foreach ($productCategories as $category)
                                <li class="px-5 py-4 sm:px-6">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-slate-900">{{ $category->name }}</p>
                                            <p class="mt-0.5 text-xs text-slate-500">
                                                {{ $category->umkms_count }} UMKM &middot; {{ $category->products_count }} Produk
                                            </p>
                                        </div>
                                        <div class="flex shrink-0 flex-col items-start gap-2 sm:flex-row sm:items-center">
                                            <a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex min-h-10 w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-[#5C4033] transition duration-300 hover:border-[#C26A4A] hover:text-[#C26A4A] sm:w-auto">
                                                Kelola
                                            </a>
                                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Yakin ingin menghapus kategori {{ $category->name }}? Kategori yang masih digunakan tidak dapat dihapus.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-red-700 transition duration-300 hover:border-red-300 hover:bg-red-50 sm:w-auto">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="mt-4 flex flex-col items-center justify-center rounded-2xl bg-white px-5 py-8 text-center shadow-sm sm:px-6">
                        <p class="text-sm text-slate-600">Belum ada kategori produk.</p>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
