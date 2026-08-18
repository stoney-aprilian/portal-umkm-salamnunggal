@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-xl bg-[#F4EDE1] px-4 py-2.5 text-start text-sm font-semibold text-[#5C4033] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]'
            : 'block w-full rounded-xl px-4 py-2.5 text-start text-sm font-medium text-[#5F524A] transition duration-150 ease-in-out hover:bg-[#FAF6F5] hover:text-[#3F2A22] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A]';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
