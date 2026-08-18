<x-app-layout title="Kelola Produk">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-3">
            <div class="min-w-0">
                <h1 class="text-2xl font-semibold tracking-tight text-[#3F2A22] sm:text-3xl">
                    {{ __('Kelola Produk') }}
                </h1>
                <p class="mt-1 text-sm text-[#8A7464] sm:text-base">
                    Kelola produk {{ $umkm->name }}. Produk perlu disetujui Administrator sebelum ditampilkan kepada publik.
                </p>
            </div>
            <a href="{{ route('owner.products.create', $umkm) }}" class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 rounded-xl bg-[#5C4033] px-5 text-sm font-semibold text-white transition-colors duration-150 hover:bg-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14" />
                    <path d="M12 5v14" />
                </svg>
                {{ __('Tambah Produk') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="container-page">
            @if (session('status'))
                <x-alert type="success" class="mb-6">{{ session('status') }}</x-alert>
            @endif

            @if (session('error'))
                <x-alert type="error" class="mb-6">{{ session('error') }}</x-alert>
            @endif

            <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                <span class="text-sm text-[#8A7464]">Produk untuk</span>
                <span class="min-w-0 break-words text-base font-medium text-[#3F2A22]">{{ $umkm->name }}</span>
                <span class="text-[#8A7464]/40" aria-hidden="true">&middot;</span>
                <span class="text-sm text-[#8A7464]">{{ $products->count() }} produk</span>
            </div>

            <section aria-label="Daftar produk" class="mt-4 overflow-hidden rounded-2xl bg-white shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-[#ECE5D9]">
                @if ($products->isEmpty())
                    <div class="flex flex-col items-center px-5 py-12 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#F4EDE1] text-[#5C4033]">
                            <svg class="h-6 w-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                                <path d="m3.3 7 8.7 5 8.7-5" />
                                <path d="M12 22V12" />
                            </svg>
                        </div>
                        <h2 class="mt-4 text-lg font-semibold tracking-tight text-[#3F2A22]">Belum ada produk</h2>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-[#6F5D50]">
                            Tambahkan produk pertama Anda untuk mulai menampilkan produk di Portal UMKM.
                        </p>
                        <a href="{{ route('owner.products.create', $umkm) }}" class="mt-6 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#5C4033] px-5 text-sm font-semibold text-white transition-colors duration-150 hover:bg-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14" />
                                <path d="M12 5v14" />
                            </svg>
                            {{ __('Tambah Produk Pertama') }}
                        </a>
                    </div>
                @else
                    <ul class="divide-y divide-[#ECE5D9]">
                        @foreach ($products as $product)
                            @php
                                $latestNote = $product->verificationRequests()
                                    ->whereIn('status', ['needs_revision', 'rejected'])
                                    ->latest('id')
                                    ->value('notes');
                                $photo = $product->media->first();
                                $revision = $product->revisions
                                    ->whereIn('status', ['draft', 'pending', 'needs_revision', 'rejected'])
                                    ->sortByDesc('id')
                                    ->first();
                            @endphp
                            <li class="px-5 py-5 sm:px-6">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="flex min-w-0 items-start gap-4">
                                        @if ($photo)
                                            <img src="{{ Storage::disk($photo->disk)->url($photo->path) }}" alt="{{ $product->name }}" class="h-14 w-14 shrink-0 rounded-xl border border-[#ECE5D9] object-cover">
                                        @else
                                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl border border-[#ECE5D9] bg-[#FAF6F5] text-[#8A7464]" aria-hidden="true">
                                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                                                    <path d="m3.3 7 8.7 5 8.7-5" />
                                                    <path d="M12 22V12" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <a href="{{ route('owner.products.show', $product) }}" class="break-words text-base font-semibold leading-snug tracking-tight text-[#3F2A22] hover:underline focus:outline-none focus-visible:underline">
                                                    {{ $product->name }}
                                                </a>
                                                <x-badge :status="$product->status" />
                                            </div>
                                            <p class="mt-1 text-sm font-medium text-[#6F5D50]">
                                                Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                                            </p>
                                            @if (in_array($product->status, ['needs_revision', 'rejected'], true))
                                                <p class="mt-1 break-words text-sm leading-relaxed text-[#6F5D50]">
                                                    {{ $product->status === 'rejected' ? 'Alasan Penolakan' : 'Catatan Administrator' }}: {{ $latestNote ?? '—' }}
                                                </p>
                                                <p class="mt-1 text-sm leading-relaxed text-[#8A7464]">
                                                    Perbaiki sesuai catatan, lalu kirim pengajuan kembali.
                                                </p>
                                            @elseif ($product->status === 'draft')
                                                <p class="mt-1 text-sm leading-relaxed text-[#8A7464]">
                                                    Belum diajukan ke Administrator. Kirim pengajuan agar produk diperiksa.
                                                </p>
                                            @elseif ($product->status === 'pending')
                                                <p class="mt-1 text-sm leading-relaxed text-[#8A7464]">
                                                    Sedang diperiksa Administrator.
                                                </p>
                                            @elseif ($product->status === 'approved' && $revision !== null)
                                                <p class="mt-1 text-sm leading-relaxed text-[#8A7464]">
                                                    Perubahan produk sedang menunggu tindakan Anda atau Administrator.
                                                </p>
                                            @else
                                                <p class="mt-1 text-sm leading-relaxed text-[#8A7464]">
                                                    Produk tampil di halaman publik.
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 sm:shrink-0">
                                        @if ($product->status === 'approved' && $revision !== null)
                                            <x-badge :status="$revision->status" />
                                        @endif
                                        @if (in_array($product->status, ['draft', 'needs_revision', 'rejected'], true))
                                            <a href="{{ route('owner.products.edit', $product) }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl px-5 text-sm font-semibold transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 {{ in_array($product->status, ['needs_revision', 'rejected'], true) ? 'bg-[#C26A4A] text-white hover:bg-[#A3523A]' : 'border border-[#ECE5D9] bg-white text-[#5C4033] hover:bg-[#F4EDE1]' }}">
                                                {{ in_array($product->status, ['needs_revision', 'rejected'], true) ? __('Perbaiki Produk') : __('Periksa') }}
                                            </a>
                                        @elseif ($product->status === 'approved' && $revision !== null && in_array($revision->status, ['draft', 'needs_revision', 'rejected'], true))
                                            <a href="{{ route('owner.products.revisions.edit', $revision) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#ECE5D9] bg-white px-5 text-sm font-semibold text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                                {{ in_array($revision->status, ['needs_revision', 'rejected'], true) ? __('Perbaiki Perubahan') : __('Kelola Perubahan') }}
                                            </a>
                                        @elseif ($product->status === 'approved' && $revision === null)
                                            <a href="{{ route('owner.products.revisions.create', $product) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#ECE5D9] bg-white px-5 text-sm font-semibold text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                                {{ __('Ajukan Perubahan') }}
                                            </a>
                                        @endif
                                        @if ($product->status === 'draft')
                                            <form method="POST" action="{{ route('owner.products.submit', $product) }}" onsubmit="return confirm('Yakin ingin mengirim pengajuan produk ini?');">
                                                @csrf
                                                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#5C4033] px-5 text-sm font-semibold text-white transition-colors duration-150 hover:bg-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                                    {{ __('Kirim Pengajuan') }}
                                                </button>
                                            </form>
                                        @elseif ($product->status === 'approved' && $revision !== null && $revision->status === 'draft')
                                            <form method="POST" action="{{ route('owner.products.revisions.submit', $revision) }}" onsubmit="return confirm('Yakin ingin mengirim pengajuan perubahan produk ini?');">
                                                @csrf
                                                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#5C4033] px-5 text-sm font-semibold text-white transition-colors duration-150 hover:bg-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                                    {{ __('Kirim Perubahan') }}
                                                </button>
                                            </form>
                                        @elseif ($product->status === 'approved' && $revision === null)
                                            <a href="{{ route('public.product.show', $product) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#ECE5D9] bg-white px-5 text-sm font-semibold text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                                {{ __('Lihat di Portal') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
