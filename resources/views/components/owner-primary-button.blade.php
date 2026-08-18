<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex min-h-11 items-center justify-center rounded-xl bg-[#5C4033] px-5 py-2.5 text-sm font-semibold text-white transition duration-300 hover:bg-[#3F2A22] active:bg-[#3F2A22] focus:outline-none focus:ring-2 focus:ring-[#C26A4A] focus:ring-offset-2 disabled:opacity-25']) }}>
    {{ $slot }}
</button>