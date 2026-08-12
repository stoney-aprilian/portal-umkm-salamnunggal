@props(['code' => '500', 'title' => 'Terjadi Kesalahan', 'message' => ''])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $code }} — {{ config('app.name', 'Portal UMKM Salamnunggal') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center bg-slate-50 px-4">
            <p class="text-5xl font-semibold text-emerald-600">{{ $code }}</p>
            <h1 class="mt-4 text-center text-2xl font-semibold text-slate-900">{{ $title }}</h1>
            @if ($message !== '')
                <p class="mt-2 max-w-md text-center text-sm text-slate-600">{{ $message }}</p>
            @endif
            <div class="mt-8 flex flex-col items-center gap-3 sm:flex-row">
                {{ $slot }}
                <a href="{{ url('/') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition duration-300 hover:bg-emerald-500">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </body>
</html>
