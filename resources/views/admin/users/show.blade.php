<x-app-layout :title="$user->name . ' — Kelola Pengguna'">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
            </div>
            <h1 class="font-semibold text-xl text-slate-900 leading-tight">{{ $user->name }}</h1>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="container-page">
            @if (session('status'))
                <x-alert type="success" class="mb-4">{{ session('status') }}</x-alert>
            @endif
            @if (session('error'))
                <x-alert type="error" class="mb-4">{{ session('error') }}</x-alert>
            @endif

            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('admin.users.index') }}" class="inline-flex min-h-11 items-center gap-2 text-sm font-medium text-[#8A7464] transition-colors duration-150 hover:text-[#5C4033]">
                    <span aria-hidden="true">&larr;</span>
                    Kembali ke Kelola Pengguna
                </a>
                <x-user-status-badge :status="$user->status" />
            </div>

            <div class="mt-6 rounded-2xl bg-white shadow-sm">
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
                <div class="p-5 sm:p-6">
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
                        <div>
                            <dt class="font-medium text-slate-500">Nama</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Email</dt>
                            <dd class="mt-0.5 text-slate-900 break-all">{{ $user->email ?? 'Tanpa email' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Nomor Telepon</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $user->phone ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Terdaftar</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $user->created_at->format('d M Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="mt-6 rounded-2xl bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0Z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">UMKM</h2>
                            <p class="mt-0.5 text-xs text-slate-500">UMKM yang dimiliki oleh owner ini.</p>
                        </div>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    @if ($user->umkm)
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
                            <div>
                                <dt class="font-medium text-slate-500">Nama</dt>
                                <dd class="mt-0.5 text-slate-900">{{ $user->umkm->name }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-slate-500">Kategori</dt>
                                <dd class="mt-0.5 text-slate-900">{{ $user->umkm->category?->name ?? '—' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="font-medium text-slate-500">Status</dt>
                                <dd class="mt-1">
                                    <x-badge :status="$user->umkm->status" />
                                </dd>
                            </div>
                        </dl>

                        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                            <a href="{{ route('admin.umkms.show', $user->umkm) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-[#C26A4A] transition duration-300 hover:border-[#C26A4A] hover:bg-[#FAF8F5] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                Kelola UMKM
                            </a>
                            @if ($user->umkm->status === 'approved')
                                <a href="{{ route('public.umkm.show', $user->umkm) }}" target="_blank" rel="noopener" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                    Lihat Halaman Publik
                                </a>
                            @endif
                        </div>
                    @else
                        <p class="mt-3 text-sm text-slate-500">Owner ini belum memiliki UMKM.</p>
                        <a href="{{ route('admin.umkms.create', ['owner' => $user->id]) }}" class="mt-4 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#C26A4A] px-5 text-sm font-semibold text-white transition duration-300 hover:bg-[#A8563A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            Buat UMKM atas nama Owner ini
                        </a>
                    @endif
                </div>
            </div>

            <div class="mt-6 rounded-2xl bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3" />
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Tindakan Akun</h2>
                            <p class="mt-0.5 text-xs text-slate-500">Kelola status akun dan kata sandi owner.</p>
                        </div>
                    </div>
                </div>
                <div class="p-5 sm:p-6 space-y-6">
                    <div>
                        @if ($user->status === 'suspended')
                            <form method="POST" action="{{ route('admin.users.activate', $user) }}" onsubmit="event.preventDefault(); confirmAction(this, 'Aktifkan Akun?', 'Akun {{ $user->name }} akan diaktifkan kembali dan owner dapat masuk.', 'success', 'Aktifkan Kembali', 'Batal');">
                                @csrf
                                <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#C26A4A] px-5 text-sm font-semibold text-white transition duration-300 hover:bg-[#A8563A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                    Aktifkan Kembali
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.users.suspend', $user) }}" onsubmit="event.preventDefault(); confirmAction(this, 'Nonaktifkan Akun?', 'Akun {{ $user->name }} akan dinonaktifkan. Owner tidak akan dapat masuk sampai akun diaktifkan kembali.', 'danger', 'Nonaktifkan Akun', 'Batal');">
                                @csrf
                                <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-5 text-sm font-semibold text-white transition duration-300 hover:bg-red-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">
                                    Nonaktifkan Akun
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="border-t border-slate-100 pt-6">
                        <h3 class="text-sm font-semibold text-slate-900">Reset Kata Sandi</h3>
                        <p class="mt-1 text-sm text-slate-600">Ganti kata sandi owner. Owner harus menggunakan kata sandi baru pada saat masuk berikutnya.</p>

                        <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @csrf

                            <div>
                                <label for="reset-password" class="block text-sm font-medium text-slate-700">
                                    Kata Sandi Baru
                                    <span class="text-red-500" aria-hidden="true">*</span>
                                </label>
                                <x-text-input id="reset-password" name="password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                                @if ($errors->reset->any())
                                    <x-input-error :messages="$errors->reset->get('password')" class="mt-2" />
                                @endif
                            </div>

                            <div>
                                <label for="reset-password_confirmation" class="block text-sm font-medium text-slate-700">
                                    Konfirmasi Kata Sandi Baru
                                    <span class="text-red-500" aria-hidden="true">*</span>
                                </label>
                                <x-text-input id="reset-password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                            </div>

                            <div class="sm:col-span-2">
                                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                    Reset Kata Sandi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="mt-6 rounded-2xl bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#F4EDE1] text-[#5C4033]" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Aktivitas Terakhir</h2>
                            <p class="mt-0.5 text-xs text-slate-500">Tindakan administrator yang tercatat pada akun ini.</p>
                        </div>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    @if ($activities->isNotEmpty())
                        <ul class="divide-y divide-slate-100">
                            @foreach ($activities as $activity)
                                <li class="flex flex-col gap-1 py-3 first:pt-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between">
                                    <p class="break-words text-sm leading-relaxed text-slate-700">{{ $activity->description }}</p>
                                    <p class="shrink-0 text-xs text-slate-400">{{ $activity->created_at->format('d M Y H:i') }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-slate-500">Belum ada aktivitas yang tercatat pada akun ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
