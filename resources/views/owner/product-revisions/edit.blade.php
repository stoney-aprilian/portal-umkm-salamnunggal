<x-app-layout title="{{ $revision->status === 'draft' ? __('Kelola Draft Perubahan') : __('Kelola Perubahan Produk') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-3">
            <div class="min-w-0">
                <h1 class="text-2xl font-semibold tracking-tight text-[#3F2A22] sm:text-3xl">
                    {{ $revision->status === 'draft' ? __('Kelola Draft Perubahan') : __('Kelola Perubahan Produk') }}
                </h1>
                <p class="mt-1 text-sm text-[#8A7464] sm:text-base">
                    Produk publik: <span class="font-medium text-[#3F2A22]">{{ $product->name }}</span>
                </p>
            </div>
            <x-badge :status="$revision->status" />
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
                $rejectionRequest = $revision->verificationRequests()->where('status', 'rejected')->latest('id')->first();
                $revisionRequest = $revision->verificationRequests()->where('status', 'needs_revision')->latest('id')->first();
                $latestNote = match (true) {
                    $revision->status === 'rejected' => $rejectionRequest?->notes,
                    $revision->status === 'needs_revision' => $revisionRequest?->notes,
                    default => null,
                };
            @endphp

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
                            <h3 class="text-sm font-semibold text-[#3F2A22]">Produk di publik tetap menampilkan versi yang disetujui</h3>
                            <p class="mt-1 text-sm text-[#6F5D50]">
                                Perubahan yang Anda kelola di sini akan tampil di halaman publik hanya setelah Administrator menyetujui.
                                <a href="{{ route('public.product.show', $product) }}" target="_blank" rel="noopener" class="font-medium text-[#C26A4A] hover:underline">{{ $product->name }}</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if ($revision->status === 'rejected')
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
                                <h3 class="text-sm font-semibold text-red-900">Pengajuan perubahan ini ditolak.</h3>
                                <p class="mt-1 text-sm text-red-800">
                                    <span class="font-semibold">Alasan Penolakan:</span> {{ $latestNote ?? '—' }}
                                </p>
                                <p class="mt-1 text-sm text-red-700">Perbaiki sesuai catatan, lalu kirim kembali. Produk di publik tetap tidak berubah.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($revision->status === 'needs_revision')
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
                                <h3 class="text-sm font-semibold text-amber-900">Pengajuan perubahan ini perlu diperbaiki.</h3>
                                <p class="mt-1 text-sm text-amber-800">
                                    <span class="font-semibold">Catatan Administrator:</span> {{ $latestNote ?? '—' }}
                                </p>
                                <p class="mt-1 text-sm text-amber-700">Perbaiki sesuai catatan, lalu kirim kembali. Produk di publik tetap tidak berubah.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($revision->status === 'draft')
                <div class="mb-6 overflow-hidden rounded-2xl bg-[#FAF6F5] shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-[#ECE5D9]">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-start gap-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                                    <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <h3 class="text-sm font-semibold text-[#3F2A22]">Perubahan masih berupa draft</h3>
                                <p class="mt-1 text-sm text-[#6F5D50]">
                                    Perbarui data dan foto sesuai keinginan, lalu kirim pengajuan agar diperiksa Administrator. Produk di publik <span class="font-semibold text-[#3F2A22]">tidak berubah</span> sampai perubahan disetujui.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($revision->status === 'pending')
                <div class="mb-6 overflow-hidden rounded-2xl bg-[#FAF6F5] shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-[#ECE5D9]">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-start gap-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <h3 class="text-sm font-semibold text-[#3F2A22]">Perubahan sedang diperiksa Administrator</h3>
                                <p class="mt-1 text-sm text-[#6F5D50]">Produk di publik tetap tidak berubah sampai perubahan disetujui.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="overflow-hidden rounded-2xl bg-white shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-[#ECE5D9]">
                <div class="p-6 sm:p-8">
                    <form method="POST" action="{{ route('owner.products.revisions.update', $revision) }}" class="space-y-8">
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
                                    <h2 class="text-sm font-semibold uppercase tracking-wider text-[#3F2A22]">Informasi Perubahan</h2>
                                    <p class="text-xs text-[#8A7464]">Nama, kategori, harga, dan deskripsi</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <x-owner-input-label for="name" :value="__('Nama Produk')" required />
                                    <x-owner-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $revision->name)" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-owner-input-label for="category_id" :value="__('Kategori')" required />
                                    <x-owner-select id="category_id" name="category_id" class="mt-1 block w-full" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected(old('category_id', $revision->category_id) == $category->id)>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </x-select>
                                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-owner-input-label for="price" :value="__('Harga')" required />
                                    <x-owner-text-input id="price" class="block mt-1 w-full" type="number" name="price" :value="old('price', $revision->price)" step="0.01" min="0" required />
                                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-2">
                                    <x-owner-input-label for="description" :value="__('Deskripsi')" />
                                    <x-owner-textarea id="description" name="description" rows="4" class="mt-1 block w-full">{{ old('description', $revision->description) }}</x-textarea>
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
                            <h2 class="text-sm font-semibold uppercase tracking-wider text-[#3F2A22]">Media Perubahan</h2>
                            <p class="text-xs text-[#8A7464]">Foto pengganti untuk produk ini</p>
                        </div>
                    </div>
                    <p class="mt-3 text-sm text-[#6F5D50]">Foto yang diunggah di sini menggantikan foto produk di publik, tetapi hanya tampil setelah perubahan disetujui. Format JPG, PNG, atau WEBP, maksimal 2 MB.</p>

                    @php
                        $photo = $revision->media->first();
                    @endphp

                    <div class="mt-4">
                        <x-owner-media-upload
                            title="Foto Produk Baru"
                            description="Foto pengganti. Foto yang sedang tampil di publik tetap dipakai sampai perubahan disetujui."
                            :current="$photo"
                            :store-url="route('owner.products.revisions.media.store', [$revision, 'product'])"
                            input-name="file_product"
                            :delete-url="$photo ? route('owner.media.destroy', $photo) : null"
                            item-label="foto produk"
                            optional
                            preview-class="max-h-48"
                        />
                    </div>
                </div>
            </section>

            @if ($revision->status === 'draft')
                <section class="mt-6 overflow-hidden rounded-2xl bg-white shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-[#ECE5D9]">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-start gap-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#5C4033] text-white">
                                <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 2 11 13" />
                                    <path d="M22 2 15 22 11 13 2 9l20-7z" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <h2 class="text-sm font-semibold text-[#3F2A22]">Kirim Pengajuan Perubahan</h2>
                                <p class="mt-1 text-sm text-[#6F5D50]">Pastikan seluruh data dan foto sudah sesuai, lalu kirim pengajuan untuk diperiksa Administrator. Produk di publik tetap tidak berubah sampai perubahan disetujui.</p>
                                <form method="POST" action="{{ route('owner.products.revisions.submit', $revision) }}" class="mt-4" onsubmit="return confirm('Yakin ingin mengirim pengajuan perubahan produk ini?');">
                                    @csrf
                                    <x-owner-primary-button>{{ __('Kirim Pengajuan Perubahan') }}</x-primary-button>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
