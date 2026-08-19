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

<div class="rounded-xl border border-slate-200 p-4">
    <div class="flex items-baseline justify-between gap-3">
        <h3 class="text-sm font-semibold text-slate-900">{{ $title }}</h3>
        @if ($optional)
            <span class="shrink-0 text-xs font-medium text-slate-400">Opsional</span>
        @endif
    </div>
    <p class="mt-1 text-sm text-slate-600">{{ $description }}</p>

    @if ($current)
        <div class="mt-3">
            <img src="{{ Storage::disk($current->disk)->url($current->path) }}" alt="{{ $itemLabel }}" class="{{ $previewClass }} rounded-lg border border-slate-200 object-cover">
            @if ($deleteUrl)
                <form method="POST" action="{{ $deleteUrl }}" class="mt-2" onsubmit="event.preventDefault(); confirmAction(this, 'Hapus {{ $itemLabel }}?', '{{ $itemLabel }} akan dihapus permanen.', 'danger', 'Hapus', 'Batal');">
                    @csrf
                    @method('DELETE')
                    <x-danger-button class="w-full justify-center sm:w-auto">{{ __('Hapus') }}</x-danger-button>
                </form>
            @endif
        </div>
    @else
        <p class="mt-3 text-sm text-slate-500">Belum ada {{ $itemLabel }}.</p>
    @endif

    <form method="POST" action="{{ $storeUrl }}" enctype="multipart/form-data" class="mt-4">
        @csrf
        <label for="{{ $inputName }}" class="block text-sm font-medium text-slate-700">
            {{ $current ? __('Pilih Gambar Baru') : __('Pilih Gambar') }}
        </label>
        <input id="{{ $inputName }}" type="file" name="{{ $inputName }}" accept="{{ $accept }}" class="mt-1 block w-full text-sm text-slate-700 file:me-3 file:min-h-11 file:rounded-xl file:border-0 file:bg-emerald-600 file:px-4 file:text-sm file:font-semibold file:text-white hover:file:bg-emerald-500">
        <x-input-error :messages="$errors->get($inputName)" class="mt-2" />
        <x-primary-button class="mt-3">{{ __('Unggah') }} {{ $title }}</x-primary-button>
    </form>
</div>