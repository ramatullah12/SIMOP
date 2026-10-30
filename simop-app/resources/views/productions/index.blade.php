@extends('layouts.app')

@section('title', 'Produksi & Instalasi | SIMOP')
@section('page-title', 'Produksi & Instalasi')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;

        $statusLabels = [
            'pending' => 'Menunggu',
            'in_progress' => 'Dalam Pengerjaan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        $statusColors = [
            'pending' => 'bg-slate-100 text-slate-700',
            'in_progress' => 'bg-amber-100 text-amber-700',
            'completed' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-red-100 text-red-700',
        ];

        $bappLabels = [
            'draft' => 'Draft',
            'submitted' => 'Diajukan',
            'approved' => 'Disetujui',
        ];

        $bappColors = [
            'draft' => 'bg-slate-100 text-slate-700',
            'submitted' => 'bg-amber-100 text-amber-700',
            'approved' => 'bg-green-100 text-green-700',
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
                    Produksi & Instalasi
                </h2>

                @if ($role === 'admin')
                    <p class="mt-1 text-sm text-slate-500">
                        Kelola pelaksanaan Produksi & Instalasi dari Planning yang telah selesai.
                    </p>
                @elseif ($role === 'direktur')
                    <p class="mt-1 text-sm text-slate-500">
                        Pantau pelaksanaan dan progress Produksi & Instalasi Project.
                    </p>
                @else
                    <p class="mt-1 text-sm text-slate-500">
                        Lihat dan kerjakan Produksi & Instalasi yang ditugaskan kepada Anda.
                    </p>
                @endif
            </div>

            @if ($role === 'admin')
                <a
                    href="{{ route('productions.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    + Buat Produksi & Instalasi
                </a>
            @endif
        </div>

        @if ($role === 'direktur')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <p class="text-sm font-semibold text-blue-800">
                    Mode Monitoring Direktur
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Direktur dapat memantau pelaksanaan dan progress Produksi & Instalasi, tetapi tidak dapat mengubah data atau progress pekerjaan.
                </p>
            </div>
        @elseif ($role === 'petugas-lapangan')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <p class="text-sm font-semibold text-blue-800">
                    Produksi & Instalasi Saya
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Daftar ini hanya menampilkan pekerjaan Produksi & Instalasi yang ditugaskan kepada Anda.
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
                                Produksi
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
                                Jadwal
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Status
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Progress
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                BAPP
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($productions as $production)
                            @php
                                $project = $production->planning?->project;
                                $order = $project?->workEstimate?->order;
                                $bapp = $production->bapp;

                                $progress = max(
                                    0,
                                    min(100, (int) $production->progress)
                                );

                                $statusLabel = $statusLabels[$production->status]
                                    ?? ucfirst(str_replace('_', ' ', $production->status));

                                $statusColor = $statusColors[$production->status]
                                    ?? 'bg-slate-100 text-slate-700';

                                $isLate =
                                    $production->end_date
                                    && $production->end_date->isPast()
                                    && !in_array(
                                        $production->status,
                                        ['completed', 'cancelled'],
                                        true
                                    );
                            @endphp

                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                    {{ $productions->firstItem() + $loop->index }}
                                </td>

                                <td class="min-w-64 px-5 py-4">
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ $production->production_number }}
                                    </p>

                                    <p class="mt-1 text-sm text-slate-700">
                                        {{ $production->title }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Planning:
                                        {{ $production->planning?->planning_number ?? '-' }}
                                    </p>
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
                                            {{ $production->assignedUser?->name ?? 'Belum ditentukan' }}
                                        </p>
                                    </td>
                                @endif

                                <td class="whitespace-nowrap px-5 py-4">
                                    <p class="text-sm text-slate-700">
                                        {{ $production->start_date?->format('d/m/Y') ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs {{ $isLate ? 'font-semibold text-red-600' : 'text-slate-500' }}">
                                        s/d {{ $production->end_date?->format('d/m/Y') ?? '-' }}
                                    </p>

                                    @if ($isLate)
                                        <span class="mt-2 inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                            Terlambat
                                        </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusColor }}">
                                        {{ $statusLabel }}
                                    </span>

                                    @if (
                                        $production->status === 'completed'
                                        && !$bapp
                                    )
                                        <p class="mt-2 text-xs font-medium text-blue-600">
                                            Siap BAPP
                                        </p>
                                    @endif
                                </td>

                                <td class="min-w-44 px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-200">
                                            <div
                                                class="h-full rounded-full bg-blue-600"
                                                style="width: {{ $progress }}%"
                                            ></div>
                                        </div>

                                        <span class="w-10 text-right text-xs font-bold text-slate-700">
                                            {{ $progress }}%
                                        </span>
                                    </div>

                                    @if (
                                        $progress === 100
                                        && !$bapp
                                    )
                                        <p class="mt-2 text-xs text-slate-500">
                                            Menunggu BAPP
                                        </p>
                                    @elseif ($bapp?->status === 'approved')
                                        <p class="mt-2 text-xs font-medium text-green-600">
                                            BAPP disetujui
                                        </p>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    @if ($bapp)
                                        @php
                                            $bappLabel = $bappLabels[$bapp->status]
                                                ?? ucfirst($bapp->status);

                                            $bappColor = $bappColors[$bapp->status]
                                                ?? 'bg-slate-100 text-slate-700';
                                        @endphp

                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $bappColor }}">
                                            {{ $bappLabel }}
                                        </span>
                                    @elseif ($production->status === 'completed')
                                        <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                            Belum Dibuat
                                        </span>
                                    @else
                                        <span class="text-sm text-slate-400">
                                            -
                                        </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <a
                                        href="{{ route('productions.show', $production) }}"
                                        class="text-sm font-semibold text-blue-600 hover:text-blue-800"
                                    >
                                        @if ($role === 'direktur')
                                            Pantau
                                        @elseif ($role === 'petugas-lapangan')
                                            Buka Pekerjaan
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
                                        Belum ada Produksi & Instalasi.
                                    </p>

                                    @if ($role === 'admin')
                                        <p class="mt-1 text-sm text-slate-500">
                                            Produksi & Instalasi dapat dibuat dari Planning yang telah selesai.
                                        </p>

                                        <a
                                            href="{{ route('productions.create') }}"
                                            class="mt-4 inline-flex rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                                        >
                                            + Buat Produksi & Instalasi
                                        </a>
                                    @elseif ($role === 'petugas-lapangan')
                                        <p class="mt-1 text-sm text-slate-500">
                                            Belum ada pekerjaan Produksi & Instalasi yang ditugaskan kepada Anda.
                                        </p>
                                    @else
                                        <p class="mt-1 text-sm text-slate-500">
                                            Belum ada data Produksi & Instalasi yang dapat dipantau.
                                        </p>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($productions->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $productions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection