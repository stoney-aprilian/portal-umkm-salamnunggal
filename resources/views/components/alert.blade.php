@props(['type' => 'success'])

@php
$styles = [
    'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
    'error' => 'border-red-200 bg-red-50 text-red-700',
    'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
    'info' => 'border-sky-200 bg-sky-50 text-sky-800',
];
$role = in_array($type, ['error', 'warning'], true) ? 'alert' : 'status';
@endphp

<div {{ $attributes->merge(['class' => 'rounded-xl border px-4 py-3 text-sm font-medium ' . $styles[$type]]) }} role="{{ $role }}">
    {{ $slot }}
</div>
