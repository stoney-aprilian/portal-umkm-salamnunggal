@props([
    'title' => '',
    'description' => '',
    'current' => null,
    'storeUrl' => '',
    'inputName' => 'file',
    'accept' => 'image/jpeg,image/png,image/webp',
    'deleteUrl' => null,
    'itemLabel' => 'media',
    'optional' => false,
    'previewClass' => 'max-h-48',
])

<div class="rounded-xl border border-[#ECE5D9] bg-white p-4">
    <div class="flex items-baseline justify-between gap-3">
        <h3 class="text-sm font-semibold text-[#3F2A22]">{{ $title }}</h3>
        @if ($optional)
            <span class="shrink-0 text-xs font-medium text-[#8A7464]">Opsional</span>
        @endif
    </div>
    <p class="mt-1 text-sm text-[#6F5D50]">{{ $description }}</p>

    @if ($current)
        <div class="mt-3">
            <img src="{{ Storage::disk($current->disk)->url($current->path) }}" alt="{{ $itemLabel }}" class="{{ $previewClass }} rounded-lg border border-[#ECE5D9] object-cover">
            @if ($deleteUrl)
                <form method="POST" action="{{ $deleteUrl }}" class="mt-2" onsubmit="return confirm('Hapus {{ $itemLabel }} ini?');">
                    @csrf
                    @method('DELETE')
                    <x-danger-button class="w-full justify-center sm:w-auto">{{ __('Hapus') }}</x-danger-button>
                </form>
            @endif
        </div>
    @else
        <p class="mt-3 text-sm text-[#8A7464]">Belum ada {{ $itemLabel }}.</p>
    @endif

    <form method="POST" action="{{ $storeUrl }}" enctype="multipart/form-data" class="mt-4">
        @csrf
        <label for="{{ $inputName }}" class="block text-sm font-medium text-[#5C4033]">
            {{ $current ? __('Pilih Gambar Baru') : __('Pilih Gambar') }}
        </label>
        <input id="{{ $inputName }}" type="file" name="{{ $inputName }}" accept="{{ $accept }}" class="mt-1 block w-full text-sm text-[#5C4033] file:me-3 file:min-h-11 file:rounded-xl file:border-0 file:bg-[#5C4033] file:px-4 file:text-sm file:font-semibold file:text-white hover:file:bg-[#3F2A22]">
        <x-input-error :messages="$errors->get($inputName)" class="mt-2" />
        <x-owner-primary-button class="mt-3">{{ __('Unggah') }} {{ $title }}</x-owner-primary-button>
    </form>
</div>