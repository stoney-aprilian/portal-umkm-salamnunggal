<x-app-layout title="Perbaiki Produk">
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('Perbaiki Produk') }}
        </h1>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <x-alert type="success" class="mb-4">{{ session('status') }}</x-alert>
            @endif

            @if (session('error'))
                <x-alert type="error" class="mb-4">{{ session('error') }}</x-alert>
            @endif

            <div class="card">
                <div class="p-6">
                    @php
                        $latestNote = $product->verificationRequests()
                            ->whereIn('status', ['needs_revision', 'rejected'])
                            ->latest('id')
                            ->value('notes');
                    @endphp

                    @if ($product->status === 'rejected')
                        <x-alert type="error" class="mb-6">
                            <p class="font-semibold text-red-800">Pengajuan produk ini ditolak.</p>
                            <p class="mt-1 text-sm text-red-700">
                                Alasan Penolakan: {{ $latestNote ?? '—' }}
                            </p>
                        </x-alert>
                    @elseif ($product->status === 'needs_revision')
                        <x-alert type="warning" class="mb-6">
                            <p class="font-semibold text-amber-800">Pengajuan produk ini perlu diperbaiki.</p>
                            <p class="mt-1 text-sm text-amber-700">
                                Catatan Administrator: {{ $latestNote ?? '—' }}
                            </p>
                        </x-alert>
                    @endif

                    @if (in_array($product->status, ['needs_revision', 'rejected'], true))
                        <p class="text-sm text-slate-600">
                            Perbaiki data produk sesuai catatan Administrator, lalu simpan. Produk akan kembali menjadi draft dan dapat dikirim ulang untuk diperiksa.
                        </p>
                        <p class="mt-2 text-sm font-medium text-emerald-700">
                            Langkah berikutnya: setelah menyimpan, kirim pengajuan kembali dari halaman produk agar diperiksa ulang.
                        </p>
                    @else
                        <p class="text-sm text-slate-600">
                            Perbarui data produk, lalu simpan. Produk akan tetap berstatus draft sampai Anda mengirim pengajuannya.
                        </p>
                        <p class="mt-2 text-sm font-medium text-emerald-700">
                            Langkah berikutnya: kirim pengajuan dari halaman produk setelah data selesai diperbarui.
                        </p>
                    @endif

                    <form method="POST" action="{{ route('owner.products.update', $product) }}" class="mt-6">
                        @csrf
                        @method('PUT')

                        <section class="space-y-4">
                            <h2 class="font-semibold text-slate-900">Informasi Produk</h2>

                            <div>
                                <x-input-label for="name" :value="__('Nama Produk')" required />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $product->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <x-input-label for="category_id" :value="__('Kategori')" required />
                                    <x-select id="category_id" name="category_id" class="mt-1 block w-full" required>
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
                                    <x-input-label for="price" :value="__('Harga (Rp)')" required />
                                    <x-text-input id="price" class="block mt-1 w-full" type="text" name="price" :value="old('price', (string) (float) $product->price)" placeholder="Contoh: 50000" required />
                                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                    <p class="mt-1 text-xs text-slate-500">Tulis angka tanpa titik atau koma.</p>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="description" :value="__('Deskripsi')" />
                                <x-textarea id="description" name="description" rows="4" class="mt-1 block w-full">{{ old('description', $product->description) }}</x-textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </section>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                            <x-primary-button class="sm:w-auto">{{ __('Simpan Perubahan') }}</x-primary-button>
                            <a href="{{ route('owner.products.index', $product->umkm) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-white border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 transition duration-300 hover:bg-slate-50">
                                {{ __('Batal') }}
                            </a>
                        </div>
                    </form>

                    <section class="mt-10">
                        <h2 class="font-semibold text-slate-900">Media Produk</h2>
                        <p class="mt-1 text-sm text-slate-600">
                            Foto produk akan tampil di halaman publik. Format JPG, PNG, atau WEBP, maksimal 2 MB. Foto bersifat opsional.
                        </p>

                        @php
                            $photo = $product->media->first();
                        @endphp

                        <div class="mt-4">
                            <x-media-upload
                                title="Foto Produk"
                                description="Gambar utama produk Anda. Unggah ulang untuk mengganti foto lama."
                                :current="$photo"
                                :store-url="route('owner.products.media.store', [$product, 'product'])"
                                input-name="file_product"
                                :delete-url="$photo ? route('owner.media.destroy', $photo) : null"
                                item-label="foto produk"
                                optional
                                preview-class="aspect-[4/3] w-full max-w-sm"
                            />
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
