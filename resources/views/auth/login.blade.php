<x-guest-layout title="Masuk" :split="true">
    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <div class="relative mt-1.5">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <svg class="h-4 w-4 text-[#8A7464]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="16" x="2" y="4" rx="2" />
                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                    </svg>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@email.com" class="block min-h-12 w-full rounded-xl border-[#E8D8C8] bg-[#FAF6F5] py-2.5 pl-10 pr-4 text-sm text-[#3F2A22] placeholder:text-[#8A7464]/60 focus:border-[#C26A4A] focus:ring-[#C26A4A] shadow-sm">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Kata Sandi')" />
            <div class="relative mt-1.5">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <svg class="h-4 w-4 text-[#8A7464]" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Kata sandi Anda" class="block min-h-12 w-full rounded-xl border-[#E8D8C8] bg-[#FAF6F5] py-2.5 pl-10 pr-10 text-sm text-[#3F2A22] placeholder:text-[#8A7464]/60 focus:border-[#C26A4A] focus:ring-[#C26A4A] shadow-sm">
                <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-[#8A7464] hover:text-[#5C4033]" aria-label="Tampilkan kata sandi">
                    <svg id="eye-icon" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    <svg id="eye-off-icon" class="hidden h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                        <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
                        <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
                        <line x1="2" x2="22" y1="2" y2="22" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me + Forgot Password -->
        <div class="flex min-h-11 items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-[#E8D8C8] text-[#5C4033] shadow-sm focus:ring-[#C26A4A]" name="remember">
                <span class="ms-2.5 text-sm text-[#5F524A]">{{ __('Ingat Saya') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="rounded-md text-sm font-medium text-[#C26A4A] hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2" href="{{ route('password.request') }}">
                    {{ __('Lupa kata sandi?') }}
                </a>
            @endif
        </div>

        <div>
            <button type="submit" class="flex w-full items-center justify-center rounded-xl bg-[#3F2A22] px-5 py-3 text-sm font-semibold text-white transition-colors duration-150 hover:bg-[#5C4033] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                {{ __('Masuk') }}
            </button>
        </div>
    </form>
</x-guest-layout>

@push('scripts')
<script>
    document.getElementById('toggle-password').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        const eyeOffIcon = document.getElementById('eye-off-icon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.add('hidden');
            eyeOffIcon.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('hidden');
            eyeOffIcon.classList.add('hidden');
        }
    });
</script>
@endpush
