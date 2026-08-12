@props(['disabled' => false])

<textarea @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm']) }}>{{ $slot }}</textarea>
