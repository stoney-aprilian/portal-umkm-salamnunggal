@props(['product', 'refined' => false])

@php $photo = $product->media->first(); @endphp

<div @class([
    'group relative flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm transition-shadow duration-300 hover:shadow-lg',
    'focus-within:ring-2 focus-within:ring-emerald-500 focus-within:ring-offset-2' => $refined,
])>
    @if ($photo)
        <img src="{{ Storage::disk($photo->disk)->url($photo->path) }}" alt="Foto {{ $product->name }}" class="aspect-[4/3] w-full object-cover">
    @elseif ($refined)
        <div class="flex aspect-[4/3] w-full items-center justify-center bg-slate-50 text-2xl font-medium text-slate-400">
            {{ mb_strtoupper(mb_substr($product->name, 0, 1)) }}
        </div>
    @else
        <div class="flex aspect-[4/3] w-full items-center justify-center bg-emerald-50 text-3xl font-semibold text-emerald-700">
            {{ mb_strtoupper(mb_substr($product->name, 0, 1)) }}
        </div>
    @endif

    <div class="flex flex-1 flex-col p-5">
        <h3 @class([
            'text-lg font-semibold text-slate-900',
            'truncate' => !$refined,
            'line-clamp-2 leading-snug' => $refined,
        ])>
            <a href="{{ route('public.product.show', $product) }}" @class(['after:absolute after:inset-0', 'focus:outline-none' => $refined])>{{ $product->name }}</a>
        </h3>
        <p class="mt-1 font-semibold text-emerald-700">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>

        <div class="relative mt-2 flex flex-wrap items-center gap-2 text-sm">
            <a href="{{ route('public.umkm.show', $product->umkm) }}" class="font-medium text-emerald-600 hover:underline focus:outline-none focus-visible:underline">{{ $product->umkm->name }}</a>
            @if ($product->category)
                <span class="text-slate-500">&middot;</span>
                <span class="text-slate-500">{{ $product->category->name }}</span>
            @endif
        </div>

        @if ($product->description)
            <p @class(['mt-2 line-clamp-2 text-sm text-slate-600', 'leading-relaxed' => $refined])>{{ $product->description }}</p>
        @endif

        @if ($refined)
            <p class="mt-auto flex items-center gap-1 pt-4 text-sm font-medium text-emerald-600">
                Lihat Detail
                <svg class="h-4 w-4 shrink-0 transition-transform duration-300 group-hover:translate-x-0.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m9 18 6-6-6-6" />
                </svg>
            </p>
        @else
            <p class="mt-4 text-sm font-medium text-emerald-600 group-hover:underline">Lihat Detail</p>
        @endif
    </div>
</div>
