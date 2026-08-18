<x-app-layout title="Kelola Pengguna">
    <div class="py-8 sm:py-10">
        <div class="container-page">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-[#8A7464] transition-colors duration-150 hover:text-[#5C4033] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                Kembali ke Dashboard
            </a>

            <div class="mt-6 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Kelola Pengguna</h1>
                    <p class="mt-1 text-sm text-slate-600">Kelola akun pengguna yang terdaftar pada Portal UMKM Salamnunggal.</p>
                </div>
                <a href="{{ route('admin.users.create') }}" class="inline-flex min-h-11 w-full shrink-0 items-center justify-center gap-2 rounded-xl bg-[#C26A4A] px-5 text-sm font-semibold text-white transition duration-300 hover:bg-[#A8563A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2 sm:w-auto">
                    + Tambah Pengguna
                </a>
            </div>

            @if (session('status'))
                <x-alert type="success" class="mt-6">{{ session('status') }}</x-alert>
            @endif
            @if (session('error'))
                <x-alert type="error" class="mt-6">{{ session('error') }}</x-alert>
            @endif

            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="rounded-2xl bg-white p-4 shadow-sm sm:p-5">
                    <dl>
                        <dt class="text-xs font-medium text-slate-500">Total Pengguna</dt>
                        <dd class="mt-1 text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">{{ $totalCount }}</dd>
                    </dl>
                </div>
                <div class="rounded-2xl bg-white p-4 shadow-sm sm:p-5">
                    <dl>
                        <dt class="text-xs font-medium text-slate-500">Aktif</dt>
                        <dd class="mt-1 text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">{{ $approvedCount }}</dd>
                    </dl>
                </div>
                <div class="rounded-2xl bg-white p-4 shadow-sm sm:p-5">
                    <dl>
                        <dt class="text-xs font-medium text-slate-500">Nonaktif</dt>
                        <dd class="mt-1 text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">{{ $suspendedCount }}</dd>
                    </dl>
                </div>
                <div class="rounded-2xl bg-white p-4 shadow-sm sm:p-5">
                    <dl>
                        <dt class="text-xs font-medium text-slate-500">Menunggu</dt>
                        <dd class="mt-1 text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">{{ $pendingCount }}</dd>
                    </dl>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex-1 sm:max-w-sm">
                    <label for="search" class="sr-only">Cari nama atau email pengguna</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-4 w-4 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.35-4.35" />
                            </svg>
                        </div>
                        <input
                            type="search"
                            id="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari nama atau email pengguna..."
                            class="block w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2"
                        >
                    </div>
                </form>
                <div class="flex items-center gap-2">
                    <select name="status" onchange="this.form.submit()" class="rounded-xl border border-slate-300 bg-white py-2.5 pl-3 pr-8 text-sm text-slate-700 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        <option value="">Semua Status</option>
                        <option value="approved" @selected($status === 'approved')>Aktif</option>
                        <option value="suspended" @selected($status === 'suspended')>Nonaktif</option>
                        <option value="pending" @selected($status === 'pending')>Menunggu</option>
                        <option value="rejected" @selected($status === 'rejected')>Ditolak</option>
                        <option value="needs_revision" @selected($status === 'needs_revision')>Perlu Revisi</option>
                    </select>
                    <select name="sort" onchange="this.form.submit()" class="rounded-xl border border-slate-300 bg-white py-2.5 pl-3 pr-8 text-sm text-slate-700 focus:border-[#C26A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                        <option value="desc" @selected($sort === 'desc')>Terbaru</option>
                        <option value="asc" @selected($sort === 'asc')>Terlama</option>
                        <option value="name_asc" @selected($sort === 'name_asc')>Nama A–Z</option>
                        <option value="name_desc" @selected($sort === 'name_desc')>Nama Z–A</option>
                    </select>
                    @if ($search !== '' || $status !== '' || $sort !== 'desc')
                        <a href="{{ route('admin.users.index') }}" class="inline-flex min-h-10 items-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                            Reset
                        </a>
                    @endif
                </div>
            </div>

            <section class="mt-4" aria-label="Daftar pengguna">
                @if ($users->isNotEmpty())
                    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
                        <div class="hidden grid-cols-12 gap-4 border-b border-slate-100 bg-slate-50/50 px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 sm:grid">
                            <div class="col-span-4">Pengguna</div>
                            <div class="col-span-2">Role</div>
                            <div class="col-span-2">Status</div>
                            <div class="col-span-2">Bergabung</div>
                            <div class="col-span-2 text-right">Aksi</div>
                        </div>
                        <ul class="divide-y divide-slate-100">
                            @foreach ($users as $user)
                                <li>
                                    <a href="{{ route('admin.users.show', $user) }}" class="flex items-center gap-4 px-5 py-4 transition-colors duration-150 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-inset sm:grid sm:grid-cols-12 sm:items-center sm:gap-4">
                                        <div class="hidden sm:col-span-4 sm:flex sm:items-center sm:gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-[#F4EDE1] text-[#5C4033]">
                                                <span class="text-sm font-semibold">{{ substr($user->name, 0, 1) }}</span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                                                <p class="mt-0.5 text-xs text-slate-500 sm:hidden">{{ $user->email ?? 'Tanpa email' }} &middot; {{ $user->created_at->format('d M Y') }}</p>
                                            </div>
                                        </div>
                                        <div class="hidden sm:col-span-2 sm:block">
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">Owner</span>
                                        </div>
                                        <div class="hidden sm:col-span-2 sm:block">
                                            <x-user-status-badge :status="$user->status" />
                                        </div>
                                        <div class="hidden sm:col-span-2 sm:block">
                                            <p class="text-sm text-slate-700">{{ $user->created_at->format('d M Y') }}</p>
                                        </div>
                                        <div class="hidden sm:col-span-2 sm:flex sm:justify-end">
                                            <span class="inline-flex min-h-10 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-[#5C4033] transition duration-300 hover:border-[#C26A4A] hover:text-[#C26A4A]">
                                                Kelola
                                            </span>
                                        </div>
                                        <div class="sm:hidden">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                                                    <p class="mt-0.5 text-xs text-slate-500">{{ $user->email ?? 'Tanpa email' }}</p>
                                                    <p class="mt-0.5 text-xs text-slate-500">{{ $user->created_at->format('d M Y') }}</p>
                                                </div>
                                                <div class="flex flex-col items-end gap-2">
                                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">Owner</span>
                                                    <x-user-status-badge :status="$user->status" />
                                                    <svg class="h-4 w-4 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="m9 18 6-6-6-6" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center rounded-2xl bg-white px-5 py-10 text-center shadow-sm sm:px-6">
                        @if ($search !== '' || $status !== '' || $sort !== 'desc')
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400" aria-hidden="true">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8" />
                                    <path d="m21 21-4.35-4.35" />
                                </svg>
                            </div>
                            <p class="mt-3 text-sm font-semibold text-slate-900">Pengguna tidak ditemukan</p>
                            <p class="mt-1 text-sm text-slate-500">Coba ubah kata kunci atau filter yang digunakan.</p>
                        @else
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400" aria-hidden="true">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </div>
                            <p class="mt-3 text-sm font-semibold text-slate-900">Belum ada pengguna</p>
                            <p class="mt-1 text-sm text-slate-500">Belum ada akun pengguna yang dapat dikelola saat ini.</p>
                            <a href="{{ route('admin.users.create') }}" class="mt-4 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#C26A4A] px-5 text-sm font-semibold text-white transition duration-300 hover:bg-[#A8563A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                                + Tambah Pengguna
                            </a>
                        @endif
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
