<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $settings['site.description'] ?? 'Portal UMKM Desa Salamnunggal — temukan UMKM dan produk unggulan Desa Salamnunggal, lihat detail usaha lokal, dan hubungi langsung pemiliknya.' }}">
        <link rel="icon" href="{{ !empty($settings['site.favicon']) ? asset('storage/'.$settings['site.favicon']) : asset('favicon.ico') }}">

        <meta property="og:site_name" content="{{ config('app.name') }}">
        <meta property="og:type" content="website">
        <meta property="og:title" content="{{ $title ? $title . ' — ' . config('app.name') : config('app.name') }}">
        <meta property="og:description" content="{{ $settings['site.description'] ?? 'Portal UMKM Desa Salamnunggal — temukan UMKM dan produk unggulan Desa Salamnunggal, lihat detail usaha lokal, dan hubungi langsung pemiliknya.' }}">
        <meta property="og:url" content="{{ url()->current() }}">

        <title>{{ $title ? $title . ' — ' . config('app.name') : config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="bg-slate-50">
            @include('layouts.navigation')
            @isset($header)
                <header class="bg-white shadow-sm">
                    <div class="container-page py-6">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            @include('layouts.footer')
        </div>
    </body>
</html>
