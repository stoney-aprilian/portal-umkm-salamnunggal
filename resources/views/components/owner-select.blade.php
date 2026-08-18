@props(['disabled' => false])

<select @disabled($disabled) {{ $attributes->merge(['class' => 'border-[#ECE5D9] focus:border-[#C26A4A] focus:ring-[#C26A4A] rounded-xl shadow-sm']) }}>{{ $slot }}</select>