<x-app-layout :title="$product->name">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-3">
            <div class="min-w-0">
                <h1 class="text-2xl font-semibold tracking-tight text-[#3F2A22] sm:text-3xl">
                    {{ $product->name }}
                </h1>
                <p class="mt-1 text-sm text-[#8A7464] sm:text-base">
                    Detail produk
                </p>
            </div>
            <x-badge :status="$product->status" />
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="container-page">
            <a href="{{ route('owner.products.index', $product->umkm) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-[#C26A4A] hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                Kembali ke Daftar Produk
            </a>

            @if (session('status'))
                <x-alert type="success" class="mt-6">{{ session('status') }}</x-alert>
            @endif

            @if (session('error'))
                <x-alert type="error" class="mt-6">{{ session('error') }}</x-alert>
            @endif

            @php
                $latestNote = $product->verificationRequests()
                    ->whereIn('status', ['needs_revision', 'rejected'])
                    ->latest('id')
                    ->value('notes');
                $photo = $product->media->first();
            @endphp

            <div class="mt-6 overflow-hidden rounded-2xl bg-white shadow-[0_2px_12px_rgba(63,42,34,0.06)] ring-1 ring-[#ECE5D9]">
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="break-words text-xl font-semibold tracking-tight text-[#3F2A22]">{{ $product->name }}</h2>
                                <x-badge :status="$product->status" />
                            </div>
                            <p class="mt-2 text-lg font-semibold text-[#C26A4A]">
                                Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                            </p>
                            @if ($product->category)
                                <p class="mt-1 text-sm font-medium text-[#8A7464]">{{ $product->category->name }}</p>
                            @endif
                        </div>

                        @if ($photo)
                            <img src="{{ Storage::disk($photo->disk)->url($photo->path) }}" alt="Foto {{ $product->name }}" class="aspect-[4/3] w-full max-w-sm shrink-0 rounded-xl border border-[#ECE5D9] object-cover">
                        @else
                            <div class="flex aspect-[4/3] w-full max-w-sm shrink-0 items-center justify-center rounded-xl border border-[#ECE5D9] bg-[#FAF6F5] text-3xl font-semibold text-[#8A7464]">
                                {{ mb_strtoupper(mb_substr($product->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    @if ($product->description)
                        <p class="mt-6 break-words whitespace-pre-line leading-relaxed text-[#6F5D50]">{{ $product->description }}</p>
                    @endif

                    @if ($product->status === 'rejected')
                        <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                            <p class="text-sm text-red-800">
                                Alasan Penolakan: {{ $latestNote ?? '—' }}
                            </p>
                        </div>
                    @elseif ($product->status === 'needs_revision')
                        <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                            <p class="text-sm text-amber-800">
                                Catatan Administrator: {{ $latestNote ?? '—' }}
                            </p>
                        </div>
                    @elseif ($product->status === 'draft')
                        <p class="mt-6 text-sm leading-relaxed text-[#8A7464]">Belum diajukan ke Administrator. Kirim pengajuan agar produk diperiksa.</p>
                    @elseif ($product->status === 'pending')
                        <p class="mt-6 text-sm leading-relaxed text-[#8A7464]">Sedang diperiksa Administrator.</p>
                    @else
                        <p class="mt-6 text-sm leading-relaxed text-[#8A7464]">Produk tampil di halaman publik.</p>
                    @endif

                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        @if (in_array($product->status, ['draft', 'needs_revision', 'rejected'], true))
                            <a href="{{ route('owner.products.edit', $product) }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl px-5 text-sm font-semibold transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 {{ in_array($product->status, ['needs_revision', 'rejected'], true) ? 'bg-[#C26A4A] text-white hover:bg-[#A3523A]' : 'border border-[#ECE5D9] bg-white text-[#5C4033] hover:bg-[#F4EDE1]' }}">
                                {{ in_array($product->status, ['needs_revision', 'rejected'], true) ? __('Perbaiki Produk') : __('Edit Produk') }}
                            </a>
                        @endif

                        @if ($product->status === 'draft')
                            <form method="POST" action="{{ route('owner.products.submit', $product) }}" onsubmit="event.preventDefault(); confirmAction(this, 'Kirim Pengajuan Produk?', 'Produk {{ $product->name }} akan dikirim untuk diperiksa Administrator.', 'success', 'Kirim Pengajuan', 'Batal');">
                                @csrf
                                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#5C4033] px-5 text-sm font-semibold text-white transition-colors duration-150 hover:bg-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                    {{ __('Kirim Pengajuan') }}
                                </button>
                            </form>
                        @elseif ($product->status === 'approved')
                            <a href="{{ route('public.product.show', $product) }}" target="_blank" rel="noopener" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#ECE5D9] bg-white px-5 text-sm font-semibold text-[#5C4033] transition-colors duration-150 hover:bg-[#F4EDE1] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                {{ __('Lihat di Portal') }}
                            </a>
                        @endif

                        @if (in_array($product->status, ['draft', 'needs_revision', 'rejected'], true))
                            <form method="POST" action="{{ route('owner.products.destroy', $product) }}" onsubmit="event.preventDefault(); confirmAction(this, 'Hapus Produk?', 'Produk {{ $product->name }} beserta foto dan pengajuan terkait akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.', 'danger', 'Hapus Produk', 'Batal');">
                                @csrf
                                @method('DELETE')
                                <x-danger-button>{{ __('Hapus Produk') }}</x-danger-button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
