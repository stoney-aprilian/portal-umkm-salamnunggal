@props(['status'])

@php
$labels = [
    'draft' => 'Draft',
    'pending' => 'Menunggu Pemeriksaan',
    'approved' => 'Disetujui',
    'needs_revision' => 'Perlu Revisi',
    'rejected' => 'Ditolak',
];
$styles = [
    'draft' => 'bg-[#F4EDE1] text-[#5C4033]',
    'pending' => 'bg-amber-100 text-amber-800',
    'approved' => 'bg-emerald-100 text-emerald-800',
    'needs_revision' => 'bg-orange-100 text-orange-800',
    'rejected' => 'bg-red-100 text-red-700',
];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ' . ($styles[$status] ?? 'bg-slate-100 text-slate-700')]) }}>
    {{ $labels[$status] ?? $status }}
</span>
