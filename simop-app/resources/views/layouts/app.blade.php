
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIMOP')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased">
    @php
        $user = auth()->user();
        $role = $user?->role?->slug ?? '';

        $menus = [
            'admin' => [
                ['Dashboard', 'dashboard', 'layout-dashboard'],
                ['Order & RFQ', 'admin.orders.index', 'clipboard-list'],
                ['Client', 'admin.clients.index', 'users'],
                ['Survey', 'admin.surveys.index', 'map-pinned'],
                ['Work Estimate', 'admin.work-estimates.index', 'calculator'],
                ['Approval', 'admin.approvals.index', 'clipboard-check'],
                ['Project', 'admin.projects.index', 'folder-kanban'],
                ['Planning', 'admin.planning.index', 'calendar-days'],
                ['Produksi & Instalasi', 'admin.production.index', 'hard-hat'],
                ['BAPP', 'admin.bapp.index', 'file-check'],
                ['Dokumen', 'admin.documents.index', 'files'],
                ['Invoice', 'admin.invoices.index', 'receipt'],
                ['Vendor', 'admin.vendors.index', 'building-2'],
                ['Laporan', 'admin.reports.index', 'chart-no-axes-combined'],
                ['Pengguna', 'admin.users.index', 'user-cog'],
            ],
            'direktur' => [
                ['Dashboard', 'dashboard', 'layout-dashboard'],
                ['Survey', 'direktur.surveys.index', 'map-pinned'],
                ['Work Estimate', 'direktur.work-estimates.index', 'calculator'],
                ['Approval', 'direktur.approvals.index', 'clipboard-check'],
                ['Project', 'direktur.projects.index', 'folder-kanban'],
                ['Laporan', 'direktur.reports.index', 'chart-no-axes-combined'],
            ],
            'ceo' => [
                ['Dashboard', 'dashboard', 'layout-dashboard'],
                ['Approval', 'ceo.approvals.index', 'clipboard-check'],
                ['Project', 'ceo.projects.index', 'folder-kanban'],
                ['Invoice', 'ceo.invoices.index', 'receipt'],
                ['Laporan', 'ceo.reports.index', 'chart-no-axes-combined'],
            ],
            'petugas-lapangan' => [
                ['Dashboard', 'dashboard', 'layout-dashboard'],
                ['Survey', 'petugas-lapangan.surveys.index', 'map-pinned'],
                ['Produksi & Instalasi', 'petugas-lapangan.production.index', 'hard-hat'],
                ['BAPP', 'petugas-lapangan.bapp.index', 'file-check'],
                ['Project', 'petugas-lapangan.projects.index', 'folder-kanban'],
                ['Dokumen', 'petugas-lapangan.documents.index', 'files'],
            ],
            'client' => [
                ['Dashboard', 'dashboard', 'layout-dashboard'],
                ['Order & RFQ', 'client.orders.index', 'clipboard-list'],
                ['Tracking', 'client.tracking.index', 'map-pinned'],
                ['Dokumen', 'client.documents.index', 'files'],
                ['Invoice', 'client.invoices.index', 'receipt'],
                ['Rating', 'client.ratings.index', 'star'],
            ],
        ];

        $currentMenus = $menus[$role] ?? [];
        $currentRoute = request()->route()?->getName();

        $roleLabels = [
            'admin' => 'Administrator',
            'direktur' => 'Direktur',
            'ceo' => 'Chief Executive Officer',
            'petugas-lapangan' => 'Petugas Lapangan',
            'client' => 'Client',
        ];

        $roleLabel = $roleLabels[$role] ?? 'Pengguna';
    @endphp

    <div class="min-h-screen lg:flex">
        <aside class="flex w-full flex-col bg-slate-900 text-white lg:min-h-screen lg:w-64 lg:shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 border-b border-slate-700 px-6 py-6">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-xl font-bold">
                    S
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-wide">SIMOP</h1>
                    <p class="mt-1 text-xs text-slate-400">Manajemen Operasional</p>
                </div>
            </a>

            <div class="border-b border-slate-700 px-5 py-4">
                <p class="text-xs text-slate-500">LOGIN SEBAGAI</p>
                <p class="mt-1 font-semibold">{{ $roleLabel }}</p>
            </div>

            <nav class="flex-1 space-y-1 px-3 py-5">
                <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-slate-500">
                    Menu Utama
                </p>

                @foreach ($currentMenus as [$label, $routeName, $icon])
                    @php
                        $routeExists = \Illuminate\Support\Facades\Route::has($routeName);
                        $active = $currentRoute === $routeName;
                    @endphp

                    @if ($routeExists)
                        <a
                            href="{{ route($routeName) }}"
                            aria-current="{{ $active ? 'page' : 'false' }}"
                            class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm transition
                            {{ $active
                                ? 'bg-blue-600 font-semibold text-white shadow-sm'
                                : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                        >
                            <span class="text-base" aria-hidden="true">
                                @switch($icon)
                                    @case('layout-dashboard') ▦ @break
                                    @case('clipboard-list') ☷ @break
                                    @case('users') ♙ @break
                                    @case('map-pinned') ◎ @break
                                    @case('calculator') ∑ @break
                                    @case('clipboard-check') ✓ @break
                                    @case('folder-kanban') ▤ @break
                                    @case('calendar-days') ▦ @break
                                    @case('hard-hat') ◈ @break
                                    @case('file-check') ☑ @break
                                    @case('files') ▧ @break
                                    @case('receipt') ▤ @break
                                    @case('building-2') ▥ @break
                                    @case('chart-no-axes-combined') ▥ @break
                                    @case('user-cog') ⚙ @break
                                    @case('star') ☆ @break
                                    @default ○
                                @endswitch
                            </span>
                            <span>{{ $label }}</span>
                        </a>
                    @else
                        <div class="flex cursor-not-allowed items-center gap-3 rounded-lg px-4 py-3 text-sm text-slate-500">
                            <span aria-hidden="true">○</span>
                            <span>{{ $label }}</span>
                            <span class="ml-auto text-xs">Segera</span>
                        </div>
                    @endif
                @endforeach
            </nav>

            <div class="border-t border-slate-700 px-3 py-4">
                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-slate-300 transition hover:bg-slate-800 hover:text-white"
                >
                    <span aria-hidden="true">?</span>
                    <span>Pusat Bantuan</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button
                        type="submit"
                        class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-left text-sm text-red-300 transition hover:bg-slate-800 hover:text-red-200"
                    >
                        <span aria-hidden="true">↪</span>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="flex flex-col gap-4 border-b border-slate-200 bg-white px-6 py-5 sm:flex-row sm:items-center sm:justify-between lg:px-10">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-blue-600">
                        Sistem Informasi Manajemen Operasional
                    </p>
                    <h2 class="mt-1 text-xl font-bold text-slate-900">
                        @yield('page-title', 'Dashboard')
                    </h2>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-semibold text-slate-800">{{ $user?->name ?? 'Pengguna' }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $roleLabel }}</p>
                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-700">
                        {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                    </div>
                </div>
            </header>

            <main class="flex-1 p-5 sm:p-6 lg:p-10">
                @yield('content')
            </main>

            <footer class="border-t border-slate-200 bg-white px-6 py-4 text-center text-xs text-slate-400">
                SIMOP &copy; {{ date('Y') }} · Sistem Informasi Manajemen Operasional
            </footer>
        </div>
    </div>
</body>
</html>
