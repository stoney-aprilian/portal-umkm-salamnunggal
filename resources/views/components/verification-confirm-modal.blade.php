@props([
    'type' => 'approve',
    'subject' => 'pengajuan ini',
])

@php
$config = match ($type) {
    'approve' => [
        'title' => 'Setujui ' . $subject . '?',
        'message' => $subject . ' akan disetujui dan dapat tampil di portal publik.',
        'confirmText' => 'Setujui',
        'confirmClass' => 'bg-emerald-600 hover:bg-emerald-500 focus:ring-emerald-500 text-white',
    ],
    'reject' => [
        'title' => 'Tolak ' . $subject . '?',
        'message' => $subject . ' akan ditolak dan tidak akan tampil sebagai approved di portal publik.',
        'confirmText' => 'Tolak',
        'confirmClass' => 'bg-red-600 hover:bg-red-500 focus:ring-red-500 text-white',
    ],
    'revision' => [
        'title' => 'Minta Perbaikan?',
        'message' => $subject . ' akan dikembalikan kepada Owner untuk diperbaiki.',
        'confirmText' => 'Minta Perbaikan',
        'confirmClass' => 'bg-amber-500 hover:bg-amber-400 focus:ring-amber-500 text-white',
    ],
};
@endphp

<x-modal name="verification-confirm" maxWidth="md">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-slate-900">{{ $config['title'] }}</h3>
        <p class="mt-2 text-sm text-slate-600">{{ $config['message'] }}</p>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button" @click="$dispatch('close-modal', 'verification-confirm')" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                Batal
            </button>
            <button type="button" @click="$dispatch('confirm-verification')" class="inline-flex min-h-11 items-center justify-center rounded-xl px-5 text-sm font-semibold text-white transition duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 {{ $config['confirmClass'] }}">
                {{ $config['confirmText'] }}
            </button>
        </div>
    </div>
</x-modal>
