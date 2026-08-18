<x-guest-layout title="Daftar">
    {{-- Brand header: logo + title + subtitle --}}
    <div class="flex flex-col items-center gap-2 py-4 border-b border-[#ECE5D9] bg-[#FAF6F5]">
        <div class="flex items-center gap-2">
            <svg class="h-5 w-5 text-[#C26A4A]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0Z" />
                <circle cx="12" cy="10" r="3" />
            </svg>
            <span class="text-xl font-bold text-[#3F2A22]">Portal UMKM</span>
        </div>
        <p class="text-sm text-[#6F5D50]">Desa Salamnunggal</p>
    </div>

    {{-- Registration card --}}
    <div class="container-page py-6 sm:py-8 bg-[#FAF6F5]">
        <div class="max-w-md mx-auto space-y-4 p-5 sm:p-6 rounded-2xl border border-[#ECE5D9] bg-white shadow-[0_4px_12px_rgba(63,42,34,0.1)]">
            {{-- Heading + supporting text --}}
            <div class="text-center">
                <h1 class="text-xl font-semibold tracking-tight text-[#3F2A22] mb-1">Daftar</h1>
                <p class="text-base text-[#6F5D50] leading-relaxed">Buat akun untuk mengelola UMKM dan produk Desa Salamnunggal.</p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                {{-- Name --}}
                <div>
                    <x-input-label for="name" :value="__('Nama')" />
                    <x-text-input id="name" class="block min-h-12 w-full rounded-xl border border-[#E3D9CB] bg-white px-4 py-2.5 text-base text-[#3F2A22] placeholder-[#A99A8C] shadow-sm focus:border-[#C26A4A] focus:ring-[#C26A4A] focus:ring-offset-2" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="text-xs text-[#C26A4A] mt-1" />
                </div>

                {{-- Email --}}
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block min-h-12 w-full rounded-xl border border-[#E3D9CB] bg-white px-4 py-2.5 text-base text-[#3F2A22] placeholder-[#A99A8C] shadow-sm focus:border-[#C26A4A] focus:ring-[#C26A4A] focus:ring-offset-2" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="text-xs text-[#C26A4A] mt-1" />
                </div>

                {{-- Password --}}
                <div>
                    <x-input-label for="password" :value="__('Kata Sandi')" />

                    <x-text-input id="password" class="block min-h-12 w-full rounded-xl border border-[#E3D9CB] bg-white px-4 py-2.5 text-base text-[#3F2A22] placeholder-[#A99A8C] shadow-sm focus:border-[#C26A4A] focus:ring-[#C26A4A] focus:ring-offset-2" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="text-xs text-[#C26A4A] mt-1" />
                </div>

                {{-- Confirm Password --}}
                <div>
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />

                    <x-text-input id="password_confirmation" class="block min-h-12 w-full rounded-xl border border-[#E3D9CB] bg-white px-4 py-2.5 text-base text-[#3F2A22] placeholder-[#A99A8C] shadow-sm focus:border-[#C26A4A] focus:ring-[#C26A4A] focus:ring-offset-2" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="text-xs text-[#C26A4A] mt-1" />
                </div>

                {{-- Helper text: account purpose --}}
                <p class="text-sm text-[#8A7464] text-center">
                    Akun ini digunakan untuk mengelola profil UMKM dan produk Desa Salamnunggal.
                </p>

                {{-- Primary CTA: "Daftar" --}}
                <div>
                    <x-primary-button class="w-full min-h-12 rounded-xl bg-[#C26A4A] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors duration-150 hover:bg-[#5C4033] focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#C26A4A]">
                        {{ __('Daftar') }}
                    </x-primary-button>
                </div>

                {{-- Secondary link: "Sudah punya akun? Masuk" --}}
                <div class="text-center">
                    <p class="text-sm text-[#6F5D50]">
                        Sudah punya akun? <a href="{{ route('login') }}" class="font-medium text-[#C26A4A] hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">Masuk</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>