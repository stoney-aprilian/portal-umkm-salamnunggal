<x-app-layout title="Kelola Perubahan UMKM">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-3">
            <div class="min-w-0">
                <h1 class="text-2xl font-semibold tracking-tight text-[#3F2A22] sm:text-3xl">
                    {{ __('Kelola Perubahan UMKM') }}
                </h1>
                <p class="mt-1 text-sm text-[#8A7464] sm:text-base">
                    Perubahan untuk {{ $umkm->name }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <x-badge :status="$revision->status" />
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

            @if ($revision->status === 'rejected')
                @php
                    $rejectionRequest = $revision->verificationRequests()
                        ->where('status', 'rejected')
                        ->latest('id')
                        ->first();
                @endphp
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
                                    <span class="font-semibold">Alasan Penolakan:</span> {{ $rejectionRequest?->notes ?? '—' }}
                                </p>
                                <p class="mt-1 text-sm text-red-700">Perbaiki sesuai catatan, lalu kirim kembali. Data UMKM di publik tetap tidak berubah.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($revision->status === 'needs_revision')
                @php
                    $revisionRequest = $revision->verificationRequests()
                        ->where('status', 'needs_revision')
                        ->latest('id')
                        ->first();
                @endphp
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
                                    <span class="font-semibold">Catatan Administrator:</span> {{ $revisionRequest?->notes ?? '—' }}
                                </p>
                                <p class="mt-1 text-sm text-amber-700">Perbaiki sesuai catatan, lalu kirim kembali. Data UMKM di publik tetap tidak berubah.</p>
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
                                <h3 class="text-sm font-semibold text-[#3F2A22]">Perubahan masih berupa draft.</h3>
                                <p class="mt-1 text-sm text-[#6F5D50]">
                                    Perbarui data dan media sesuai keinginan, lalu kirim pengajuan agar diperiksa Administrator. Data UMKM di publik <span class="font-semibold text-[#3F2A22]">tidak berubah</span> sampai perubahan disetujui.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mb-6 overflow-hidden rounded-2xl bg-white shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-[#ECE5D9]">
                <div class="p-6 sm:p-8">
                    <p class="text-sm text-[#6F5D50]">
                        Data UMKM yang sedang tampil di publik:
                        <a href="{{ route('public.umkm.show', $umkm) }}" target="_blank" rel="noopener" class="font-semibold text-[#C26A4A] hover:underline">{{ $umkm->name }}</a>
                    </p>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl bg-white shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-[#ECE5D9]">
                <div class="p-6 sm:p-8">
                    <form method="POST" action="{{ route('owner.umkm.revisions.update', $revision) }}" class="space-y-8">
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
                                    <h2 class="text-sm font-semibold uppercase tracking-wider text-[#3F2A22]">Informasi Umum</h2>
                                    <p class="text-xs text-[#8A7464]">Identitas dasar UMKM</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <x-owner-input-label for="name" :value="__('Nama UMKM')" required />
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

                                <div class="sm:col-span-2">
                                    <x-owner-input-label for="description" :value="__('Deskripsi')" />
                                    <x-owner-textarea id="description" name="description" rows="4" class="mt-1 block w-full">{{ old('description', $revision->description) }}</x-textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>
                            </div>
                        </section>

                        <section class="space-y-5">
                            <div class="flex items-center gap-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#5C4033]">
                                    <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0Z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                </span>
                                <div>
                                    <h2 class="text-sm font-semibold uppercase tracking-wider text-[#3F2A22]">Alamat &amp; Operasional</h2>
                                    <p class="text-xs text-[#8A7464]">Lokasi dan jam kerja usaha</p>
                                </div>
                            </div>

                            <div>
                                <x-owner-input-label for="address" :value="__('Alamat')" />
                                <x-owner-textarea id="address" name="address" rows="3" class="mt-1 block w-full">{{ old('address', $revision->address) }}</x-textarea>
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <x-owner-input-label for="google_maps" :value="__('Google Maps')" />
                                    <x-owner-text-input id="google_maps" class="block mt-1 w-full" type="text" name="google_maps" :value="old('google_maps', $revision->google_maps)" placeholder="Contoh: https://maps.app.goo.gl/xxxxx" />
                                    <x-input-error :messages="$errors->get('google_maps')" class="mt-2" />
                                </div>

                                <div>
                                    <x-owner-input-label for="operational_hours" :value="__('Jam Operasional')" />
                                    <x-owner-text-input id="operational_hours" class="block mt-1 w-full" type="text" name="operational_hours" :value="old('operational_hours', $revision->operational_hours)" placeholder="Contoh: 08.00 - 17.00" />
                                    <x-input-error :messages="$errors->get('operational_hours')" class="mt-2" />
                                </div>
                            </div>
                        </section>

                        <section class="space-y-5">
                            <div class="flex items-center gap-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#5C4033]">
                                    <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                                    </svg>
                                </span>
                                <div>
                                    <h2 class="text-sm font-semibold uppercase tracking-wider text-[#3F2A22]">Kontak &amp; Media Sosial</h2>
                                    <p class="text-xs text-[#8A7464]">Informasi kontak dan akun sosial media</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <x-owner-input-label for="phone" :value="__('Nomor Telepon')" />
                                    <x-owner-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $revision->phone)" placeholder="Contoh: 0812-3456-7890" />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>

                                <div>
                                    <x-owner-input-label for="email" :value="__('Email')" />
                                    <x-owner-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $revision->email)" placeholder="Contoh: usaha@email.com" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <x-owner-input-label for="website" :value="__('Website')" />
                                    <x-owner-text-input id="website" class="block mt-1 w-full" type="text" name="website" :value="old('website', $revision->website)" placeholder="Contoh: https://usaha.com" />
                                    <x-input-error :messages="$errors->get('website')" class="mt-2" />
                                </div>

                                <div>
                                    <x-owner-input-label for="instagram" :value="__('Instagram')" />
                                    <x-owner-text-input id="instagram" class="block mt-1 w-full" type="text" name="instagram" :value="old('instagram', $revision->instagram)" placeholder="Contoh: @nama.usaha" />
                                    <x-input-error :messages="$errors->get('instagram')" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <x-owner-input-label for="facebook" :value="__('Facebook')" />
                                    <x-owner-text-input id="facebook" class="block mt-1 w-full" type="text" name="facebook" :value="old('facebook', $revision->facebook)" placeholder="Contoh: nama.usaha" />
                                    <x-input-error :messages="$errors->get('facebook')" class="mt-2" />
                                </div>

                                <div>
                                    <x-owner-input-label for="tiktok" :value="__('TikTok')" />
                                    <x-owner-text-input id="tiktok" class="block mt-1 w-full" type="text" name="tiktok" :value="old('tiktok', $revision->tiktok)" placeholder="Contoh: @nama.usaha" />
                                    <x-input-error :messages="$errors->get('tiktok')" class="mt-2" />
                                </div>
                            </div>
                        </section>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <x-owner-primary-button class="sm:w-auto">{{ __('Simpan Perubahan') }}</x-primary-button>
                            <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#ECE5D9] bg-white px-5 text-sm font-semibold text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
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
                            <h2 class="text-sm font-semibold uppercase tracking-wider text-[#3F2A22]">Media pada Perubahan Ini</h2>
                            <p class="text-xs text-[#8A7464]">Logo, banner, dan galeri foto perubahan</p>
                        </div>
                    </div>
                    <p class="mt-3 text-sm text-[#6F5D50]">
                        Media yang diunggah di sini menggantikan atau melengkapi media publik, tetapi hanya tampil setelah perubahan disetujui. Format JPG, PNG, atau WEBP, maksimal 2 MB per file.
                    </p>

                    @php
                        $logo = $revision->media->firstWhere('collection', 'logo');
                        $banner = $revision->media->firstWhere('collection', 'banner');
                        $gallery = $revision->media->where('collection', 'gallery')->sortBy('sort_order')->values();
                    @endphp

                    <div class="mt-4 space-y-4">
                        <x-owner-media-upload
                            title="Logo Baru"
                            description="Logo pengganti. Logo yang sedang tampil di publik tetap dipakai sampai perubahan disetujui."
                            :current="$logo"
                            :store-url="route('owner.umkm.revisions.media.store', [$revision, 'logo'])"
                            input-name="file_logo"
                            :delete-url="$logo ? route('owner.media.destroy', $logo) : null"
                            item-label="logo"
                            optional
                            preview-class="aspect-square w-32"
                        />

                        <x-owner-media-upload
                            title="Banner Baru"
                            description="Banner pengganti. Banner yang sedang tampil di publik tetap dipakai sampai perubahan disetujui."
                            :current="$banner"
                            :store-url="route('owner.umkm.revisions.media.store', [$revision, 'banner'])"
                            input-name="file_banner"
                            :delete-url="$banner ? route('owner.media.destroy', $banner) : null"
                            item-label="banner"
                            optional
                            preview-class="aspect-[3/1] w-full"
                        />

                        <div class="rounded-xl border border-[#ECE5D9] bg-[#FAF6F5] p-5">
                            <div class="flex items-baseline justify-between gap-3">
                                <h3 class="text-sm font-semibold text-[#3F2A22]">Galeri Tambahan</h3>
                                <span class="shrink-0 text-xs font-medium text-[#8A7464]">Opsional</span>
                            </div>
                            <p class="mt-1 text-sm text-[#6F5D50]">
                                Foto tambahan yang akan ditambahkan ke galeri publik setelah perubahan disetujui. Maksimal 5 gambar dalam satu unggahan.
                            </p>

                            <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                @forelse ($gallery as $item)
                                    <div class="rounded-lg border border-[#ECE5D9] bg-white p-2">
                                        <img src="{{ Storage::disk($item->disk)->url($item->path) }}" alt="Galeri perubahan {{ $revision->name }}" class="aspect-[4/3] w-full rounded object-cover">
                                        <form method="POST" action="{{ route('owner.media.destroy', $item) }}" class="mt-2" onsubmit="return confirm('Hapus foto galeri ini dari perubahan?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button class="w-full justify-center">{{ __('Hapus') }}</x-danger-button>
                                        </form>
                                    </div>
                                @empty
                                    <p class="col-span-full mt-0 text-sm text-[#8A7464]">Belum ada foto galeri pada perubahan ini.</p>
                                @endforelse
                            </div>

                            <form method="POST" action="{{ route('owner.umkm.revisions.media.store', [$revision, 'gallery']) }}" enctype="multipart/form-data" class="mt-4">
                                @csrf
                                <label for="gallery" class="block text-sm font-medium text-[#3F2A22]">{{ __('Pilih Foto Galeri') }}</label>
                                <input id="gallery" type="file" name="gallery[]" multiple accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full text-sm text-[#3F2A22] file:me-3 file:min-h-11 file:rounded-xl file:border-0 file:bg-[#5C4033] file:px-4 file:text-sm file:font-semibold file:text-white hover:file:bg-[#3F2A22]">
                                <x-input-error :messages="$errors->get('gallery')" class="mt-2" />
                                <x-input-error :messages="$errors->get('gallery.*')" class="mt-2" />
                                <x-owner-primary-button class="mt-3">{{ __('Unggah Galeri') }}</x-primary-button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            @if ($revision->status === 'draft')
                <section class="mt-6 overflow-hidden rounded-2xl bg-[#5C4033] shadow-[0_2px_12px_rgba(63,42,34,0.08)]">
                    <div class="p-6 sm:p-8">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <h2 class="text-sm font-semibold uppercase tracking-wider text-white">Kirim Pengajuan Perubahan</h2>
                                <p class="mt-1 text-sm text-[#E8D8C8]/80">
                                    Pastikan seluruh data dan media sudah sesuai, lalu kirim pengajuan untuk diperiksa Administrator.
                                </p>
                            </div>
                            <form method="POST" action="{{ route('owner.umkm.revisions.submit', $revision) }}" class="shrink-0" onsubmit="return confirm('Yakin ingin mengirim pengajuan perubahan UMKM ini?');">
                                @csrf
                                <x-owner-primary-button>{{ __('Kirim Pengajuan Perubahan') }}</x-primary-button>
                            </form>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
