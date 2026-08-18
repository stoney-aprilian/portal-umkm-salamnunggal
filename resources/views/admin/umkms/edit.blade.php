<x-app-layout :title="$umkm->name . ' — Edit UMKM'">
    <div class="py-8 sm:py-10">
        <div class="container-page">
            <a href="{{ route('admin.umkms.show', $umkm) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-[#8A7464] transition-colors duration-150 hover:text-[#5C4033] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                Kembali ke Detail UMKM
            </a>

            <div class="mt-6">
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Edit UMKM</h1>
                <p class="mt-1 text-sm text-slate-600">Perbarui informasi UMKM yang terdaftar atas nama <span class="font-semibold text-slate-900">{{ $umkm->user?->name ?? '—' }}</span>. Status UMKM tidak berubah dengan pengeditan ini.</p>
            </div>

            @if (session('status'))
                <x-alert type="success" class="mt-6">{{ session('status') }}</x-alert>
            @endif
            @if (session('error'))
                <x-alert type="error" class="mt-6">{{ session('error') }}</x-alert>
            @endif

            <form method="POST" action="{{ route('admin.umkms.update', $umkm) }}" class="mt-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="rounded-2xl bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-base font-semibold text-slate-900">Pemilik & Kategori</h2>
                                <p class="mt-0.5 text-xs text-slate-500">Tentukan pemilik dan kategori UMKM.</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-5 p-5 sm:p-6">
                        <div>
                            <label for="owner_id" class="block text-sm font-medium text-slate-700">Pemilik (Owner) <span class="text-red-500" aria-hidden="true">*</span></label>
                            <x-select id="owner_id" name="owner_id" class="mt-1 block w-full" required>
                                <option value="">Pilih Owner</option>
                                @foreach ($owners as $owner)
                                    <option value="{{ $owner->id }}" @selected(old('owner_id', $umkm->user_id) == $owner->id)>
                                        {{ $owner->name }}{{ $owner->email ? ' ('.$owner->email.')' : '' }}
                                    </option>
                                @endforeach
                            </x-select>
                            <x-input-error :messages="$errors->get('owner_id')" class="mt-2" />
                        </div>

                        <div>
                            <label for="category_id" class="block text-sm font-medium text-slate-700">Kategori <span class="text-red-500" aria-hidden="true">*</span></label>
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
                    </div>
                </div>

                <div class="rounded-2xl bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-base font-semibold text-slate-900">Informasi Umum</h2>
                                <p class="mt-0.5 text-xs text-slate-500">Nama dan deskripsi UMKM yang tampil di portal.</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-5 p-5 sm:p-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700">Nama UMKM <span class="text-red-500" aria-hidden="true">*</span></label>
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $umkm->name) }}" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-slate-700">Deskripsi</label>
                            <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">{{ old('description', $umkm->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0Z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-base font-semibold text-slate-900">Alamat & Operasional</h2>
                                <p class="mt-0.5 text-xs text-slate-500">Lokasi dan jam operasional UMKM.</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-5 p-5 sm:p-6">
                        <div>
                            <label for="address" class="block text-sm font-medium text-slate-700">Alamat</label>
                            <textarea id="address" name="address" rows="3" class="mt-1 block w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">{{ old('address', $umkm->address) }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="google_maps" class="block text-sm font-medium text-slate-700">Google Maps</label>
                                <x-text-input id="google_maps" name="google_maps" type="text" class="mt-1 block w-full" value="{{ old('google_maps', $umkm->google_maps) }}" placeholder="Contoh: https://maps.app.goo.gl/xxxxx" />
                                <x-input-error :messages="$errors->get('google_maps')" class="mt-2" />
                            </div>

                            <div>
                                <label for="operational_hours" class="block text-sm font-medium text-slate-700">Jam Operasional</label>
                                <x-text-input id="operational_hours" name="operational_hours" type="text" class="mt-1 block w-full" value="{{ old('operational_hours', $umkm->operational_hours) }}" placeholder="Contoh: 08.00 - 17.00" />
                                <x-input-error :messages="$errors->get('operational_hours')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-base font-semibold text-slate-900">Kontak & Media Sosial</h2>
                                <p class="mt-0.5 text-xs text-slate-500">Informasi kontak yang ditampilkan di halaman publik UMKM.</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-5 p-5 sm:p-6">
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="phone" class="block text-sm font-medium text-slate-700">Nomor Telepon</label>
                                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" value="{{ old('phone', $umkm->phone) }}" placeholder="Contoh: 0812-3456-7890" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email', $umkm->email) }}" placeholder="Contoh: usaha@email.com" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="website" class="block text-sm font-medium text-slate-700">Website</label>
                                <x-text-input id="website" name="website" type="text" class="mt-1 block w-full" value="{{ old('website', $umkm->website) }}" placeholder="Contoh: https://usaha.com" />
                                <x-input-error :messages="$errors->get('website')" class="mt-2" />
                            </div>

                            <div>
                                <label for="instagram" class="block text-sm font-medium text-slate-700">Instagram</label>
                                <x-text-input id="instagram" name="instagram" type="text" class="mt-1 block w-full" value="{{ old('instagram', $umkm->instagram) }}" placeholder="Contoh: @nama.usaha" />
                                <x-input-error :messages="$errors->get('instagram')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="facebook" class="block text-sm font-medium text-slate-700">Facebook</label>
                                <x-text-input id="facebook" name="facebook" type="text" class="mt-1 block w-full" value="{{ old('facebook', $umkm->facebook) }}" placeholder="Contoh: nama.usaha" />
                                <x-input-error :messages="$errors->get('facebook')" class="mt-2" />
                            </div>

                            <div>
                                <label for="tiktok" class="block text-sm font-medium text-slate-700">TikTok</label>
                                <x-text-input id="tiktok" name="tiktok" type="text" class="mt-1 block w-full" value="{{ old('tiktok', $umkm->tiktok) }}" placeholder="Contoh: @nama.usaha" />
                                <x-input-error :messages="$errors->get('tiktok')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                    <a href="{{ route('admin.umkms.show', $umkm) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#C26A4A] px-5 text-sm font-semibold text-white transition duration-300 hover:bg-[#A8563A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
