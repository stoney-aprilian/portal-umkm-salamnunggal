@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-xl bg-emerald-50 px-4 py-2.5 text-start text-sm font-semibold text-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500'
            : 'block w-full rounded-xl px-4 py-2.5 text-start text-sm font-medium text-slate-600 transition duration-150 ease-in-out hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
