<x-app-layout title="Perbaiki UMKM">
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ $umkm->status === 'draft' ? __('Ubah UMKM') : __('Perbaiki UMKM') }}
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
                    @if ($umkm->status === 'rejected')
                        @php
                            $rejectionRequest = $umkm->verificationRequests()
                                ->where('status', 'rejected')
                                ->latest('id')
                                ->first();
                        @endphp
                        <x-alert type="error" class="mb-6">
                            <p class="font-semibold text-red-800">Pengajuan UMKM Anda ditolak.</p>
                            <p class="mt-1 text-sm text-red-700">
                                Alasan Penolakan: {{ $rejectionRequest?->notes ?? '—' }}
                            </p>
                            <p class="mt-1 text-sm text-red-700">
                                Silakan perbaiki informasi UMKM sesuai catatan tersebut, lalu kirim kembali untuk diperiksa.
                            </p>
                        </x-alert>
                    @elseif ($umkm->status === 'needs_revision')
                        @php
                            $revisionRequest = $umkm->verificationRequests()
                                ->where('status', 'needs_revision')
                                ->latest('id')
                                ->first();
                        @endphp
                        <x-alert type="warning" class="mb-6">
                            <p class="font-semibold text-amber-800">Pengajuan UMKM Anda perlu diperbaiki.</p>
                            <p class="mt-1 text-sm text-amber-700">
                                Catatan Administrator: {{ $revisionRequest?->notes ?? '—' }}
                            </p>
                            <p class="mt-1 text-sm text-amber-700">
                                Silakan perbaiki informasi UMKM sesuai catatan tersebut, lalu kirim kembali untuk diperiksa.
                            </p>
                        </x-alert>
                    @endif

                    @if ($umkm->status === 'draft')
                        <p class="text-sm text-slate-600">
                            Perbarui data UMKM Anda, lalu simpan. UMKM akan tetap berstatus draft sampai Anda mengirim pengajuannya.
                        </p>
                        <p class="mt-2 text-sm font-medium text-emerald-700">
                            Langkah berikutnya: kirim pengajuan dari Dashboard setelah data selesai diperbarui.
                        </p>
                    @else
                        <p class="text-sm text-slate-600">
                            Perbaiki data UMKM sesuai catatan Administrator, lalu simpan. UMKM akan kembali menjadi draft dan dapat dikirim ulang untuk diperiksa.
                        </p>
                        <p class="mt-2 text-sm font-medium text-emerald-700">
                            Langkah berikutnya: setelah menyimpan, kirim pengajuan kembali agar diperiksa ulang.
                        </p>
                    @endif

                    <form method="POST" action="{{ route('owner.umkm.update', $umkm) }}" class="mt-6">
                        @csrf
                        @method('PUT')

                        <section class="space-y-4">
                            <h2 class="font-semibold text-slate-900">Informasi Umum</h2>

                            <div>
                                <x-input-label for="name" :value="__('Nama UMKM')" required />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $umkm->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="category_id" :value="__('Kategori')" required />
                                <x-select id="category_id" name="category_id" class="mt-1 block w-full" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id', $umkm->category_id) == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </x-select>
                                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="description" :value="__('Deskripsi')" />
                                <x-textarea id="description" name="description" rows="4" class="mt-1 block w-full">{{ old('description', $umkm->description) }}</x-textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </section>

                        <section class="mt-8 space-y-4">
                            <h2 class="font-semibold text-slate-900">Alamat &amp; Operasional</h2>

                            <div>
                                <x-input-label for="address" :value="__('Alamat')" />
                                <x-textarea id="address" name="address" rows="3" class="mt-1 block w-full">{{ old('address', $umkm->address) }}</x-textarea>
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <x-input-label for="google_maps" :value="__('Google Maps')" />
                                    <x-text-input id="google_maps" class="block mt-1 w-full" type="text" name="google_maps" :value="old('google_maps', $umkm->google_maps)" placeholder="Contoh: https://maps.app.goo.gl/xxxxx" />
                                    <x-input-error :messages="$errors->get('google_maps')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="operational_hours" :value="__('Jam Operasional')" />
                                    <x-text-input id="operational_hours" class="block mt-1 w-full" type="text" name="operational_hours" :value="old('operational_hours', $umkm->operational_hours)" placeholder="Contoh: 08.00 - 17.00" />
                                    <x-input-error :messages="$errors->get('operational_hours')" class="mt-2" />
                                </div>
                            </div>
                        </section>

                        <section class="mt-8 space-y-4">
                            <h2 class="font-semibold text-slate-900">Kontak &amp; Media Sosial</h2>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <x-input-label for="phone" :value="__('Nomor Telepon')" />
                                    <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $umkm->phone)" placeholder="Contoh: 0812-3456-7890" />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $umkm->email)" placeholder="Contoh: usaha@email.com" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <x-input-label for="website" :value="__('Website')" />
                                    <x-text-input id="website" class="block mt-1 w-full" type="text" name="website" :value="old('website', $umkm->website)" placeholder="Contoh: https://usaha.com" />
                                    <x-input-error :messages="$errors->get('website')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="instagram" :value="__('Instagram')" />
                                    <x-text-input id="instagram" class="block mt-1 w-full" type="text" name="instagram" :value="old('instagram', $umkm->instagram)" placeholder="Contoh: @nama.usaha" />
                                    <x-input-error :messages="$errors->get('instagram')" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <x-input-label for="facebook" :value="__('Facebook')" />
                                    <x-text-input id="facebook" class="block mt-1 w-full" type="text" name="facebook" :value="old('facebook', $umkm->facebook)" placeholder="Contoh: nama.usaha" />
                                    <x-input-error :messages="$errors->get('facebook')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="tiktok" :value="__('TikTok')" />
                                    <x-text-input id="tiktok" class="block mt-1 w-full" type="text" name="tiktok" :value="old('tiktok', $umkm->tiktok)" placeholder="Contoh: @nama.usaha" />
                                    <x-input-error :messages="$errors->get('tiktok')" class="mt-2" />
                                </div>
                            </div>
                        </section>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                            <x-primary-button class="sm:w-auto">{{ __('Simpan Perubahan') }}</x-primary-button>
                            <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-white border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 transition duration-300 hover:bg-slate-50">
                                {{ __('Batal') }}
                            </a>
                        </div>
                    </form>

                    <section class="mt-10">
                        <h2 class="font-semibold text-slate-900">Media UMKM</h2>
                        <p class="mt-1 text-sm text-slate-600">
                            Unggah logo, banner, dan galeri untuk ditampilkan di halaman publik. Format JPG, PNG, atau WEBP, maksimal 2 MB per file.
                        </p>

                        @php
                            $logo = $umkm->media->firstWhere('collection', 'logo');
                            $banner = $umkm->media->firstWhere('collection', 'banner');
                            $gallery = $umkm->media->where('collection', 'gallery')->sortBy('sort_order')->values();
                        @endphp

                        <div class="mt-4 space-y-4">
                            <x-media-upload
                                title="Logo"
                                description="Logo usaha Anda. Akan tampil sebagai identitas UMKM di halaman publik."
                                :current="$logo"
                                :store-url="route('owner.umkm.media.store', [$umkm, 'logo'])"
                                input-name="file_logo"
                                :delete-url="$logo ? route('owner.media.destroy', $logo) : null"
                                item-label="logo"
                                optional
                                preview-class="aspect-square w-32"
                            />

                            <x-media-upload
                                title="Banner"
                                description="Banner tampilan atas halaman UMKM Anda."
                                :current="$banner"
                                :store-url="route('owner.umkm.media.store', [$umkm, 'banner'])"
                                input-name="file_banner"
                                :delete-url="$banner ? route('owner.media.destroy', $banner) : null"
                                item-label="banner"
                                optional
                                preview-class="aspect-[3/1] w-full"
                            />

                            <div class="rounded-xl border border-slate-200 p-4">
                                <div class="flex items-baseline justify-between gap-3">
                                    <h3 class="text-sm font-semibold text-slate-900">Galeri</h3>
                                    <span class="shrink-0 text-xs font-medium text-slate-400">Opsional</span>
                                </div>
                                <p class="mt-1 text-sm text-slate-600">
                                    Kumpulan foto usaha. Maksimal 5 gambar dalam satu unggahan.
                                </p>

                                <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                    @forelse ($gallery as $item)
                                        <div class="rounded-lg border border-slate-200 p-2">
                                            <img src="{{ Storage::disk($item->disk)->url($item->path) }}" alt="Galeri {{ $umkm->name }}" class="aspect-[4/3] w-full rounded object-cover">
                                            <form method="POST" action="{{ route('owner.media.destroy', $item) }}" class="mt-2" onsubmit="return confirm('Hapus foto galeri ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <x-danger-button class="w-full justify-center">{{ __('Hapus') }}</x-danger-button>
                                            </form>
                                        </div>
                                    @empty
                                        <p class="col-span-full mt-0 text-sm text-slate-500">Belum ada foto galeri.</p>
                                    @endforelse
                                </div>

                                <form method="POST" action="{{ route('owner.umkm.media.store', [$umkm, 'gallery']) }}" enctype="multipart/form-data" class="mt-4">
                                    @csrf
                                    <label for="gallery" class="block text-sm font-medium text-slate-700">{{ __('Pilih Foto Galeri') }}</label>
                                    <input id="gallery" type="file" name="gallery[]" multiple accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full text-sm text-slate-700 file:me-3 file:min-h-11 file:rounded-xl file:border-0 file:bg-emerald-600 file:px-4 file:text-sm file:font-semibold file:text-white hover:file:bg-emerald-500">
                                    <x-input-error :messages="$errors->get('gallery')" class="mt-2" />
                                    <x-input-error :messages="$errors->get('gallery.*')" class="mt-2" />
                                    <x-primary-button class="mt-3">{{ __('Unggah Galeri') }}</x-primary-button>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
