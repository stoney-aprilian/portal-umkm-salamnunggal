<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex min-h-11 items-center justify-center rounded-xl bg-white border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 transition duration-300 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
