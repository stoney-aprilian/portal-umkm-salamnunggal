<x-app-layout title="{{ $product->status === 'draft' ? __('Ubah Produk') : __('Perbaiki Produk') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-3">
            <div class="min-w-0">
                <h1 class="text-2xl font-semibold tracking-tight text-[#3F2A22] sm:text-3xl">
                    {{ $product->status === 'draft' ? __('Ubah Produk') : __('Perbaiki Produk') }}
                </h1>
                <p class="mt-1 text-sm text-[#8A7464] sm:text-base">
                    {{ $product->name }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <x-badge :status="$product->status" />
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

            @php
                $latestNote = $product->verificationRequests()
                    ->whereIn('status', ['needs_revision', 'rejected'])
                    ->latest('id')
                    ->value('notes');
            @endphp

            @if ($product->status === 'rejected')
                <div class="mb-6 overflow-hidden rounded-2xl bg-red-50 shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-red-200">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-start gap-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-700">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="m15 9-6 6" />
                                    <path d="m9 9 6 6" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <h3 class="text-sm font-semibold text-red-900">Pengajuan produk ini ditolak.</h3>
                                <p class="mt-1 text-sm text-red-800">
                                    <span class="font-semibold">Alasan Penolakan:</span> {{ $latestNote ?? '—' }}
                                </p>
                                <p class="mt-1 text-sm text-red-700">Perbaiki sesuai catatan, lalu kirim kembali.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($product->status === 'needs_revision')
                <div class="mb-6 overflow-hidden rounded-2xl bg-amber-50 shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-amber-200">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-start gap-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                                    <line x1="12" x2="12" y1="9" y2="13" />
                                    <line x1="12" x2="12.01" y1="17" y2="17" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <h3 class="text-sm font-semibold text-amber-900">Pengajuan produk ini perlu diperbaiki.</h3>
                                <p class="mt-1 text-sm text-amber-800">
                                    <span class="font-semibold">Catatan Administrator:</span> {{ $latestNote ?? '—' }}
                                </p>
                                <p class="mt-1 text-sm text-amber-700">Perbaiki sesuai catatan, lalu kirim kembali.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mb-6 overflow-hidden rounded-2xl bg-white shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-[#ECE5D9]">
                <div class="p-6 sm:p-8">
                    @if (in_array($product->status, ['needs_revision', 'rejected'], true))
                        <div class="flex items-start gap-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                                    <line x1="12" x2="12" y1="9" y2="13" />
                                    <line x1="12" x2="12.01" y1="17" y2="17" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <h3 class="text-sm font-semibold text-[#3F2A22]">Perbaiki data sesuai catatan Administrator</h3>
                                <p class="mt-1 text-sm text-[#6F5D50]">Perbaiki data produk sesuai catatan Administrator, lalu simpan. Produk akan kembali menjadi draft dan dapat dikirim ulang untuk diperiksa.</p>
                                <p class="mt-1 text-sm font-medium text-[#C26A4A]">Langkah berikutnya: setelah menyimpan, kirim pengajuan kembali agar diperiksa ulang.</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-start gap-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                                    <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <h3 class="text-sm font-semibold text-[#3F2A22]">Produk masih berupa draft</h3>
                                <p class="mt-1 text-sm text-[#6F5D50]">Perbarui data produk, lalu simpan. Produk akan tetap berstatus draft sampai Anda mengirim pengajuannya.</p>
                                <p class="mt-1 text-sm font-medium text-[#C26A4A]">Langkah berikutnya: kirim pengajuan dari halaman produk setelah data selesai diperbarui.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl bg-white shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-[#ECE5D9]">
                <div class="p-6 sm:p-8">
                    <form method="POST" action="{{ route('owner.products.update', $product) }}" class="space-y-8">
                        @csrf
                        @method('PUT')

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
                                    <x-owner-input-label for="price" :value="__('Harga (Rp)')" required />
                                    <x-owner-text-input id="price" class="block mt-1 w-full" type="text" name="price" :value="old('price', (string) (float) $product->price)" placeholder="Contoh: 50000" required />
                                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                    <p class="mt-1 text-xs text-[#8A7464]">Tulis angka tanpa titik atau koma.</p>
                                </div>

                                <div class="sm:col-span-2">
                                    <x-owner-input-label for="description" :value="__('Deskripsi')" />
                                    <x-owner-textarea id="description" name="description" rows="4" class="mt-1 block w-full">{{ old('description', $product->description) }}</x-textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>
                            </div>
                        </section>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <x-owner-primary-button class="sm:w-auto">{{ __('Simpan Perubahan') }}</x-primary-button>
                            <a href="{{ route('owner.products.index', $product->umkm) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#ECE5D9] bg-white px-5 text-sm font-semibold text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                {{ __('Batal') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <section class="mt-6 overflow-hidden rounded-2xl bg-white shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-[#ECE5D9]">
                <div class="p-6 sm:p-8">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#5C4033]">
                            <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                                <circle cx="9" cy="9" r="2" />
                                <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-sm font-semibold uppercase tracking-wider text-[#3F2A22]">Media Produk</h2>
                            <p class="text-xs text-[#8A7464]">Foto produk yang tampil di halaman publik</p>
                        </div>
                    </div>
                    <p class="mt-3 text-sm text-[#6F5D50]">Foto produk akan tampil di halaman publik. Format JPG, PNG, atau WEBP, maksimal 2 MB. Foto bersifat opsional.</p>

                    @php
                        $photo = $product->media->first();
                    @endphp

                    <div class="mt-4">
                        <x-owner-media-upload
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
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
