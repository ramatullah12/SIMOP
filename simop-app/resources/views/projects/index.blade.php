@extends('layouts.app')

@section('title', 'Project | SIMOP')
@section('page-title', 'Project')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;

        $statusLabels = [
            'planning' => 'Planning',
            'ongoing' => 'Dalam Pengerjaan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        $statusColors = [
            'planning' => 'bg-blue-100 text-blue-700',
            'ongoing' => 'bg-amber-100 text-amber-700',
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
                    Daftar Project
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    @if ($role === 'admin')
                        Kelola Project yang berasal dari Work Estimate yang telah disetujui Direktur.
                    @elseif ($role === 'direktur')
                        Pantau Project yang telah mendapatkan persetujuan dan sedang dalam proses pengerjaan.
                    @elseif ($role === 'petugas-lapangan')
                        Lihat Project yang ditugaskan kepada Anda dan pantau progress pengerjaan.
                    @endif
                </p>
            </div>

            @if ($role === 'admin')
                <a
                    href="{{ route('projects.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                    + Buat Project
                </a>
            @endif
        </div>

        @if ($role === 'direktur')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                <p class="text-sm font-semibold text-blue-800">
                    Monitoring Project
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Direktur dapat memantau Project, progress pekerjaan, Petugas Lapangan, dan status penyelesaian Project.
                </p>
            </div>
        @elseif ($role === 'petugas-lapangan')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                <p class="text-sm font-semibold text-blue-800">
                    Project Saya
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Daftar ini hanya menampilkan Project yang ditugaskan kepada Anda.
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
                                Project
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Order / Client
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Work Estimate
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
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($projects as $project)
                            @php
                                $statusLabel = $statusLabels[$project->status]
                                    ?? ucfirst(str_replace('_', ' ', $project->status));

                                $statusClass = $statusColors[$project->status]
                                    ?? 'bg-slate-100 text-slate-700';

                                $isLate = $project->deadline
                                    && $project->deadline->isPast()
                                    && !in_array(
                                        $project->status,
                                        ['completed', 'cancelled'],
                                        true
                                    );

                                $progress = max(
                                    0,
                                    min(100, (int) $project->progress)
                                );
                            @endphp

                            <tr class="align-top transition hover:bg-slate-50">
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                    {{ $projects->firstItem() + $loop->index }}
                                </td>

                                <td class="min-w-56 px-5 py-4">
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ $project->project_number }}
                                    </p>

                                    <p class="mt-1 text-sm font-medium text-slate-700">
                                        {{ $project->title }}
                                    </p>

                                    @if ($isLate)
                                        <span class="mt-2 inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                            Terlambat
                                        </span>
                                    @endif
                                </td>

                                <td class="min-w-48 px-5 py-4">
                                    <p class="text-sm font-medium text-slate-800">
                                        {{ $project->workEstimate?->order?->order_number ?? '-' }}
                                    </p>

                                    @if ($project->workEstimate?->order?->company_name)
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $project->workEstimate->order->company_name }}
                                        </p>
                                    @else
                                        <p class="mt-1 text-xs text-slate-400">
                                            Client tidak tersedia
                                        </p>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <p class="text-sm font-medium text-slate-700">
                                        {{ $project->workEstimate?->estimate_number ?? '-' }}
                                    </p>

                                    @if ($project->workEstimate)
                                        <p class="mt-1 text-xs font-semibold text-green-600">
                                            Disetujui Direktur
                                        </p>
                                    @endif
                                </td>

                                @if ($role !== 'petugas-lapangan')
                                    <td class="min-w-40 px-5 py-4">
                                        @if ($project->assignedUser)
                                            <p class="text-sm font-medium text-slate-800">
                                                {{ $project->assignedUser->name }}
                                            </p>

                                            <p class="mt-1 text-xs text-slate-500">
                                                Petugas Lapangan
                                            </p>
                                        @else
                                            <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                                Belum Ditentukan
                                            </span>
                                        @endif
                                    </td>
                                @endif

                                <td class="min-w-40 px-5 py-4">
                                    <div>
                                        <p class="text-xs text-slate-500">
                                            Mulai
                                        </p>

                                        <p class="mt-1 text-sm font-medium text-slate-700">
                                            {{ $project->start_date?->format('d/m/Y') ?? '-' }}
                                        </p>
                                    </div>

                                    <div class="mt-3">
                                        <p class="text-xs text-slate-500">
                                            Deadline
                                        </p>

                                        <p class="mt-1 text-sm font-medium {{ $isLate ? 'text-red-600' : 'text-slate-700' }}">
                                            {{ $project->deadline?->format('d/m/Y') ?? '-' }}
                                        </p>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>

                                    @if (
                                        $project->status === 'ongoing'
                                        && $progress === 100
                                    )
                                        <p class="mt-2 max-w-36 text-xs leading-5 text-amber-600">
                                            Menunggu penyelesaian BAPP
                                        </p>
                                    @endif
                                </td>

                                <td class="min-w-44 px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-200">
                                            <div
                                                class="h-full rounded-full bg-blue-600 transition-all"
                                                style="width: {{ $progress }}%"
                                            ></div>
                                        </div>

                                        <span class="w-10 text-right text-xs font-semibold text-slate-700">
                                            {{ $progress }}%
                                        </span>
                                    </div>

                                    @if ($project->status === 'completed')
                                        <p class="mt-2 text-xs font-semibold text-green-600">
                                            Project selesai
                                        </p>
                                    @elseif ($project->status === 'cancelled')
                                        <p class="mt-2 text-xs font-semibold text-red-600">
                                            Project dibatalkan
                                        </p>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <a
                                        href="{{ route('projects.show', $project) }}"
                                        class="text-sm font-semibold text-blue-600 hover:text-blue-800"
                                    >
                                        @if ($role === 'petugas-lapangan')
                                            Buka Project
                                        @elseif ($role === 'direktur')
                                            Pantau
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
                                    class="px-5 py-14 text-center"
                                >
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-500">
                                        ≡
                                    </div>

                                    <p class="mt-4 font-semibold text-slate-700">
                                        @if ($role === 'petugas-lapangan')
                                            Belum Ada Project yang Ditugaskan
                                        @else
                                            Belum Ada Project
                                        @endif
                                    </p>

                                    <p class="mx-auto mt-1 max-w-md text-sm leading-6 text-slate-500">
                                        @if ($role === 'admin')
                                            Project dapat dibuat dari Work Estimate yang telah mendapatkan persetujuan final Direktur.
                                        @elseif ($role === 'direktur')
                                            Belum ada Project yang tersedia untuk dipantau.
                                        @elseif ($role === 'petugas-lapangan')
                                            Project akan muncul setelah Admin menugaskan Project kepada Anda.
                                        @endif
                                    </p>

                                    @if ($role === 'admin')
                                        <a
                                            href="{{ route('projects.create') }}"
                                            class="mt-5 inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                                        >
                                            + Buat Project
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($projects->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection