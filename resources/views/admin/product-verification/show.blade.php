<x-app-layout :title="$product->name . ' — Verifikasi Produk'">
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ $product->name }}
        </h1>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <x-alert type="success" class="mb-4">{{ session('status') }}</x-alert>
            @endif

            @if (session('error'))
                <x-alert type="error" class="mb-4">{{ session('error') }}</x-alert>
            @endif

            <a href="{{ route('admin.products.verification.index') }}" class="inline-flex min-h-11 items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900">
                <span aria-hidden="true">&larr;</span>
                {{ __('Kembali ke Antrean') }}
            </a>

            @if ($product->status === 'approved' && $product->umkm?->status === 'approved')
                <a href="{{ route('public.product.show', $product) }}" target="_blank" rel="noopener" class="mt-4 inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50">
                    Lihat Halaman Publik
                </a>
            @endif

            <div class="mt-4 card">
                <div class="p-6">
                    <h2 class="font-semibold text-slate-900">Informasi Produk</h2>
                    <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
                        <div>
                            <dt class="font-medium text-slate-500">Nama</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $product->name }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Kategori</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $product->category?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Harga</dt>
                            <dd class="mt-0.5 text-slate-900">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Status</dt>
                            <dd class="mt-0.5 text-slate-900">{{ match ($product->status) {
                                'draft' => 'Draft',
                                'pending' => 'Menunggu Pemeriksaan',
                                'approved' => 'Disetujui',
                                'needs_revision' => 'Perlu Revisi',
                                'rejected' => 'Ditolak',
                                default => $product->status,
                            } }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="font-medium text-slate-500">Deskripsi</dt>
                            <dd class="mt-0.5 text-slate-900 whitespace-pre-line">{{ $product->description ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">UMKM</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $product->umkm?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Diajukan</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $request->created_at->format('d M Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="mt-6 card">
                <div class="p-6">
                    <h2 class="font-semibold text-slate-900">Foto Produk</h2>
                    <p class="mt-1 text-sm text-slate-600">
                        Foto yang diunggah pemilik untuk produk ini.
                    </p>

                    @php
                        $photo = $product->media->first();
                    @endphp

                    @if ($photo)
                        <img src="{{ Storage::disk($photo->disk)->url($photo->path) }}" alt="Foto {{ $product->name }}" class="mt-3 max-w-sm rounded-lg border border-slate-200 object-cover">
                    @else
                        <p class="mt-3 text-sm text-slate-500">
                            Belum ada foto produk yang diunggah. Produk tanpa foto kurang menarik di halaman publik.
                        </p>
                    @endif
                </div>
            </div>

            <div class="mt-6 card">
                <div class="p-6">
                    <h2 class="font-semibold text-slate-900">Pemilik</h2>
                    <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
                        <div>
                            <dt class="font-medium text-slate-500">Nama</dt>
                            <dd class="mt-0.5 text-slate-900">{{ $request->user?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-500">Email</dt>
                            <dd class="mt-0.5 text-slate-900 break-all">{{ $request->user?->email ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            @if ($request->status === 'pending')
                <div class="mt-6 card">
                    <div class="p-6">
                        <h2 class="font-semibold text-slate-900">Tindakan Pemeriksaan</h2>
                        <p class="mt-1 text-sm text-slate-600">
                            Setujui jika data lengkap dan sesuai. Gunakan Perlu Revisi untuk perbaikan kecil, atau Tolak jika produk tidak memenuhi ketentuan.
                        </p>

                        <x-verification-actions
                            :approve-url="route('admin.products.verification.approve', $request)"
                            :reject-url="route('admin.products.verification.reject', $request)"
                            :revision-url="route('admin.products.verification.needs-revision', $request)"
                            subject="produk ini"
                        />
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
