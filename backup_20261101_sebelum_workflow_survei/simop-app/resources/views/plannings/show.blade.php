@extends('layouts.app')

@section('title', 'Detail Perencanaan | SIMOP')
@section('page-title', 'Detail Perencanaan')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;
        $project = $planning->project;
        $production = $planning->production;

        $projectProgress = max(
            0,
            min(100, (int) $project->progress)
        );

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

        $statusLabel = $statusLabels[$planning->status]
            ?? ucfirst(str_replace('_', ' ', $planning->status));

        $statusColor = $statusColors[$planning->status]
            ?? 'bg-slate-100 text-slate-700';

        $isFinal = in_array(
            $planning->status,
            ['completed', 'cancelled'],
            true
        );

        $projectIsFinal = in_array(
            $project->status,
            ['completed', 'cancelled'],
            true
        );

        $isAssignedOfficer =
            $role === 'petugas-lapangan'
            && $project->assigned_to === auth()->id();

        $canUpdateStatus =
            !$isFinal
            && !$projectIsFinal
            && (
                $role === 'admin'
                || $isAssignedOfficer
            );

        $nextStatus = match ($planning->status) {
            'draft' => 'ready',
            'ready' => 'in_progress',
            'in_progress' => 'completed',
            default => null,
        };

        $nextStatusLabel = match ($nextStatus) {
            'ready' => 'Tandai Siap',
            'in_progress' => 'Mulai Perencanaan',
            'completed' => 'Selesaikan Perencanaan',
            default => null,
        };

        $isLate =
            $planning->planned_end_date
            && $planning->planned_end_date->isPast()
            && !$isFinal;
    @endphp

    <div class="mx-auto max-w-6xl space-y-6">
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

        <div>
            <a
                href="{{ route('plannings.index') }}"
                class="text-sm font-semibold text-blue-600 hover:text-blue-800"
            >
                &larr; Kembali ke Perencanaan
            </a>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="text-sm font-semibold text-slate-500">
                            {{ $planning->planning_number }}
                        </p>

                        @if ($isLate)
                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                Terlambat
                            </span>
                        @endif
                    </div>

                    <h2 class="mt-2 text-2xl font-bold text-slate-900">
                        {{ $planning->title }}
                    </h2>

                    <p class="mt-2 text-sm text-slate-500">
                        Proyek:
                        <a
                            href="{{ route('projects.show', $project) }}"
                            class="font-semibold text-blue-600 hover:text-blue-800"
                        >
                            {{ $project->project_number }} — {{ $project->title }}
                        </a>
                    </p>
                </div>

                <span class="inline-flex w-fit rounded-full px-4 py-2 text-sm font-semibold {{ $statusColor }}">
                    {{ $statusLabel }}
                </span>
            </div>
        </div>

        @if ($planning->status === 'completed')
            <div class="rounded-xl border border-green-200 bg-green-50 p-5">
                <p class="font-semibold text-green-800">
                    Perencanaan Telah Selesai
                </p>

                <p class="mt-1 text-sm leading-6 text-green-700">
                    Tahap perencanaan telah selesai. Proyek dapat dilanjutkan ke proses Produksi & Instalasi.
                </p>
            </div>
        @elseif ($planning->status === 'cancelled')
            <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                <p class="font-semibold text-red-800">
                    Perencanaan Dibatalkan
                </p>

                <p class="mt-1 text-sm leading-6 text-red-700">
                    Perencanaan ini telah dibatalkan dan statusnya tidak dapat diubah kembali.
                </p>
            </div>
        @elseif ($project->status === 'cancelled')
            <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                <p class="font-semibold text-red-800">
                    Proyek Dibatalkan
                </p>

                <p class="mt-1 text-sm leading-6 text-red-700">
                    Perencanaan tidak dapat dilanjutkan karena proyek telah dibatalkan.
                </p>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900">
                    Informasi Perencanaan
                </h3>

                <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Nomor Perencanaan
                        </dt>

                        <dd class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $planning->planning_number }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Status
                        </dt>

                        <dd class="mt-2">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusColor }}">
                                {{ $statusLabel }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Rencana Mulai
                        </dt>

                        <dd class="mt-1 text-sm font-medium text-slate-800">
                            {{ $planning->planned_start_date?->format('d/m/Y') ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Rencana Selesai
                        </dt>

                        <dd class="mt-1 text-sm font-medium {{ $isLate ? 'text-red-600' : 'text-slate-800' }}">
                            {{ $planning->planned_end_date?->format('d/m/Y') ?? '-' }}

                            @if ($isLate)
                                <span class="ml-1 text-xs font-semibold">
                                    Terlambat
                                </span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Dibuat Oleh
                        </dt>

                        <dd class="mt-1 text-sm font-medium text-slate-800">
                            {{ $planning->creator?->name ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Petugas Lapangan
                        </dt>

                        <dd class="mt-1 text-sm font-medium text-slate-800">
                            {{ $project->assignedUser?->name ?? 'Belum ditentukan' }}
                        </dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Deskripsi
                        </dt>

                        <dd class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">
                            {{ $planning->description ?: 'Tidak ada deskripsi.' }}
                        </dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Catatan
                        </dt>

                        <dd class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">
                            {{ $planning->notes ?: 'Tidak ada catatan.' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">
                                Informasi Proyek
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Proyek yang menjadi sumber perencanaan.
                            </p>
                        </div>

                        <a
                            href="{{ route('projects.show', $project) }}"
                            class="text-sm font-semibold text-blue-600 hover:text-blue-800"
                        >
                            Detail Proyek
                        </a>
                    </div>

                    <div class="mt-5 space-y-5">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Nomor Proyek
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-800">
                                {{ $project->project_number }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Nama Proyek
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $project->title }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Pesanan
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $project->order?->order_number ?? '-' }}
                            </p>

                            @if ($project->order?->company_name)
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $project->order->company_name }}
                                </p>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Petugas Lapangan
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $project->assignedUser?->name ?? 'Belum ditentukan' }}
                            </p>
                        </div>

                        <div>
                            <div class="flex items-center justify-between gap-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Kemajuan Proyek
                                </p>

                                <span class="text-sm font-bold text-blue-600">
                                    {{ $projectProgress }}%
                                </span>
                            </div>

                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-200">
                                <div
                                    class="h-full rounded-full bg-blue-600"
                                    style="width: {{ $projectProgress }}%"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">
                        Status Perencanaan
                    </h3>

                    @if ($role === 'direktur')
                        <div class="mt-5 rounded-lg border border-blue-200 bg-blue-50 p-4">
                            <p class="text-sm font-semibold text-blue-700">
                                Mode Pemantauan
                            </p>

                            <p class="mt-1 text-xs leading-5 text-blue-600">
                                Direktur dapat memantau perencanaan tetapi tidak dapat mengubah status.
                            </p>
                        </div>
                    @elseif ($planning->status === 'completed')
                        <div class="mt-5 rounded-lg border border-green-200 bg-green-50 p-4">
                            <p class="text-sm font-semibold text-green-700">
                                Perencanaan Selesai
                            </p>

                            <p class="mt-1 text-xs leading-5 text-green-600">
                                Status ini bersifat final dan tidak dapat diubah kembali.
                            </p>
                        </div>
                    @elseif ($planning->status === 'cancelled')
                        <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4">
                            <p class="text-sm font-semibold text-red-700">
                                Perencanaan Dibatalkan
                            </p>

                            <p class="mt-1 text-xs leading-5 text-red-600">
                                Status ini bersifat final dan tidak dapat diubah kembali.
                            </p>
                        </div>
                    @elseif ($projectIsFinal)
                        <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4">
                            <p class="text-sm font-semibold text-red-700">
                                Perencanaan Dikunci
                            </p>

                            <p class="mt-1 text-xs leading-5 text-red-600">
                                Perencanaan tidak dapat diperbarui karena proyek sudah selesai atau dibatalkan.
                            </p>
                        </div>
                    @elseif ($canUpdateStatus)
                        <div class="mt-5 space-y-4">
                            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                    Status Saat Ini
                                </p>

                                <p class="mt-2 font-semibold text-slate-800">
                                    {{ $statusLabel }}
                                </p>

                                @if ($nextStatus)
                                    <p class="mt-2 text-sm text-slate-600">
                                        Tahap berikutnya:
                                        <span class="font-semibold">
                                            {{ $statusLabels[$nextStatus] }}
                                        </span>
                                    </p>
                                @endif
                            </div>

                            @if ($nextStatus)
                                <form
                                    method="POST"
                                    action="{{ route('plannings.update-status', $planning) }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <input
                                        type="hidden"
                                        name="status"
                                        value="{{ $nextStatus }}"
                                    >

                                    <button
                                        type="submit"
                                        onclick="return confirm('Lanjutkan status perencanaan ke {{ $statusLabels[$nextStatus] }}?')"
                                        class="w-full rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                                    >
                                        {{ $nextStatusLabel }}
                                    </button>
                                </form>
                            @endif

                            @if ($role === 'admin')
                                <form
                                    method="POST"
                                    action="{{ route('plannings.update-status', $planning) }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <input
                                        type="hidden"
                                        name="status"
                                        value="cancelled"
                                    >

                                    <button
                                        type="submit"
                                        onclick="return confirm('Batalkan perencanaan ini? Perencanaan yang dibatalkan tidak dapat diaktifkan kembali.')"
                                        class="w-full rounded-lg border border-red-300 bg-white px-5 py-3 text-sm font-semibold text-red-600 transition hover:bg-red-50"
                                    >
                                        Batalkan Perencanaan
                                    </button>
                                </form>
                            @endif

                            @if ($role === 'petugas-lapangan')
                                <p class="text-xs leading-5 text-slate-500">
                                    Petugas Lapangan hanya dapat melanjutkan tahapan perencanaan sesuai urutan proses.
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <h3 class="text-lg font-bold text-slate-900">
                    Alur Perencanaan
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Status perencanaan berjalan secara berurutan sebelum masuk ke Produksi & Instalasi.
                </p>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-3">
                @php
                    $steps = [
                        [
                            'status' => 'draft',
                            'label' => 'Draf',
                            'number' => 1,
                        ],
                        [
                            'status' => 'ready',
                            'label' => 'Siap',
                            'number' => 2,
                        ],
                        [
                            'status' => 'in_progress',
                            'label' => 'Dalam Proses',
                            'number' => 3,
                        ],
                        [
                            'status' => 'completed',
                            'label' => 'Selesai',
                            'number' => 4,
                        ],
                    ];

                    $statusOrder = [
                        'draft' => 1,
                        'ready' => 2,
                        'in_progress' => 3,
                        'completed' => 4,
                    ];

                    $currentOrder = $statusOrder[$planning->status] ?? 0;
                @endphp

                @foreach ($steps as $step)
                    @php
                        $stepReached =
                            $planning->status !== 'cancelled'
                            && $currentOrder >= $step['number'];

                        $stepCurrent =
                            $planning->status === $step['status'];
                    @endphp

                    <div class="rounded-xl border p-4
                        {{ $stepCurrent
                            ? 'border-blue-300 bg-blue-50'
                            : ($stepReached
                                ? 'border-green-200 bg-green-50'
                                : 'border-slate-200 bg-white') }}">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold
                                {{ $stepCurrent
                                    ? 'bg-blue-600 text-white'
                                    : ($stepReached
                                        ? 'bg-green-600 text-white'
                                        : 'bg-slate-100 text-slate-500') }}">
                                {{ $step['number'] }}
                            </div>

                            <div>
                                <p class="text-sm font-semibold
                                    {{ $stepCurrent
                                        ? 'text-blue-800'
                                        : ($stepReached
                                            ? 'text-green-800'
                                            : 'text-slate-600') }}">
                                    {{ $step['label'] }}
                                </p>

                                @if ($stepCurrent)
                                    <p class="mt-1 text-xs text-blue-600">
                                        Status saat ini
                                    </p>
                                @elseif ($stepReached)
                                    <p class="mt-1 text-xs text-green-600">
                                        Tahap terlewati
                                    </p>
                                @else
                                    <p class="mt-1 text-xs text-slate-400">
                                        Belum dimulai
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($planning->status === 'cancelled')
                <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-4">
                    <p class="text-sm font-semibold text-red-700">
                        Alur perencanaan dihentikan karena perencanaan dibatalkan.
                    </p>
                </div>
            @endif
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">
                        Produksi & Instalasi
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Tahap berikutnya setelah perencanaan selesai.
                    </p>
                </div>

                @if ($production)
                    @if ($production->status === 'completed')
                        <span class="inline-flex w-fit rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                            Selesai
                        </span>
                    @elseif ($production->status === 'in_progress')
                        <span class="inline-flex w-fit rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                            Dalam Pengerjaan
                        </span>
                    @elseif ($production->status === 'cancelled')
                        <span class="inline-flex w-fit rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                            Dibatalkan
                        </span>
                    @else
                        <span class="inline-flex w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                            Menunggu
                        </span>
                    @endif
                @endif
            </div>

            @if ($production)
                <div class="mt-5 rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">
                                Produksi telah dibuat
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Kemajuan Produksi: {{ (int) $production->progress }}%
                            </p>
                        </div>

                        <a
                            href="{{ route('productions.show', $production) }}"
                            class="text-sm font-semibold text-blue-600 hover:text-blue-800"
                        >
                            Buka Produksi
                        </a>
                    </div>
                </div>
            @elseif ($planning->status === 'completed')
                <div class="mt-5 rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <p class="text-sm font-semibold text-blue-800">
                        Perencanaan Selesai
                    </p>

                    <p class="mt-1 text-sm leading-6 text-blue-700">
                        Perencanaan telah selesai dan siap dilanjutkan ke tahap Produksi & Instalasi.
                    </p>

                    @if ($role === 'admin')
                        <a
                            href="{{ route('productions.create') }}"
                            class="mt-4 inline-flex rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                        >
                            Buat Produksi & Instalasi
                        </a>
                    @endif
                </div>
            @else
                <div class="mt-5 rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm text-slate-600">
                        Produksi & Instalasi baru dapat dibuat setelah perencanaan berstatus Selesai.
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection
