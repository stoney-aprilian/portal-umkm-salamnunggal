<x-app-layout title="Ajukan Perubahan Produk">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-3">
            <div class="min-w-0">
                <h1 class="text-2xl font-semibold tracking-tight text-[#3F2A22] sm:text-3xl">
                    {{ __('Ajukan Perubahan Produk') }}
                </h1>
                <p class="mt-1 text-sm text-[#8A7464] sm:text-base">
                    Perbarui data {{ $product->name }}. Perubahan akan diperiksa Administrator terlebih dahulu.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="container-page">
            @if (session('status'))
                <x-alert type="success" class="mb-6">{{ session('status') }}</x-alert>
            @endif

            @if (session('error'))
                <x-alert type="error" class="mb-6">{{ session('error') }}</x-alert>
            @endif

            <div class="mb-6 overflow-hidden rounded-2xl bg-[#FAF6F5] shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-[#ECE5D9]">
                <div class="p-6 sm:p-8">
                    <div class="flex items-start gap-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]">
                            <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                                <line x1="12" x2="12" y1="9" y2="13" />
                                <line x1="12" x2="12.01" y1="17" y2="17" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold text-[#3F2A22]">Perubahan ini akan diperiksa Administrator terlebih dahulu.</h3>
                            <p class="mt-1 text-sm text-[#6F5D50]">
                                Produk yang sedang tampil di publik <span class="font-semibold text-[#3F2A22]">tidak berubah</span> sampai Administrator menyetujui perubahan ini. Isi formulir sesuai data produk saat ini, lalu ubah bagian yang ingin diperbarui.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl bg-white shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-[#ECE5D9]">
                <div class="p-6 sm:p-8">
                    <form method="POST" action="{{ route('owner.products.revisions.store', $product) }}" class="space-y-8">
                        @csrf

                        <section class="space-y-5">
                            <div class="flex items-center gap-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#5C4033]">
                                    <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" />
                                        <path d="M14 2v6h6" />
                                        <path d="M16 13H8" />
                                        <path d="M16 17H8" />
                                        <path d="M10 9H8" />
                                    </svg>
                                </span>
                                <div>
                                    <h2 class="text-sm font-semibold uppercase tracking-wider text-[#3F2A22]">Informasi Produk</h2>
                                    <p class="text-xs text-[#8A7464]">Nama, kategori, harga, dan deskripsi</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <x-owner-input-label for="name" :value="__('Nama Produk')" required />
                                    <x-owner-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $product->name)" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-owner-input-label for="category_id" :value="__('Kategori')" required />
                                    <x-owner-select id="category_id" name="category_id" class="mt-1 block w-full" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </x-select>
                                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-owner-input-label for="price" :value="__('Harga')" required />
                                    <x-owner-text-input id="price" class="block mt-1 w-full" type="number" name="price" :value="old('price', $product->price)" step="0.01" min="0" required />
                                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-2">
                                    <x-owner-input-label for="description" :value="__('Deskripsi')" />
                                    <x-owner-textarea id="description" name="description" rows="4" class="mt-1 block w-full">{{ old('description', $product->description) }}</x-textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>
                            </div>
                        </section>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <x-owner-primary-button class="sm:w-auto">{{ __('Simpan Draft Perubahan') }}</x-primary-button>
                            <a href="{{ route('owner.products.index', $product->umkm) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#ECE5D9] bg-white px-5 text-sm font-semibold text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                {{ __('Batal') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
