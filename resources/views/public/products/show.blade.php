<x-app-layout :title="$product->name">
    @php
        $photo = $product->media->first();
        $whatsapp = \App\Support\WhatsApp::waNumber($product->umkm->phone);
    @endphp

    <div class="py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="{{ route('public.product.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-600 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                Kembali ke daftar Produk
            </a>

            <div class="mt-8 grid gap-8 lg:grid-cols-2">
                <div class="min-w-0">
                    @if ($photo)
                        <img src="{{ Storage::disk($photo->disk)->url($photo->path) }}" alt="Foto {{ $product->name }}" class="aspect-[4/3] w-full rounded-2xl object-cover">
                    @else
                        <div class="flex aspect-[4/3] w-full items-center justify-center rounded-2xl bg-slate-50 text-2xl font-medium text-slate-400">
                            {{ mb_strtoupper(mb_substr($product->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="min-w-0">
                    <h1 class="break-words text-2xl font-semibold leading-tight tracking-tight text-slate-900 sm:text-3xl">{{ $product->name }}</h1>
                    <p class="mt-3 text-2xl font-semibold text-emerald-700">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>

                    @if ($product->category)
                        <p class="mt-2 text-sm font-medium text-emerald-600">{{ $product->category->name }}</p>
                    @endif

                    @if ($product->description)
                        <p class="mt-6 max-w-2xl break-words whitespace-pre-line leading-relaxed text-slate-600">{{ $product->description }}</p>
                    @endif

                    <div class="mt-8 rounded-2xl bg-white p-6 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Dijual oleh</p>
                        <p class="mt-1.5 break-words font-medium text-slate-900">{{ $product->umkm->name }}</p>
                        @if ($product->umkm->address)
                            <p class="mt-1 break-words text-sm leading-relaxed text-slate-600">{{ $product->umkm->address }}</p>
                        @endif
                        <a href="{{ route('public.umkm.show', $product->umkm) }}" class="-mx-2 mt-4 inline-flex min-h-11 items-center gap-1.5 rounded-lg px-2 text-sm font-medium text-emerald-600 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                            Lihat Profil UMKM
                            <svg class="h-4 w-4 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </a>
                    </div>

                    @if ($whatsapp !== '')
                        <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" class="mt-4 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 text-sm font-semibold text-white transition duration-300 hover:bg-emerald-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                            <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" />
                            </svg>
                            Hubungi via WhatsApp
                        </a>
                    @endif
                </div>
            </div>

            @if ($similarProducts->isNotEmpty())
                @php
                    $cols = match ($similarProducts->count()) {
                        1 => 'max-w-md mx-auto',
                        2 => 'sm:grid-cols-2',
                        3 => 'sm:grid-cols-2 lg:grid-cols-3',
                        default => 'sm:grid-cols-2 lg:grid-cols-4',
                    };
                @endphp
                <section class="mt-16">
                    <h2 class="text-xl font-semibold tracking-tight text-slate-900">Produk Serupa</h2>
                    <div class="mt-4 grid grid-cols-1 gap-6 {{ $cols }}">
                        @foreach ($similarProducts as $similar)
                            <x-product-card :product="$similar" />
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
