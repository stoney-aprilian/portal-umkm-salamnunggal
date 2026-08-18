<x-app-layout :title="$owner->name . ' — Verifikasi Owner'">
    <x-slot name="header">
        <div class="flex flex-wrap items-center gap-2">
            <h1 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ $owner->name }}
            </h1>
            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">Verifikasi Akun Owner</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <x-alert type="success" class="mb-4">{{ session('status') }}</x-alert>
            @endif

            @if (session('error'))
                <x-alert type="error" class="mb-4">{{ session('error') }}</x-alert>
            @endif

            <a href="{{ route('admin.owner-verification.index') }}" class="inline-flex min-h-11 items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900">
                <span aria-hidden="true">&larr;</span>
                {{ __('Kembali ke Antrean') }}
            </a>

            <div class="mt-4 card">
                <div class="p-6">
                    <h2 class="font-semibold text-slate-900">Data Akun</h2>
                    <p class="mt-1 text-sm text-slate-600">
                        Data akun dari Self-Service pendaftaran. Data UMKM (jika sudah ada) ditampilkan di bawah untuk konteks.
                    </p>
                    <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
                        <div>
                            <dt class="font-medium text-slate-500">Nama</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $owner->name }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Email</dt>
                            <dd class="mt-0.5 text-slate-900 break-all">{{ $owner->email ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Nomor Telepon</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $owner->phone ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Status Akun</dt>
                            <dd class="mt-0.5 text-slate-900">
                                <x-user-status-badge :status="$owner->status" />
                            </dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Didaftarkan</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $request->created_at->format('d M Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="mt-6 card">
                <div class="p-6">
                    <h2 class="font-semibold text-slate-900">Data UMKM (Konteks)</h2>
                    <p class="mt-1 text-sm text-slate-600">
                        Informasi usaha milik owner, jika sudah tersedia. Data bisnis diverifikasi melalui alur Verifikasi UMKM/Produk yang terpisah.
                    </p>

                    @if ($owner->umkm === null)
                        <p class="mt-3 text-sm text-slate-500">
                            Owner belum memiliki UMKM terdaftar. UMKM dapat ditambahkan setelah akun disetujui.
                        </p>
                    @else
                        <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
                            <div>
                                <dt class="font-medium text-slate-500">Nama UMKM</dt>
                                <dd class="mt-0.5 text-slate-900">{{ $owner->umkm->name }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-slate-500">Status UMKM</dt>
                                <dd class="mt-0.5 text-slate-900">
                                    <x-badge :status="$owner->umkm->status" />
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="font-medium text-slate-500">Kategori</dt>
                                <dd class="mt-0.5 text-slate-900">{{ $owner->umkm->category?->name ?? '—' }}</dd>
                            </div>
                        </dl>
                    @endif
                </div>
            </div>

            @if ($request->status === 'pending')
                <div class="mt-6 card">
                    <div class="p-6">
                        <h2 class="font-semibold text-slate-900">Tindakan Pemeriksaan</h2>
                        <p class="mt-1 text-sm text-slate-600">
                            Setujui jika data akun lengkap dan sesuai. Gunakan Perlu Revisi untuk meminta perbaikan data akun, atau Tolak jika akun tidak memenuhi ketentuan.
                        </p>

                        <x-verification-actions
                            :approve-url="route('admin.owner-verification.approve', $request)"
                            :reject-url="route('admin.owner-verification.reject', $request)"
                            :revision-url="route('admin.owner-verification.needs-revision', $request)"
                            subject="akun owner ini"
                        />
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>