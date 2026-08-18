<x-app-layout :title="$category->name . ' — UMKM'">
    <div class="py-8 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- ============ BREADCRUMB ============ --}}
            <a href="{{ route('public.umkm.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-[#C26A4A] hover:text-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                Semua UMKM
            </a>

            {{-- ============ CATEGORY HEADER ============ --}}
            <div class="mt-4 border-b border-[#E8D8C8] pb-6">
                <h1 class="text-2xl font-semibold tracking-tight text-[#3F2A22] sm:text-3xl">UMKM Kategori {{ $category->name }}</h1>
                @if ($category->description)
                    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[#6F5D50]">{{ $category->description }}</p>
                @endif
            </div>

            {{-- ============ CATEGORY NAVIGATION ============ --}}
            @php $categories = \App\Models\Category::where('type', 'umkm')->orderBy('name')->get(); @endphp
            @if ($categories->isNotEmpty())
                <div class="mt-5 flex items-center gap-2 overflow-x-auto pb-1">
                    @foreach ($categories as $cat)
                        <a href="{{ route('public.category.umkm', $cat) }}" @class([
                            'inline-flex min-h-[40px] shrink-0 items-center rounded-lg px-3.5 text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2',
                            'bg-[#3F2A22] text-white' => $cat->id === $category->id,
                            'border border-[#E8D8C8] bg-white text-[#5C4033] hover:border-[#C26A4A] hover:text-[#C26A4A]' => $cat->id !== $category->id,
                        ])>
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- ============ RESULT TOOLBAR ============ --}}
            <div class="mt-5 flex items-center justify-between gap-4">
                <p class="text-sm text-[#8A7464]">
                    @if ($umkms->count() === 0)
                        Belum ada UMKM
                    @elseif ($umkms->count() === 1)
                        1 UMKM ditemukan
                    @else
                        {{ $umkms->count() }} UMKM ditemukan
                    @endif
                    dalam kategori ini
                </p>
            </div>

            {{-- ============ UMKM LIST ============ --}}
            @if ($umkms->isNotEmpty())
                <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2">
                    @foreach ($umkms as $umkm)
                        <x-umkm-card :umkm="$umkm" variant="warm" />
                    @endforeach
                </div>
            @else
                <div class="mt-6 rounded-2xl bg-[#FAF6F5] px-6 py-10 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-[#E8D8C8]">
                        <svg class="h-6 w-6 text-[#5C4033]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                            <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                            <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                            <path d="M2 7h20" />
                        </svg>
                    </div>
                    <h2 class="mt-4 text-lg font-semibold tracking-tight text-[#3F2A22]">Belum ada UMKM dalam kategori ini</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-[#6F5D50]">Saat ini belum ada UMKM yang terdaftar dalam kategori {{ $category->name }}. Silakan kembali lagi nanti.</p>
                    <a href="{{ route('public.umkm.index') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-[#5C4033] px-5 text-sm font-semibold text-white transition duration-300 hover:bg-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        Lihat Semua UMKM
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>