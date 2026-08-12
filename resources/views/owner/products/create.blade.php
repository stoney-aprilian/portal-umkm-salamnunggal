<x-app-layout title="Tambah Produk">
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('Tambah Produk') }}
        </h1>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <ol class="flex flex-wrap items-center gap-x-3 gap-y-2 text-sm">
                <li class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-xs font-semibold text-emerald-700">1</span>
                    <span class="font-medium text-slate-700">Isi Data Produk</span>
                </li>
                <li aria-hidden="true" class="text-slate-300">→</li>
                <li class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-500">2</span>
                    <span class="text-slate-500">Kirim Pengajuan</span>
                </li>
                <li aria-hidden="true" class="text-slate-300">→</li>
                <li class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-500">3</span>
                    <span class="text-slate-500">Disetujui &amp; Tampil di Portal</span>
                </li>
            </ol>

            <div class="mt-4 card">
                <div class="p-6 sm:p-8">
                    <p class="text-sm text-slate-600">
                        Isi data produk Anda. Data akan disimpan sebagai draft, lalu Anda dapat mengirimkannya untuk diperiksa Administrator.
                    </p>
                    <p class="mt-2 text-xs text-slate-500">
                        Kolom bertanda <span class="font-semibold text-red-500">*</span> wajib diisi. Foto produk dapat ditambahkan setelah produk disimpan.
                    </p>

                    <form method="POST" action="{{ route('owner.products.store', $umkm) }}" class="mt-6">
                        @csrf

                        <section class="space-y-4">
                            <h2 class="font-semibold text-slate-900">Informasi Produk</h2>

                            <div>
                                <x-input-label for="name" :value="__('Nama Produk')" required />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <x-input-label for="category_id" :value="__('Kategori')" required />
                                    <x-select id="category_id" name="category_id" class="mt-1 block w-full" required>
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
                                    <x-input-label for="price" :value="__('Harga (Rp)')" required />
                                    <x-text-input id="price" class="block mt-1 w-full" type="text" name="price" :value="old('price')" placeholder="Contoh: 50000" required />
                                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                    <p class="mt-1 text-xs text-slate-500">Tulis angka tanpa titik atau koma.</p>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="description" :value="__('Deskripsi')" />
                                <x-textarea id="description" name="description" rows="4" class="mt-1 block w-full">{{ old('description') }}</x-textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </section>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                            <x-primary-button class="sm:w-auto">{{ __('Simpan Draft') }}</x-primary-button>
                            <a href="{{ route('owner.products.index', $umkm) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-white border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 transition duration-300 hover:bg-slate-50">
                                {{ __('Batal') }}
                            </a>
                            <p class="text-sm text-slate-500 sm:ms-auto">
                                Setelah disimpan, kirim pengajuan dari halaman produk.
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
