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
                    <h2 class="font-semibold text-slate-900">Verifikasi Calon Owner</h2>
                    <p class="mt-1 text-sm text-slate-600">
                        Hubungi pendaftar untuk memastikan bahwa ia merupakan pemilik atau pengelola UMKM dan bermaksud mendaftarkan UMKM ke Portal UMKM Salamnunggal.
                    </p>

                    @if ($owner->phone)
                        @php
                            $whatsappNumber = str_starts_with($owner->phone, '08')
                                ? '62' . substr($owner->phone, 1)
                                : $owner->phone;
                            $whatsappMessage = rawurlencode('Halo, saya Admin Portal UMKM Salamnunggal. Kami ingin mengonfirmasi pendaftaran akun Anda sebagai Owner UMKM. Apakah Anda bermaksud mendaftarkan UMKM Anda di Portal UMKM Salamnunggal?');
                        @endphp
                        <a href="https://wa.me/{{ $whatsappNumber }}?text={{ $whatsappMessage }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-[#25D366] bg-[#25D366] px-5 text-sm font-semibold text-white transition duration-300 hover:bg-[#20bd5a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#25D366] focus-visible:ring-offset-2">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Hubungi via WhatsApp
                        </a>
                    @else
                        <p class="mt-3 text-sm text-slate-500">Nomor telepon belum tersedia untuk kontak.</p>
                    @endif
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