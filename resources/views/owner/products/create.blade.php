<x-app-layout title="Tambah Produk">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-3">
            <div class="min-w-0">
                <h1 class="text-2xl font-semibold tracking-tight text-[#3F2A22] sm:text-3xl">
                    {{ __('Tambah Produk') }}
                </h1>
                <p class="mt-1 text-sm text-[#8A7464] sm:text-base">
                    Tambah produk baru untuk {{ $umkm->name }}.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="container-page">
            <ol class="flex flex-wrap items-center gap-x-3 gap-y-2 text-sm">
                <li class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#5C4033] text-xs font-bold text-white">1</span>
                    <span class="font-medium text-[#3F2A22]">Isi Data Produk</span>
                </li>
                <li aria-hidden="true" class="text-[#8A7464]/40">→</li>
                <li class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#F4EDE1] text-xs font-bold text-[#5C4033]">2</span>
                    <span class="text-[#8A7464]">Kirim Pengajuan</span>
                </li>
                <li aria-hidden="true" class="text-[#8A7464]/40">→</li>
                <li class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#F4EDE1] text-xs font-bold text-[#5C4033]">3</span>
                    <span class="text-[#8A7464]">Disetujui &amp; Tampil di Portal</span>
                </li>
            </ol>

            <div class="mt-6 overflow-hidden rounded-2xl bg-white shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-[#ECE5D9]">
                <div class="p-6 sm:p-8">
                    <div class="rounded-xl bg-[#FAF6F5] p-4 ring-1 ring-[#ECE5D9]">
                        <p class="text-sm text-[#6F5D50]">
                            Isi data produk Anda. Data akan disimpan sebagai <span class="font-semibold text-[#3F2A22]">draft</span>, lalu Anda dapat mengirimkannya untuk diperiksa Administrator.
                        </p>
                        <p class="mt-1 text-xs text-[#8A7464]">
                            Kolom bertanda <span class="font-semibold text-[#C26A4A]">*</span> wajib diisi. Foto produk dapat ditambahkan setelah produk disimpan.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('owner.products.store', $umkm) }}" class="mt-6">
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
                                    <x-owner-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus placeholder="Contoh: Kopi Robusta" />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-owner-input-label for="category_id" :value="__('Kategori')" required />
                                    <x-owner-select id="category_id" name="category_id" class="mt-1 block w-full" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </x-select>
                                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-owner-input-label for="price" :value="__('Harga (Rp)')" required />
                                    <x-owner-text-input id="price" class="block mt-1 w-full" type="text" name="price" :value="old('price')" placeholder="Contoh: 50000" required />
                                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                    <p class="mt-1 text-xs text-[#8A7464]">Tulis angka tanpa titik atau koma.</p>
                                </div>

                                <div class="sm:col-span-2">
                                    <x-owner-input-label for="description" :value="__('Deskripsi')" />
                                    <x-owner-textarea id="description" name="description" rows="4" class="mt-1 block w-full" placeholder="Jelaskan produk Anda secara singkat">{{ old('description') }}</x-textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>
                            </div>
                        </section>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                            <x-owner-primary-button class="sm:w-auto">{{ __('Simpan Draft') }}</x-primary-button>
                            <a href="{{ route('owner.products.index', $umkm) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#ECE5D9] bg-white px-5 text-sm font-semibold text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                {{ __('Batal') }}
                            </a>
                            <p class="text-sm text-[#8A7464] sm:ms-auto">
                                Setelah disimpan, kirim pengajuan dari halaman produk.
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
