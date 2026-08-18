<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $settings['site.description'] ?? 'Portal UMKM Desa Salamnunggal — temukan UMKM dan produk unggulan Desa Salamnunggal, lihat detail usaha lokal, dan hubungi langsung pemiliknya.' }}">
        <link rel="icon" href="{{ !empty($settings['site.favicon']) ? asset('storage/'.$settings['site.favicon']) : asset('favicon.ico') }}">

        <title>{{ $title ? $title . ' — ' . config('app.name') : config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('scripts')
    </head>
    <body class="font-sans antialiased">
        @if ($split)
            <!-- Desktop: split layout -->
            <div class="hidden min-h-screen flex-col lg:flex lg:flex-row">
                <!-- Left: Branding -->
                <div class="relative flex flex-col justify-between overflow-hidden bg-[#3F2A22] px-8 py-10 lg:w-5/12 lg:px-12 lg:py-14">
                    <span aria-hidden="true" class="pointer-events-none absolute -bottom-24 -right-24 h-80 w-80 text-[#E8D8C8] opacity-[0.04]">
                        <x-application-logo class="h-full w-full" />
                    </span>

                    <div class="relative flex flex-1 flex-col justify-center">
                        <div class="flex flex-col gap-6">
                            <a href="{{ url('/') }}" aria-label="{{ config('app.name', 'Portal UMKM Salamnunggal') }}" class="inline-flex w-fit flex-col items-center gap-3 rounded-2xl p-1.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 lg:flex-row">
                                @if (!empty($settings['site.logo']))
                                    <img src="{{ asset('storage/'.$settings['site.logo']) }}" alt="{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}" class="h-12 w-12 rounded-2xl object-contain lg:h-14 lg:w-14">
                                @else
                                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#5C4033] text-white lg:h-14 lg:w-14">
                                        <x-application-logo class="h-6 w-6 lg:h-7 lg:w-7" />
                                    </span>
                                @endif
                                <span class="text-center leading-tight lg:text-left">
                                    <span class="block text-base font-semibold tracking-tight text-[#FAF6F5] lg:text-lg">{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}</span>
                                    <span class="block text-xs font-medium text-[#E8D8C8]/70 lg:text-sm">{{ $settings['site.tagline'] ?? 'Desa Salamnunggal' }}</span>
                                </span>
                            </a>

                            <div class="hidden lg:block">
                                <div class="h-0.5 w-10 bg-[#C26A4A]" aria-hidden="true"></div>
                                <p class="mt-5 max-w-sm text-sm leading-relaxed text-[#E8D8C8]/75">
                                    Temukan, kenali, dan dukung usaha lokal Desa Salamnunggal.
                                </p>
                            </div>
                        </div>

                        <div class="pointer-events-none absolute inset-0 opacity-[0.06]" aria-hidden="true">
                            <svg viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg" class="h-full w-full">
                                <circle cx="320" cy="80" r="120" fill="currentColor" class="text-[#C26A4A]" />
                                <circle cx="80" cy="320" r="80" fill="currentColor" class="text-[#E8D8C8]" />
                                <path d="M60 60 Q180 20 300 100 T380 300" fill="none" stroke="currentColor" stroke-width="2" class="text-[#C26A4A]" />
                                <path d="M20 200 Q100 120 220 180 T400 140" fill="none" stroke="currentColor" stroke-width="1.5" class="text-[#E8D8C8]" />
                            </svg>
                        </div>
                    </div>

                    <div class="relative mt-6 hidden lg:block">
                        <p class="text-xs text-[#E8D8C8]/50">&copy; {{ now()->year }} {{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}</p>
                    </div>
                </div>

                <!-- Right: Login Card -->
                <div class="flex w-full items-center justify-center bg-[#FAF6F5] px-4 py-10 lg:w-7/12 lg:px-10">
                    <div class="w-full max-w-md">
                        <div class="rounded-3xl bg-white p-6 shadow-[0_8px_30px_-12px_rgba(63,42,34,0.18)] sm:p-8">
                            <div class="text-center">
                                <h1 class="text-2xl font-semibold tracking-tight text-[#3F2A22] sm:text-3xl">Masuk ke Portal UMKM</h1>
                                <p class="mt-2 text-sm text-[#8A7464]">Masuk untuk mengelola UMKM dan produk Anda.</p>
                            </div>

                            <!-- Session Status -->
                            <x-auth-session-status class="mt-5" :status="session('status')" />

                            {{ $slot }}
                        </div>

                        <p class="mt-6 text-center text-xs text-[#8A7464]/80">
                            Dilindungi dan aman. Data Anda terlindungi dengan enkripsi standar.
                        </p>

                        <a href="{{ url('/') }}" class="mt-6 inline-flex items-center justify-center gap-1.5 rounded-md text-sm font-medium text-[#C26A4A] hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m15 18-6-6 6-6" />
                            </svg>
                            Kembali ke Portal
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mobile: simple centered login -->
            <div class="flex min-h-screen flex-col lg:hidden">
                <span aria-hidden="true" class="pointer-events-none absolute -bottom-24 -right-24 h-72 w-72 text-[#E8D8C8] opacity-[0.04]">
                    <x-application-logo class="h-full w-full" />
                </span>

                <header class="relative flex flex-col items-center gap-2 px-6 pt-10 pb-4 text-center">
                    <a href="{{ url('/') }}" aria-label="{{ config('app.name', 'Portal UMKM Salamnunggal') }}" class="inline-flex flex-col items-center gap-2 rounded-2xl p-1.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        @if (!empty($settings['site.logo']))
                            <img src="{{ asset('storage/'.$settings['site.logo']) }}" alt="{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}" class="h-14 w-14 rounded-2xl object-contain">
                        @else
                            <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#5C4033] text-white">
                                <x-application-logo class="h-7 w-7" />
                            </span>
                        @endif
                        <span class="text-center leading-tight">
                            <span class="block text-lg font-semibold tracking-tight text-[#3F2A22]">{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}</span>
                            <span class="block text-sm font-medium text-[#8A7464]">{{ $settings['site.tagline'] ?? 'Desa Salamnunggal' }}</span>
                        </span>
                    </a>
                </header>

                <main class="relative flex-1 px-4 pb-10">
                    <div class="mx-auto w-full max-w-sm">
                        <div class="rounded-3xl bg-white p-6 shadow-[0_8px_30px_-12px_rgba(63,42,34,0.18)] sm:p-8">
                            <div class="text-center">
                                <h1 class="text-2xl font-semibold tracking-tight text-[#3F2A22] sm:text-3xl">Masuk ke Portal UMKM</h1>
                                <p class="mt-2 text-sm text-[#8A7464]">Masuk untuk mengelola UMKM dan produk Anda.</p>
                            </div>

                            <!-- Session Status -->
                            <x-auth-session-status class="mt-5" :status="session('status')" />

                            {{ $slot }}
                        </div>

                        <p class="mt-4 text-center text-xs text-[#8A7464]/80">
                            Dilindungi dan aman. Data Anda terlindungi dengan enkripsi standar.
                        </p>

                        <a href="{{ url('/') }}" class="mt-6 inline-flex items-center justify-center gap-1.5 rounded-xl border border-[#E8D8C8] bg-white px-5 py-3 text-sm font-semibold text-[#5C4033] transition-colors duration-150 hover:border-[#C26A4A] hover:text-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m15 18-6-6 6-6" />
                            </svg>
                            Kembali ke Portal
                        </a>
                    </div>
                </main>
            </div>
        @else
            <div class="relative flex min-h-screen flex-col items-center justify-center bg-slate-50 px-4 py-12 sm:py-16">
                <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-gradient-to-b from-[#F4EDE1] via-[#FAF6F5] to-transparent"></div>

                <div class="relative flex w-full flex-col items-center">
                    <a href="{{ url('/') }}" aria-label="{{ config('app.name', 'Portal UMKM Salamnunggal') }}" class="inline-flex flex-col items-center gap-3 rounded-2xl p-1.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        @if (!empty($settings['site.logo']))
                            <img src="{{ asset('storage/'.$settings['site.logo']) }}" alt="{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}" class="h-12 w-12 rounded-2xl object-contain sm:h-14 sm:w-14">
                        @else
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#5C4033] text-white sm:h-14 sm:w-14">
                                <x-application-logo class="h-6 w-6 sm:h-7 sm:w-7" />
                            </span>
                        @endif
                        <span class="text-center leading-tight">
                            <span class="block text-base font-semibold tracking-tight text-slate-900 sm:text-lg">{{ $settings['site.name'] ?? 'Portal UMKM Salamnunggal' }}</span>
                            <span class="block text-xs font-medium text-slate-500 sm:text-sm">{{ $settings['site.tagline'] ?? 'Desa Salamnunggal' }}</span>
                        </span>
                    </a>

                    @if ($card)
                        <div class="mt-6 w-full rounded-2xl bg-white p-6 shadow-sm sm:max-w-md sm:p-8">
                            {{ $slot }}
                        </div>
                    @else
                        <div class="mt-8 w-full sm:max-w-sm">
                            {{ $slot }}
                        </div>
                    @endif

                    <a href="{{ url('/') }}" class="mt-8 inline-flex items-center gap-1.5 rounded-md text-sm font-medium text-[#C26A4A] hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                        Kembali ke Portal
                    </a>
                </div>
            </div>
        @endif
    </body>
</html>
