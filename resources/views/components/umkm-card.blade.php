@props(['umkm'])

<a href="{{ route('public.umkm.show', $umkm) }}" class="group flex flex-col rounded-2xl bg-white p-6 shadow-sm transition-shadow duration-300 hover:shadow-lg">
    <div class="flex items-center gap-4">
        @php $logo = $umkm->media->first(); @endphp
        @if ($logo)
            <img src="{{ Storage::disk($logo->disk)->url($logo->path) }}" alt="Logo {{ $umkm->name }}" class="h-14 w-14 shrink-0 rounded-xl object-cover">
        @else
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-xl font-semibold text-emerald-700">
                {{ mb_strtoupper(mb_substr($umkm->name, 0, 1)) }}
            </div>
        @endif
        <div class="min-w-0">
            <h3 class="line-clamp-2 text-lg font-semibold leading-snug text-slate-900 transition duration-300 group-hover:text-emerald-700">{{ $umkm->name }}</h3>
            @if ($umkm->category)
                <p class="mt-0.5 text-sm font-medium text-emerald-600">{{ $umkm->category->name }}</p>
            @endif
        </div>
    </div>

    @if ($umkm->description)
        <p class="mt-4 line-clamp-2 text-sm leading-relaxed text-slate-600">{{ $umkm->description }}</p>
    @endif

    <div class="mt-4 flex items-center gap-3 border-t border-slate-100 pt-4">
        @if ($umkm->address)
            <p class="flex min-w-0 flex-1 items-center gap-1.5 text-sm text-slate-500">
                <svg class="h-4 w-4 shrink-0 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0Z" />
                    <circle cx="12" cy="10" r="3" />
                </svg>
                <span class="line-clamp-2 min-w-0">{{ $umkm->address }}</span>
            </p>
        @endif
        <span class="ml-auto inline-flex min-h-11 shrink-0 items-center gap-1.5 rounded-xl bg-emerald-600 px-4 text-sm font-semibold text-white transition duration-300 group-hover:bg-emerald-500">
            Lihat Detail
            <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m9 18 6-6-6-6" />
            </svg>
        </span>
    </div>
</a>
