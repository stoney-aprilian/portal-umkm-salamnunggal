<x-app-layout :title="$user->name . ' — Edit Owner'">
    <div class="py-8 sm:py-10">
        <div class="container-page">
            <a href="{{ route('admin.users.show', $user) }}" class="inline-flex min-h-11 items-center gap-2 text-sm font-medium text-[#8A7464] transition-colors duration-150 hover:text-[#5C4033]">
                <span aria-hidden="true">&larr;</span>
                Kembali ke Detail Owner
            </a>

            <div class="mt-6">
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Edit Owner</h1>
                <p class="mt-1 text-sm text-slate-600">Perbarui data akun owner. Status akun diatur melalui tindakan Nonaktifkan/Aktifkan di halaman detail.</p>
            </div>

            @if (session('status'))
                <x-alert type="success" class="mt-6">{{ session('status') }}</x-alert>
            @endif
            @if (session('error'))
                <x-alert type="error" class="mt-6">{{ session('error') }}</x-alert>
            @endif

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="rounded-2xl bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-base font-semibold text-slate-900">Informasi Akun</h2>
                                <p class="mt-0.5 text-xs text-slate-500">Data identitas dan kontak owner.</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-5 p-5 sm:p-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700">Nama <span class="text-red-500" aria-hidden="true">*</span></label>
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email', $user->email) }}" autocomplete="email" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            <p class="mt-1 text-xs text-slate-500">Kosongkan jika owner tidak memiliki email (layanan dibantu oleh Administrator).</p>
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-700">Nomor Telepon</label>
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" value="{{ old('phone', $user->phone) }}" autocomplete="tel" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                    <a href="{{ route('admin.users.show', $user) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
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
