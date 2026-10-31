@extends('layouts.app')

@section('title', 'Perencanaan | SIMOP')
@section('page-title', 'Perencanaan')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;

        $statusLabels = [
            'draft' => 'Draf',
            'ready' => 'Siap',
            'in_progress' => 'Dalam Proses',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        $statusColors = [
            'draft' => 'bg-slate-100 text-slate-700',
            'ready' => 'bg-blue-100 text-blue-700',
            'in_progress' => 'bg-amber-100 text-amber-700',
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
                    Daftar Perencanaan
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    @if ($role === 'admin')
                        Kelola perencanaan pekerjaan dari setiap proyek yang aktif.
                    @elseif ($role === 'direktur')
                        Pantau perencanaan dan kesiapan pelaksanaan setiap proyek.
                    @elseif ($role === 'petugas-lapangan')
                        Lihat dan jalankan perencanaan dari proyek yang ditugaskan kepada Anda.
                    @endif
                </p>
            </div>

            @if ($role === 'admin')
                <a
                    href="{{ route('plannings.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                    + Buat Perencanaan
                </a>
            @endif
        </div>

        @if ($role === 'direktur')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                <p class="text-sm font-semibold text-blue-800">
                    Pemantauan Perencanaan
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Direktur dapat melihat perkembangan perencanaan setiap proyek, tetapi tidak dapat mengubah status perencanaan.
                </p>
            </div>
        @elseif ($role === 'petugas-lapangan')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                <p class="text-sm font-semibold text-blue-800">
                    Perencanaan Saya
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Hanya perencanaan dari proyek yang ditugaskan kepada Anda yang ditampilkan pada halaman ini.
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
                                Perencanaan
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Proyek
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Pesanan
                            </th>

                            @if ($role !== 'petugas-lapangan')
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Petugas
                                </th>
                            @endif

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Periode
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Status
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Produksi
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($plannings as $planning)
                            @php
                                $project = $planning->project;
                                $production = $planning->production;

                                $statusLabel = $statusLabels[$planning->status]
                                    ?? ucfirst(str_replace('_', ' ', $planning->status));

                                $statusClass = $statusColors[$planning->status]
                                    ?? 'bg-slate-100 text-slate-700';

                                $isLate =
                                    $planning->planned_end_date
                                    && $planning->planned_end_date->isPast()
                                    && !in_array(
                                        $planning->status,
                                        ['completed', 'cancelled'],
                                        true
                                    );
                            @endphp

                            <tr class="align-top transition hover:bg-slate-50">
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                    {{ $plannings->firstItem() + $loop->index }}
                                </td>

                                <td class="min-w-52 px-5 py-4">
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ $planning->planning_number }}
                                    </p>

                                    <p class="mt-1 text-sm text-slate-700">
                                        {{ $planning->title }}
                                    </p>

                                    @if ($isLate)
                                        <span class="mt-2 inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                            Terlambat
                                        </span>
                                    @endif
                                </td>

                                <td class="min-w-48 px-5 py-4">
                                    <p class="text-sm font-medium text-slate-800">
                                        {{ $project?->title ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $project?->project_number ?? '-' }}
                                    </p>
                                </td>

                                <td class="min-w-40 px-5 py-4">
                                    <p class="text-sm font-medium text-slate-700">
                                        {{ $project?->order?->order_number ?? '-' }}
                                    </p>

                                    @if ($project?->order?->company_name)
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $project->order->company_name }}
                                        </p>
                                    @endif
                                </td>

                                @if ($role !== 'petugas-lapangan')
                                    <td class="min-w-40 px-5 py-4">
                                        @if ($project?->assignedUser)
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

                                <td class="min-w-36 px-5 py-4">
                                    <div>
                                        <p class="text-xs text-slate-500">
                                            Mulai
                                        </p>

                                        <p class="mt-1 text-sm font-medium text-slate-700">
                                            {{ $planning->planned_start_date?->format('d/m/Y') ?? '-' }}
                                        </p>
                                    </div>

                                    <div class="mt-3">
                                        <p class="text-xs text-slate-500">
                                            Selesai
                                        </p>

                                        <p class="mt-1 text-sm font-medium {{ $isLate ? 'text-red-600' : 'text-slate-700' }}">
                                            {{ $planning->planned_end_date?->format('d/m/Y') ?? '-' }}
                                        </p>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td class="min-w-36 px-5 py-4">
                                    @if ($production)
                                        @if ($production->status === 'completed')
                                            <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                                Selesai
                                            </span>
                                        @elseif ($production->status === 'in_progress')
                                            <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                                Berjalan
                                            </span>
                                        @elseif ($production->status === 'cancelled')
                                            <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                                Dibatalkan
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                                Menunggu
                                            </span>
                                        @endif

                                        <p class="mt-2 text-xs text-slate-500">
                                            Kemajuan {{ (int) $production->progress }}%
                                        </p>
                                    @elseif ($planning->status === 'completed')
                                        <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                            Siap Produksi
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">
                                            Belum Tersedia
                                        </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <a
                                        href="{{ route('plannings.show', $planning) }}"
                                        class="text-sm font-semibold text-blue-600 hover:text-blue-800"
                                    >
                                        @if ($role === 'direktur')
                                            Pantau
                                        @elseif ($role === 'petugas-lapangan')
                                            Buka Perencanaan
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
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl font-semibold text-slate-500">
                                        ≡
                                    </div>

                                    <p class="mt-4 font-semibold text-slate-700">
                                        @if ($role === 'petugas-lapangan')
                                            Belum Ada Perencanaan yang Ditugaskan
                                        @else
                                            Belum Ada Perencanaan
                                        @endif
                                    </p>

                                    <p class="mx-auto mt-1 max-w-md text-sm leading-6 text-slate-500">
                                        @if ($role === 'admin')
                                            Perencanaan dapat dibuat dari proyek aktif yang belum memiliki perencanaan.
                                        @elseif ($role === 'direktur')
                                            Belum ada perencanaan proyek yang tersedia untuk dipantau.
                                        @elseif ($role === 'petugas-lapangan')
                                            Perencanaan akan muncul setelah Admin membuat perencanaan untuk proyek yang ditugaskan kepada Anda.
                                        @endif
                                    </p>

                                    @if ($role === 'admin')
                                        <a
                                            href="{{ route('plannings.create') }}"
                                            class="mt-5 inline-flex rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                                        >
                                            + Buat Perencanaan
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($plannings->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $plannings->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
