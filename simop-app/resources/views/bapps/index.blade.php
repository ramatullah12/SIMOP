@extends('layouts.app')

@section('title', 'BAPP | SIMOP')
@section('page-title', 'BAPP')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;

        $statusLabels = [
            'draft' => 'Draft',
            'submitted' => 'Diajukan',
            'approved' => 'Disetujui',
        ];

        $statusColors = [
            'draft' => 'bg-slate-100 text-slate-700',
            'submitted' => 'bg-amber-100 text-amber-700',
            'approved' => 'bg-green-100 text-green-700',
        ];

        $projectStatusLabels = [
            'planning' => 'Planning',
            'ongoing' => 'Berjalan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        $projectStatusColors = [
            'planning' => 'bg-slate-100 text-slate-700',
            'ongoing' => 'bg-blue-100 text-blue-700',
            'completed' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-red-100 text-red-700',
        ];
    @endphp

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold">
                    Terdapat data yang perlu diperbaiki:
                </p>

                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">
                    Berita Acara Penyelesaian Pekerjaan
                </h2>

                @if ($role === 'admin')
                    <p class="mt-1 text-sm text-slate-500">
                        Kelola BAPP dari Produksi & Instalasi yang telah selesai 100%.
                    </p>
                @elseif ($role === 'direktur')
                    <p class="mt-1 text-sm text-slate-500">
                        Pantau BAPP dan penyelesaian Project.
                    </p>
                @else
                    <p class="mt-1 text-sm text-slate-500">
                        Lihat BAPP dari pekerjaan Produksi & Instalasi yang ditugaskan kepada Anda.
                    </p>
                @endif
            </div>

            @if ($role === 'admin')
                <a
                    href="{{ route('bapps.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    + Buat BAPP
                </a>
            @endif
        </div>

        @if ($role === 'direktur')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <p class="text-sm font-semibold text-blue-800">
                    Mode Monitoring Direktur
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Direktur dapat memantau BAPP dan status penyelesaian Project, tetapi tidak dapat mengubah status BAPP.
                </p>
            </div>
        @elseif ($role === 'petugas-lapangan')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <p class="text-sm font-semibold text-blue-800">
                    BAPP Pekerjaan Saya
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Daftar ini hanya menampilkan BAPP dari Produksi & Instalasi yang ditugaskan kepada Anda.
                </p>
            </div>
        @endif

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                No.
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                BAPP
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Produksi & Instalasi
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Project
                            </th>

                            @if ($role !== 'petugas-lapangan')
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Petugas
                                </th>
                            @endif

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Penyelesaian
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Status BAPP
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Status Project
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($bapps as $bapp)
                            @php
                                $production = $bapp->production;
                                $project = $production?->planning?->project;
                                $order = $project?->workEstimate?->order;

                                $statusLabel = $statusLabels[$bapp->status]
                                    ?? ucfirst($bapp->status);

                                $statusColor = $statusColors[$bapp->status]
                                    ?? 'bg-slate-100 text-slate-700';

                                $projectStatusLabel = $projectStatusLabels[$project?->status]
                                    ?? ucfirst($project?->status ?? '-');

                                $projectStatusColor = $projectStatusColors[$project?->status]
                                    ?? 'bg-slate-100 text-slate-700';
                            @endphp

                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                    {{ $bapps->firstItem() + $loop->index }}
                                </td>

                                <td class="min-w-44 px-5 py-4">
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ $bapp->bapp_number }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Dibuat oleh:
                                        {{ $bapp->creator?->name ?? '-' }}
                                    </p>
                                </td>

                                <td class="min-w-60 px-5 py-4">
                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $production?->title ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $production?->production_number ?? '-' }}
                                    </p>

                                    @if ($production)
                                        <div class="mt-2 flex items-center gap-2">
                                            <div class="h-1.5 w-20 overflow-hidden rounded-full bg-slate-200">
                                                <div
                                                    class="h-full rounded-full bg-green-600"
                                                    style="width: {{ max(0, min(100, (int) $production->progress)) }}%"
                                                ></div>
                                            </div>

                                            <span class="text-xs font-semibold text-green-700">
                                                {{ max(0, min(100, (int) $production->progress)) }}%
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <td class="min-w-56 px-5 py-4">
                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $project?->title ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $project?->project_number ?? '-' }}
                                    </p>

                                    @if ($order?->order_number)
                                        <p class="mt-1 text-xs text-slate-400">
                                            Order: {{ $order->order_number }}
                                        </p>
                                    @endif
                                </td>

                                @if ($role !== 'petugas-lapangan')
                                    <td class="min-w-40 px-5 py-4">
                                        <p class="text-sm font-medium text-slate-700">
                                            {{ $production?->assignedUser?->name ?? 'Belum ditentukan' }}
                                        </p>
                                    </td>
                                @endif

                                <td class="whitespace-nowrap px-5 py-4">
                                    <p class="text-sm font-medium text-slate-700">
                                        {{ $bapp->completion_date?->format('d/m/Y') ?? '-' }}
                                    </p>

                                    @if ($bapp->location)
                                        <p class="mt-1 max-w-40 truncate text-xs text-slate-500">
                                            {{ $bapp->location }}
                                        </p>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusColor }}">
                                        {{ $statusLabel }}
                                    </span>

                                    @if ($bapp->status === 'draft')
                                        <p class="mt-2 text-xs text-slate-500">
                                            Belum diajukan
                                        </p>
                                    @elseif ($bapp->status === 'submitted')
                                        <p class="mt-2 text-xs text-amber-600">
                                            Menunggu persetujuan
                                        </p>
                                    @elseif ($bapp->status === 'approved')
                                        <p class="mt-2 text-xs font-medium text-green-600">
                                            Final
                                        </p>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $projectStatusColor }}">
                                        {{ $projectStatusLabel }}
                                    </span>

                                    @if (
                                        $bapp->status === 'approved'
                                        && $project?->status === 'completed'
                                    )
                                        <p class="mt-2 text-xs font-medium text-green-600">
                                            Project selesai
                                        </p>
                                    @elseif (
                                        $bapp->status !== 'approved'
                                        && $project?->status === 'ongoing'
                                    )
                                        <p class="mt-2 text-xs text-slate-500">
                                            Menunggu BAPP disetujui
                                        </p>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <a
                                        href="{{ route('bapps.show', $bapp) }}"
                                        class="text-sm font-semibold text-blue-600 hover:text-blue-800"
                                    >
                                        @if ($role === 'direktur')
                                            Pantau
                                        @elseif ($role === 'petugas-lapangan')
                                            Lihat
                                        @else
                                            Detail
                                        @endif
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="{{ $role === 'petugas-lapangan' ? 8 : 9 }}"
                                    class="px-5 py-12 text-center"
                                >
                                    <p class="font-semibold text-slate-700">
                                        Belum ada BAPP.
                                    </p>

                                    @if ($role === 'admin')
                                        <p class="mt-1 text-sm text-slate-500">
                                            BAPP dapat dibuat setelah Produksi & Instalasi selesai 100%.
                                        </p>

                                        <a
                                            href="{{ route('bapps.create') }}"
                                            class="mt-4 inline-flex rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                                        >
                                            + Buat BAPP
                                        </a>
                                    @elseif ($role === 'petugas-lapangan')
                                        <p class="mt-1 text-sm text-slate-500">
                                            Belum ada BAPP dari pekerjaan yang ditugaskan kepada Anda.
                                        </p>
                                    @else
                                        <p class="mt-1 text-sm text-slate-500">
                                            Belum ada BAPP yang dapat dipantau.
                                        </p>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($bapps->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $bapps->links() }}
                </div>
            @endif
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900">
                Alur BAPP
            </h3>

            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        1. Draft
                    </p>

                    <p class="mt-1 text-sm text-slate-600">
                        BAPP telah dibuat dan masih diperiksa.
                    </p>
                </div>

                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">
                        2. Diajukan
                    </p>

                    <p class="mt-1 text-sm text-amber-700">
                        BAPP telah diajukan untuk proses persetujuan.
                    </p>
                </div>

                <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-green-600">
                        3. Disetujui
                    </p>

                    <p class="mt-1 text-sm text-green-700">
                        BAPP final dan Project otomatis dinyatakan selesai.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection