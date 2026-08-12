<x-app-layout title="Ajukan UMKM">
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('Ajukan UMKM') }}
        </h1>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <ol class="flex flex-wrap items-center gap-x-3 gap-y-2 text-sm">
                <li class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-xs font-semibold text-emerald-700">1</span>
                    <span class="font-medium text-slate-700">Isi Data UMKM</span>
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
                        Isi data UMKM Anda dengan lengkap. Data akan disimpan sebagai draft, lalu Anda dapat mengirimkannya untuk diperiksa Administrator.
                    </p>
                    <p class="mt-2 text-xs text-slate-500">
                        Kolom bertanda <span class="font-semibold text-red-500">*</span> wajib diisi. Kolom lainnya opsional dan dapat dilengkapi kapan saja.
                    </p>

                    <form method="POST" action="{{ route('owner.umkm.store') }}" class="mt-6">
                        @csrf

                        <section class="space-y-4">
                            <h2 class="font-semibold text-slate-900">Informasi Umum</h2>

                            <div>
                                <x-input-label for="name" :value="__('Nama UMKM')" required />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

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
                                <x-input-label for="description" :value="__('Deskripsi')" />
                                <x-textarea id="description" name="description" rows="4" class="mt-1 block w-full">{{ old('description') }}</x-textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </section>

                        <section class="mt-8 space-y-4">
                            <h2 class="font-semibold text-slate-900">Alamat &amp; Operasional</h2>

                            <div>
                                <x-input-label for="address" :value="__('Alamat')" />
                                <x-textarea id="address" name="address" rows="3" class="mt-1 block w-full">{{ old('address') }}</x-textarea>
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <x-input-label for="google_maps" :value="__('Google Maps')" />
                                    <x-text-input id="google_maps" class="block mt-1 w-full" type="text" name="google_maps" :value="old('google_maps')" placeholder="Contoh: https://maps.app.goo.gl/xxxxx" />
                                    <x-input-error :messages="$errors->get('google_maps')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="operational_hours" :value="__('Jam Operasional')" />
                                    <x-text-input id="operational_hours" class="block mt-1 w-full" type="text" name="operational_hours" :value="old('operational_hours')" placeholder="Contoh: 08.00 - 17.00" />
                                    <x-input-error :messages="$errors->get('operational_hours')" class="mt-2" />
                                </div>
                            </div>
                        </section>

                        <section class="mt-8 space-y-4">
                            <h2 class="font-semibold text-slate-900">Kontak &amp; Media Sosial</h2>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <x-input-label for="phone" :value="__('Nomor Telepon')" />
                                    <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" placeholder="Contoh: 0812-3456-7890" />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" placeholder="Contoh: usaha@email.com" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <x-input-label for="website" :value="__('Website')" />
                                    <x-text-input id="website" class="block mt-1 w-full" type="text" name="website" :value="old('website')" placeholder="Contoh: https://usaha.com" />
                                    <x-input-error :messages="$errors->get('website')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="instagram" :value="__('Instagram')" />
                                    <x-text-input id="instagram" class="block mt-1 w-full" type="text" name="instagram" :value="old('instagram')" placeholder="Contoh: @nama.usaha" />
                                    <x-input-error :messages="$errors->get('instagram')" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <x-input-label for="facebook" :value="__('Facebook')" />
                                    <x-text-input id="facebook" class="block mt-1 w-full" type="text" name="facebook" :value="old('facebook')" placeholder="Contoh: nama.usaha" />
                                    <x-input-error :messages="$errors->get('facebook')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="tiktok" :value="__('TikTok')" />
                                    <x-text-input id="tiktok" class="block mt-1 w-full" type="text" name="tiktok" :value="old('tiktok')" placeholder="Contoh: @nama.usaha" />
                                    <x-input-error :messages="$errors->get('tiktok')" class="mt-2" />
                                </div>
                            </div>
                        </section>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                            <x-primary-button class="sm:w-auto">{{ __('Simpan Draft') }}</x-primary-button>
                            <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-white border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 transition duration-300 hover:bg-slate-50">
                                {{ __('Batal') }}
                            </a>
                            <p class="text-sm text-slate-500 sm:ms-auto">
                                Setelah disimpan, kirim pengajuan dari Dashboard.
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
