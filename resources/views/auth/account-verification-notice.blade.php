<x-guest-layout title="Verifikasi Akun">
    @if (session('status'))
        <div class="mb-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex items-center gap-3">
        <h2 class="text-xl font-semibold text-slate-900">Verifikasi Akun</h2>
        <x-user-status-badge :status="$user->status" />
    </div>

    @if ($user->status === 'pending')
        <div class="mt-4 space-y-3 text-sm leading-relaxed text-slate-600">
            <p>
                Akun Anda sedang menunggu verifikasi Administrator. Sebelum akun disetujui, Anda belum dapat
                menggunakan dashboard Owner maupun Self-Service UMKM/Produk.
            </p>
            <p class="rounded-xl bg-amber-50 px-4 py-3 text-amber-800">
                Anda akan diberitahu melalui halaman ini begitu akun Anda diperiksa.
            </p>
        </div>
    @elseif ($user->status === 'needs_revision')
        <div class="mt-4 space-y-3 text-sm leading-relaxed text-slate-600">
            <p>
                Akun Anda perlu diperbaiki sebelum dapat disetujui. Perbaiki data yang diminta Administrator,
                lalu ajukan kembali akun Anda untuk verifikasi.
            </p>

            @if ($latest?->notes)
                <p class="rounded-xl bg-orange-50 px-4 py-3 text-orange-800">
                    <span class="font-semibold">Catatan Administrator:</span>
                    {{ $latest->notes }}
                </p>
            @endif

            <div class="pt-1">
                <a href="{{ route('account.verification.edit') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-emerald-600 px-5 text-sm font-semibold text-white transition duration-300 hover:bg-emerald-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                    Perbaiki Data Akun
                </a>
            </div>

            <form method="POST" action="{{ route('account.verification.submit') }}" onsubmit="event.preventDefault(); confirmAction(this, 'Ajukan Kembali Akun?', 'Akun Anda akan diajukan kembali untuk verifikasi Administrator.', 'success', 'Ajukan Kembali', 'Batal');">
                @csrf
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-emerald-700 transition duration-300 hover:border-emerald-300 hover:bg-emerald-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                    Ajukan Kembali untuk Verifikasi
                </button>
            </form>
        </div>
    @elseif ($user->status === 'rejected')
        <div class="mt-4 space-y-3 text-sm leading-relaxed text-slate-600">
            <p>
                Akun Anda tidak disetujui. Anda tidak dapat menggunakan dashboard Owner maupun Self-Service
                UMKM/Produk dengan akun ini.
            </p>

            @if ($latest?->notes)
                <p class="rounded-xl bg-red-50 px-4 py-3 text-red-700">
                    <span class="font-semibold">Alasan Penolakan:</span>
                    {{ $latest->notes }}
                </p>
            @endif

            <p>
                Jika Anda merasa keputusan ini keliru, silakan hubungi Administrator Desa Salamnunggal.
            </p>
        </div>
    @endif

    <div class="mt-6 flex items-center justify-between">
        <a href="{{ url('/') }}" class="text-sm font-medium text-emerald-600 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
            Kembali ke Beranda
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-slate-600 underline hover:text-slate-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
