@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex min-h-11 items-center rounded-xl bg-[#F4EDE1] px-4 text-sm font-semibold text-[#5C4033] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2'
            : 'inline-flex min-h-11 items-center rounded-xl px-4 text-sm font-medium text-slate-500 transition duration-150 ease-in-out hover:bg-[#F4EDE1] hover:text-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
