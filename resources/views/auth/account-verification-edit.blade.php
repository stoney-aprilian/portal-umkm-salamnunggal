<x-guest-layout title="Perbaiki Data Akun">
    <div class="mb-4 text-sm text-slate-600">
        Perbaiki data akun yang diminta Administrator, lalu ajukan kembali akun Anda untuk verifikasi.
    </div>

    <form method="POST" action="{{ route('account.verification.update') }}">
        @csrf
        @method('PUT')

        <div>
            <x-input-label for="name" :value="__('Nama')" />
            <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email', $user->email)" autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="phone" :value="__('Nomor Telepon')" />
            <x-text-input id="phone" class="mt-1 block w-full" type="text" name="phone" :value="old('phone', $user->phone)" autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="mt-6 flex items-center justify-between gap-3">
            <a href="{{ route('account.verification.notice') }}" class="text-sm font-medium text-emerald-600 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                Kembali
            </a>

            <x-primary-button>
                {{ __('Simpan') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>