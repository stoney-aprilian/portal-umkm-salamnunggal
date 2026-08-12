<x-app-layout title="Verifikasi UMKM">
    <div class="py-12 sm:py-16">
        <div class="container-page">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 rounded-md text-sm font-medium text-emerald-600 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                Kembali ke Dashboard
            </a>

            <h1 class="mt-6 text-3xl font-semibold tracking-tight text-slate-900">Verifikasi UMKM</h1>
            <p class="mt-2 max-w-2xl leading-relaxed text-slate-600">Periksa pengajuan UMKM yang menunggu validasi.</p>

            @if (session('status'))
                <x-alert type="success" class="mt-6">{{ session('status') }}</x-alert>
            @endif

            @if (session('error'))
                <x-alert type="error" class="mt-6">{{ session('error') }}</x-alert>
            @endif

            @if ($requests->isNotEmpty())
                <p class="mt-6 text-sm text-slate-600">
                    Ada {{ $requests->count() }} pengajuan UMKM yang menunggu pemeriksaan Anda.
                </p>
            @endif

            <section class="mt-4">
                <h2 class="text-xl font-semibold tracking-tight text-slate-900">Pengajuan UMKM</h2>

                @if ($requests->isNotEmpty())
                    <ul class="mt-4 overflow-hidden rounded-2xl bg-white shadow-sm">
                        @foreach ($requests as $request)
                            <li class="min-w-0 border-b border-slate-100 px-5 py-5 last:border-b-0 sm:px-6">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <h3 class="break-words text-lg font-semibold tracking-tight text-slate-900">{{ $request->verifiable->name }}</h3>
                                        <dl class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-sm leading-relaxed text-slate-500">
                                            <div class="min-w-0">
                                                <dt class="inline font-medium text-slate-400">Pemilik:</dt>
                                                <dd class="inline break-words">{{ $request->user?->name ?? '—' }}</dd>
                                            </div>
                                            @if ($request->verifiable->category)
                                                <div class="min-w-0">
                                                    <dt class="inline font-medium text-slate-400">Kategori:</dt>
                                                    <dd class="inline break-words">{{ $request->verifiable->category->name }}</dd>
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <dt class="inline font-medium text-slate-400">Diajukan:</dt>
                                                <dd class="inline break-words">{{ $request->created_at->format('d M Y') }}</dd>
                                            </div>
                                        </dl>
                                    </div>

                                    <div class="flex shrink-0 flex-col items-start gap-3 sm:items-end">
                                        <x-badge status="pending" class="text-sm" />
                                        <a href="{{ route('admin.umkm.verification.show', $request) }}" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 text-sm font-semibold text-white transition duration-300 hover:bg-emerald-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 sm:w-auto">
                                            Periksa
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="mt-4 rounded-2xl bg-white px-5 py-6 shadow-sm sm:px-6">
                        <p class="text-sm leading-relaxed text-slate-600">
                            Tidak ada pengajuan UMKM yang menunggu pemeriksaan. Anda sudah menangani seluruh pengajuan UMKM saat ini.
                        </p>
                        <a href="{{ route('admin.dashboard') }}" class="mt-4 inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-emerald-700 transition duration-300 hover:border-emerald-300 hover:bg-emerald-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                            Kembali ke Dashboard
                        </a>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>