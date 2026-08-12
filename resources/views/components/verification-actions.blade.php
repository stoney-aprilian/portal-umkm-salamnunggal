@props([
    'approveUrl' => null,
    'rejectUrl' => null,
    'revisionUrl' => null,
    'subject' => 'pengajuan ini',
])

@php
    $rejectHasErrors = $errors->reject->any();
    $revisionHasErrors = $errors->revision->any();
@endphp

<div class="mt-4">
    @if ($approveUrl)
        <form method="POST" action="{{ $approveUrl }}" onsubmit="return confirm('Yakin ingin menyetujui {{ $subject }}?');">
            @csrf
            <x-primary-button class="w-full justify-center">{{ __('Setujui') }}</x-primary-button>
        </form>
    @endif

    @if ($rejectUrl || $revisionUrl)
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
            @if ($rejectUrl)
                <form method="POST" action="{{ $rejectUrl }}" onsubmit="return confirm('Yakin ingin menolak {{ $subject }}?');">
                    @csrf
                    <label for="reject-notes" class="block text-sm font-medium text-slate-700">
                        {{ __('Alasan Penolakan') }}
                    </label>
                    <x-textarea id="reject-notes" name="notes" rows="3" class="mt-1 block w-full" required>{{ $rejectHasErrors ? old('notes') : '' }}</x-textarea>
                    <p class="mt-1 text-xs text-slate-500">
                        Alasan ini akan ditampilkan kepada pemilik pengajuan.
                    </p>
                    @if ($rejectHasErrors)
                        <x-input-error :messages="$errors->reject->get('notes')" class="mt-2" />
                    @endif
                    <x-danger-button class="mt-3 w-full justify-center">{{ __('Tolak') }}</x-danger-button>
                </form>
            @endif

            @if ($revisionUrl)
                <form method="POST" action="{{ $revisionUrl }}" onsubmit="return confirm('Yakin ingin meminta revisi {{ $subject }}?');">
                    @csrf
                    <label for="revision-notes" class="block text-sm font-medium text-slate-700">
                        {{ __('Catatan Revisi') }}
                    </label>
                    <x-textarea id="revision-notes" name="notes" rows="3" class="mt-1 block w-full" required>{{ $revisionHasErrors ? old('notes') : '' }}</x-textarea>
                    <p class="mt-1 text-xs text-slate-500">
                        Catatan ini akan ditampilkan kepada pemilik untuk perbaikan.
                    </p>
                    @if ($revisionHasErrors)
                        <x-input-error :messages="$errors->revision->get('notes')" class="mt-2" />
                    @endif
                    <button type="submit" class="mt-3 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white transition duration-300 hover:bg-amber-400 active:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 disabled:opacity-25">
                        {{ __('Perlu Revisi') }}
                    </button>
                </form>
            @endif
        </div>
    @endif
</div>
