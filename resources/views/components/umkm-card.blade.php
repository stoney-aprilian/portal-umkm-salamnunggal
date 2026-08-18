@props(['umkm', 'variant' => null])

@php
    $logo = $umkm->media->first();
    
    // Auto-determine variant based on route for public pages
    $isPublicPage = request()->routeIs('public.umkm.*') || request()->routeIs('home') || request()->routeIs('public.category.*');
    $variant = $variant ?? ($isPublicPage ? 'warm' : null);
    
    $warm = $variant === 'warm';
@endphp

<a href="{{ route('public.umkm.show', $umkm) }}" @class([
    'group block',
    'flex flex-col rounded-2xl bg-white p-6 transition-shadow duration-300' => !$warm && $variant !== 'list',
    'border border-[#ECE5D9] shadow-[0_2px_12px_rgba(63,42,34,0.06)] hover:shadow-[0_16px_32px_-18px_rgba(63,42,34,0.35)]' => $warm && $variant !== 'list',
    'flex items-center gap-5 rounded-2xl border border-[#ECE5D9] bg-white p-4 shadow-[0_2px_12px_rgba(63,42,34,0.06)] transition-shadow duration-300 hover:shadow-[0_16px_32px_-18px_rgba(63,42,34,0.35)] group' => $variant === 'list',
])>
    @if ($variant === 'list')
        @php $logo = $umkm->media->first(); @endphp
        <div class="flex h-32 w-40 shrink-0 overflow-hidden rounded-xl bg-[#F4EDE1] sm:h-36 sm:w-48">
            @if ($logo)
                <img src="{{ Storage::disk($logo->disk)->url($logo->path) }}" alt="Logo {{ $umkm->name }}" class="h-full w-full object-cover">
            @else
                <div class="flex h-full w-full items-center justify-center text-2xl font-semibold text-[#5C4033]">
                    {{ mb_strtoupper(mb_substr($umkm->name, 0, 1)) }}
                </div>
            @endif
        </div>
        <div class="flex min-w-0 flex-1 flex-col justify-between py-1">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h3 class="line-clamp-2 text-base font-semibold leading-snug text-[#3F2A22]">{{ $umkm->name }}</h3>
                    @if ($umkm->status === 'approved')
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">
                            <svg class="h-3 w-3" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 12l2 2 4-4" />
                                <circle cx="12" cy="12" r="10" />
                            </svg>
                            Terverifikasi
                        </span>
                    @endif
                </div>
                @if ($umkm->category)
                    <p class="mt-1 text-sm font-medium text-[#C26A4A]">{{ $umkm->category->name }}</p>
                @endif
                @if ($umkm->description)
                    <p class="mt-1.5 line-clamp-2 text-sm leading-relaxed text-[#6F5D50]">{{ $umkm->description }}</p>
                @endif
            </div>
            <div class="mt-3 flex items-center justify-between gap-3">
                @if ($umkm->address)
                    <p class="flex min-w-0 items-center gap-1.5 text-sm text-[#8A7464]">
                        <svg class="h-4 w-4 shrink-0 text-[#A99A8C]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0Z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        <span class="truncate">{{ $umkm->address }}</span>
                    </p>
                @endif
                <span class="inline-flex shrink-0 items-center gap-1 text-sm font-semibold text-[#C26A4A] transition-colors duration-150 group-hover:text-[#5C4033]">
                    Lihat Detail
                    <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m9 18 6-6-6-6" />
                    </svg>
                </span>
            </div>
        </div>
    @else
        <div class="flex items-center gap-4">
            @if ($logo)
                <img src="{{ Storage::disk($logo->disk)->url($logo->path) }}" alt="Logo {{ $umkm->name }}" class="h-14 w-14 shrink-0 rounded-xl object-cover">
            @else
                <div @class([
                    'flex h-14 w-14 shrink-0 items-center justify-center rounded-xl text-xl font-semibold',
                    'bg-[#F4EDE1] text-[#5C4033]' => $warm,
                    'bg-emerald-50 text-emerald-700' => !$warm,
                ])>
                    {{ mb_strtoupper(mb_substr($umkm->name, 0, 1)) }}
                </div>
            @endif
            <div class="min-w-0">
                <h3 @class([
                    'line-clamp-2 text-lg font-semibold leading-snug',
                    'text-[#3F2A22] transition duration-300 group-hover:text-[#3F2A22]' => $warm,
                    'text-slate-900 transition duration-300 group-hover:text-emerald-700' => !$warm,
                ])>{{ $umkm->name }}</h3>
                @if ($umkm->category)
                    <p @class([
                        'mt-0.5 text-sm font-medium',
                        'text-[#C26A4A]' => $warm,
                        'text-emerald-600' => !$warm,
                    ])>{{ $umkm->category->name }}</p>
                @endif
            </div>
        </div>

        @if ($umkm->description)
            <p @class([
                'mt-4 line-clamp-2 text-sm leading-relaxed',
                'text-[#6F5D50]' => $warm,
                'text-slate-600' => !$warm,
            ])>{{ $umkm->description }}</p>
        @endif

        <div @class([
            'mt-4 flex items-center gap-3 pt-4',
            'border-t border-[#ECE5D9]' => $warm,
            'border-t border-slate-100' => !$warm,
        ])>
            @if ($umkm->address)
                <p class="flex min-w-0 flex-1 items-center gap-1.5 text-sm">
                    <svg @class([
                        'h-4 w-4 shrink-0',
                        'text-[#8A7464]' => $warm,
                        'text-slate-400' => !$warm,
                    ]) aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0Z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                    <span @class([
                        'line-clamp-2 min-w-0',
                        'text-[#8A7464]' => $warm,
                        'text-slate-500' => !$warm,
                    ])>{{ $umkm->address }}</span>
                </p>
            @endif
            <span @class([
                'ml-auto inline-flex min-h-11 shrink-0 items-center gap-1.5 rounded-xl px-4 text-sm font-semibold text-white transition duration-300',
                'bg-[#3F2A22] group-hover:bg-[#5C4033]' => $warm,
                'bg-emerald-600 group-hover:bg-emerald-500' => !$warm,
            ])>
                Lihat Detail
                <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m9 18 6-6-6-6" />
                </svg>
            </span>
        </div>
    @endif
</a>
