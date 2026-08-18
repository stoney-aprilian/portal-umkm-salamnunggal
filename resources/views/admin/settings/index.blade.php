<x-app-layout title="Pengaturan Portal">
    <div class="py-8 sm:py-10">
        <div class="container-page">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-[#8A7464] transition-colors duration-150 hover:text-[#5C4033] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                Kembali ke Dashboard
            </a>

            <div class="mt-6 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Pengaturan Portal</h1>
                    <p class="mt-1 text-sm text-slate-600">Kelola informasi dan konfigurasi utama Portal UMKM Salamnunggal.</p>
                </div>
            </div>

            @if (session('status'))
                <x-alert type="success" class="mt-6">{{ session('status') }}</x-alert>
            @endif
            @if (session('error'))
                <x-alert type="error" class="mt-6">{{ session('error') }}</x-alert>
            @endif

            <form method="POST" action="{{ route('admin.settings.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="rounded-2xl bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9" />
                                    <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-base font-semibold text-slate-900">Identitas Portal</h2>
                                <p class="mt-0.5 text-xs text-slate-500">Nama, tagline, dan deskripsi portal yang tampil di navbar, footer, dan halaman publik.</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-5 p-5 sm:p-6">
                        <div>
                            <label for="site-name" class="block text-sm font-medium text-slate-700">Nama Portal <span class="text-red-500" aria-hidden="true">*</span></label>
                            <input id="site-name" name="site[name]" type="text" class="mt-1 block w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2" value="{{ old('site.name', $settings->get('site.name')) }}" required autocomplete="off">
                            @error('site.name')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="site-tagline" class="block text-sm font-medium text-slate-700">Tagline</label>
                            <input id="site-tagline" name="site[tagline]" type="text" class="mt-1 block w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2" value="{{ old('site.tagline', $settings->get('site.tagline', '')) }}" placeholder="Contoh: Desa Salamnunggal" autocomplete="off">
                            @error('site.tagline')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="site-description" class="block text-sm font-medium text-slate-700">Deskripsi Portal</label>
                            <textarea id="site-description" name="site[description]" rows="3" class="mt-1 block w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">{{ old('site.description', $settings->get('site.description', '')) }}</textarea>
                            @error('site.description')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-xs text-slate-500">Maksimal 1000 karakter. Kosongkan untuk memakai deskripsi bawaan.</p>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="file-logo" class="block text-sm font-medium text-slate-700">Logo</label>
                                @if ($settings->get('site.logo'))
                                    <div class="mt-2 flex items-center gap-3">
                                        <img src="{{ asset('storage/'.$settings->get('site.logo')) }}" alt="Logo portal" class="h-10 w-10 rounded-lg object-contain border border-slate-200">
                                        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                                            <input type="checkbox" name="remove_logo" value="1" class="rounded border-slate-300 text-[#C26A4A] focus:ring-[#C26A4A]">
                                            Hapus logo
                                        </label>
                                    </div>
                                @endif
                                <input id="file-logo" name="file_logo" type="file" accept="image/jpeg,image/png,image/webp,image/svg+xml" class="mt-2 block w-full text-sm text-slate-500 file:mr-3 file:rounded-xl file:border-0 file:bg-[#F4EDE1] file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-[#5C4033] hover:file:bg-[#E8D8C8]">
                                @error('file_logo')
                                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1.5 text-xs text-slate-500">JPG, PNG, WEBP, atau SVG. Maksimal 2 MB.</p>
                            </div>

                            <div>
                                <label for="file-favicon" class="block text-sm font-medium text-slate-700">Favicon</label>
                                @if ($settings->get('site.favicon'))
                                    <div class="mt-2 flex items-center gap-3">
                                        <img src="{{ asset('storage/'.$settings->get('site.favicon')) }}" alt="Favicon portal" class="h-8 w-8 rounded object-contain border border-slate-200">
                                        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                                            <input type="checkbox" name="remove_favicon" value="1" class="rounded border-slate-300 text-[#C26A4A] focus:ring-[#C26A4A]">
                                            Hapus favicon
                                        </label>
                                    </div>
                                @endif
                                <input id="file-favicon" name="file_favicon" type="file" accept="image/*" class="mt-2 block w-full text-sm text-slate-500 file:mr-3 file:rounded-xl file:border-0 file:bg-[#F4EDE1] file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-[#5C4033] hover:file:bg-[#E8D8C8]">
                                @error('file_favicon')
                                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1.5 text-xs text-slate-500">JPG, PNG, WEBP, SVG, atau ICO. Maksimal 2 MB.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                                    <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                                    <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-base font-semibold text-slate-900">Beranda</h2>
                                <p class="mt-0.5 text-xs text-slate-500">Judul, deskripsi, dan gambar hero yang tampil di bagian atas halaman beranda.</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-5 p-5 sm:p-6">
                        <div>
                            <label for="site-hero-title" class="block text-sm font-medium text-slate-700">Judul Hero <span class="text-red-500" aria-hidden="true">*</span></label>
                            <input id="site-hero-title" name="site[hero_title]" type="text" class="mt-1 block w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2" value="{{ old('site.hero_title', $settings->get('site.hero_title')) }}" required autocomplete="off">
                            @error('site.hero_title')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="site-hero-description" class="block text-sm font-medium text-slate-700">Deskripsi Hero</label>
                            <textarea id="site-hero-description" name="site[hero_description]" rows="3" class="mt-1 block w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">{{ old('site.hero_description', $settings->get('site.hero_description', '')) }}</textarea>
                            @error('site.hero_description')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-xs text-slate-500">Maksimal 1000 karakter. Kosongkan untuk memakai deskripsi bawaan.</p>
                        </div>

                        <div>
                            <label for="file-hero-image" class="block text-sm font-medium text-slate-700">Gambar Hero</label>
                            @if ($settings->get('site.hero_image'))
                                <div class="mt-2 flex items-center gap-3">
                                    <img src="{{ asset('storage/'.$settings->get('site.hero_image')) }}" alt="Gambar hero portal" class="h-16 w-28 rounded-lg object-cover border border-slate-200">
                                    <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                                        <input type="checkbox" name="remove_hero_image" value="1" class="rounded border-slate-300 text-[#C26A4A] focus:ring-[#C26A4A]">
                                        Hapus gambar
                                    </label>
                                </div>
                            @endif
                            <input id="file-hero-image" name="file_hero_image" type="file" accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full text-sm text-slate-500 file:mr-3 file:rounded-xl file:border-0 file:bg-[#F4EDE1] file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-[#5C4033] hover:file:bg-[#E8D8C8]">
                            @error('file_hero_image')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-xs text-slate-500">JPG, PNG, atau WEBP. Maksimal 2 MB.</p>
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
                                <h2 class="text-base font-semibold text-slate-900">Kontak Resmi</h2>
                                <p class="mt-0.5 text-xs text-slate-500">Informasi kontak yang ditampilkan di halaman Kontak dan footer.</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-5 p-5 sm:p-6">
                        <div>
                            <label for="contact-address" class="block text-sm font-medium text-slate-700">Alamat Kantor</label>
                            <input id="contact-address" name="contact[address]" type="text" class="mt-1 block w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2" value="{{ old('contact.address', $settings->get('contact.address', '')) }}" autocomplete="off">
                            @error('contact.address')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="contact-phone" class="block text-sm font-medium text-slate-700">Telepon</label>
                                <input id="contact-phone" name="contact[phone]" type="text" class="mt-1 block w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2" value="{{ old('contact.phone', $settings->get('contact.phone', '')) }}" autocomplete="off">
                                @error('contact.phone')
                                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="contact-whatsapp" class="block text-sm font-medium text-slate-700">WhatsApp</label>
                                <input id="contact-whatsapp" name="contact[whatsapp]" type="text" class="mt-1 block w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2" value="{{ old('contact.whatsapp', $settings->get('contact.whatsapp', '')) }}" autocomplete="off">
                                @error('contact.whatsapp')
                                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="contact-email" class="block text-sm font-medium text-slate-700">Email</label>
                            <input id="contact-email" name="contact[email]" type="email" class="mt-1 block w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2" value="{{ old('contact.email', $settings->get('contact.email', '')) }}" autocomplete="off">
                            @error('contact.email')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact-website" class="block text-sm font-medium text-slate-700">Situs Web</label>
                            <input id="contact-website" name="contact[website]" type="url" class="mt-1 block w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2" value="{{ old('contact.website', $settings->get('contact.website', '')) }}" placeholder="https://..." autocomplete="off">
                            @error('contact.website')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="contact-hours" class="block text-sm font-medium text-slate-700">Jam Pelayanan</label>
                                <input id="contact-hours" name="contact[hours]" type="text" class="mt-1 block w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2" value="{{ old('contact.hours', $settings->get('contact.hours', '')) }}" autocomplete="off">
                                @error('contact.hours')
                                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="contact-maps-url" class="block text-sm font-medium text-slate-700">Tautan Google Maps</label>
                                <input id="contact-maps-url" name="contact[maps_url]" type="url" class="mt-1 block w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2" value="{{ old('contact.maps_url', $settings->get('contact.maps_url', '')) }}" placeholder="https://maps.google.com/..." autocomplete="off">
                                @error('contact.maps_url')
                                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="18" cy="5" r="3" />
                                    <circle cx="6" cy="12" r="3" />
                                    <circle cx="18" cy="19" r="3" />
                                    <line x1="8.59" x2="15.42" y1="13.51" y2="17.49" />
                                    <line x1="15.41" x2="8.59" y1="6.51" y2="10.49" />
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-base font-semibold text-slate-900">Media Sosial</h2>
                                <p class="mt-0.5 text-xs text-slate-500">Tautan media sosial yang tampil di halaman Kontak dan footer.</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-5 p-5 sm:p-6">
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="social-instagram" class="block text-sm font-medium text-slate-700">Instagram</label>
                                <input id="social-instagram" name="social[instagram]" type="url" class="mt-1 block w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2" value="{{ old('social.instagram', $settings->get('social.instagram', '')) }}" placeholder="https://instagram.com/..." autocomplete="off">
                                @error('social.instagram')
                                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="social-facebook" class="block text-sm font-medium text-slate-700">Facebook</label>
                                <input id="social-facebook" name="social[facebook]" type="url" class="mt-1 block w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2" value="{{ old('social.facebook', $settings->get('social.facebook', '')) }}" placeholder="https://facebook.com/..." autocomplete="off">
                                @error('social.facebook')
                                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
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
