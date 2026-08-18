@props(['product', 'refined' => false, 'variant' => null])

@php
    $photo = $product->media->first();
    
    $isPublicPage = request()->routeIs('public.product.*') || request()->routeIs('home') || request()->routeIs('public.umkm.*') || request()->routeIs('public.category.*') || request()->routeIs('public.search');
    $variant = $variant ?? ($isPublicPage ? 'warm' : null);
    
    $warm = $variant === 'warm';
@endphp

<div @class([
    'group relative flex flex-col overflow-hidden rounded-2xl bg-white transition-shadow duration-300',
    'shadow-sm hover:shadow-lg' => !$warm,
    'border border-[#ECE5D9] shadow-[0_2px_12px_rgba(63,42,34,0.06)] hover:shadow-[0_16px_32px_-18px_rgba(63,42,34,0.35)]' => $warm,
    'focus-within:ring-2 focus-within:ring-offset-2' => $refined,
    'focus-within:ring-[#C26A4A]' => $refined && $warm,
    'focus-within:ring-emerald-500' => $refined && !$warm,
])>
    @if ($photo)
        <img src="{{ Storage::disk($photo->disk)->url($photo->path) }}" alt="Foto {{ $product->name }}" class="aspect-[4/3] w-full object-cover">
    @elseif ($refined)
        <div class="flex aspect-[4/3] w-full items-center justify-center bg-slate-50 text-2xl font-medium text-slate-400">
            {{ mb_strtoupper(mb_substr($product->name, 0, 1)) }}
        </div>
    @elseif ($warm)
        <div class="flex aspect-[4/3] w-full items-center justify-center bg-[#F4EDE1] text-3xl font-semibold text-[#5C4033]">
            {{ mb_strtoupper(mb_substr($product->name, 0, 1)) }}
        </div>
    @else
        <div class="flex aspect-[4/3] w-full items-center justify-center bg-emerald-50 text-3xl font-semibold text-emerald-700">
            {{ mb_strtoupper(mb_substr($product->name, 0, 1)) }}
        </div>
    @endif

    <div class="flex flex-1 flex-col p-5">
        <h3 @class([
            'text-lg font-semibold',
            'text-[#3F2A22]' => $warm,
            'text-slate-900' => !$warm,
            'line-clamp-2 leading-snug' => $refined || $warm,
            'truncate' => !$refined && !$warm,
        ])>
            <a href="{{ route('public.product.show', $product) }}" @class(['after:absolute after:inset-0', 'focus:outline-none' => $refined])>{{ $product->name }}</a>
        </h3>
        <p @class([
            'mt-1 font-semibold',
            'text-[#C26A4A]' => $warm,
            'text-emerald-700' => !$warm,
        ])>Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>

        <div class="relative mt-2 flex flex-wrap items-center gap-2 text-sm">
            <a href="{{ route('public.umkm.show', $product->umkm) }}" @class([
                'font-medium hover:underline focus:outline-none focus-visible:underline',
                'text-[#5C4033] hover:text-[#3F2A22]' => $warm,
                'text-emerald-600' => !$warm,
            ])>{{ $product->umkm->name }}</a>
            @if ($product->category)
                <span @class(['text-[#A99A8C]' => $warm, 'text-slate-500' => !$warm])>&middot;</span>
                <span @class(['text-[#A99A8C]' => $warm, 'text-slate-500' => !$warm])>{{ $product->category->name }}</span>
            @endif
        </div>

        @if ($product->description)
            <p @class([
                'mt-2 line-clamp-2 text-sm',
                'text-[#6F5D50]' => $warm,
                'text-slate-600' => !$warm,
                'leading-relaxed' => $refined,
            ])>{{ $product->description }}</p>
        @endif

        @if ($refined)
            <p @class([
                'mt-auto flex items-center gap-1 pt-4 text-sm font-medium',
                'text-[#C26A4A]' => $warm,
                'text-emerald-600' => !$warm,
            ])>
                Lihat Detail
                <svg class="h-4 w-4 shrink-0 transition-transform duration-300 group-hover:translate-x-0.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m9 18 6-6-6-6" />
                </svg>
            </p>
        @else
            <p @class([
                'mt-4 text-sm font-medium group-hover:underline',
                'text-[#C26A4A]' => $warm,
                'text-emerald-600' => !$warm,
            ])>Lihat Detail</p>
        @endif
    </div>
</div>
