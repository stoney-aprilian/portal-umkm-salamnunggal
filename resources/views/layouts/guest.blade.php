<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Portal UMKM Desa Salamnunggal — temukan UMKM dan produk unggulan Desa Salamnunggal, lihat detail usaha lokal, dan hubungi langsung pemiliknya.">

        <title>{{ $title ? $title . ' — ' . config('app.name') : config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="relative flex min-h-screen flex-col items-center justify-center bg-slate-50 px-4 py-12 sm:py-16">
            <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-gradient-to-b from-emerald-100/70 via-emerald-50/40 to-transparent"></div>

            <div class="relative flex w-full flex-col items-center">
                <a href="{{ url('/') }}" aria-label="{{ config('app.name', 'Portal UMKM Salamnunggal') }}" class="inline-flex flex-col items-center gap-3 rounded-2xl p-1.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-600 text-white sm:h-14 sm:w-14">
                        <x-application-logo class="h-6 w-6 sm:h-7 sm:w-7" />
                    </span>
                    <span class="text-center leading-tight">
                        <span class="block text-base font-semibold tracking-tight text-slate-900 sm:text-lg">Portal UMKM</span>
                        <span class="block text-xs font-medium text-slate-500 sm:text-sm">Salamnunggal</span>
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

                <a href="{{ url('/') }}" class="mt-8 inline-flex items-center gap-1.5 rounded-md text-sm font-medium text-emerald-600 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                    <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                    Kembali ke Portal
                </a>
            </div>
        </div>
    </body>
</html>