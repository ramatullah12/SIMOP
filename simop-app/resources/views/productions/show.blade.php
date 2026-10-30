@extends('layouts.app')

@section('title', 'Detail Produksi & Instalasi | SIMOP')
@section('page-title', 'Detail Produksi & Instalasi')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;
        $planning = $production->planning;
        $project = $planning->project;
        $order = $project?->workEstimate?->order;
        $bapp = $production->bapp;

        $progress = max(
            0,
            min(100, (int) $production->progress)
        );

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

        $statusLabel = $statusLabels[$production->status]
            ?? ucfirst(str_replace('_', ' ', $production->status));

        $statusColor = $statusColors[$production->status]
            ?? 'bg-slate-100 text-slate-700';

        $isFinal = in_array(
            $production->status,
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
            && $production->assigned_to === auth()->id();

        $canUpdateProgress =
            !$isFinal
            && !$projectIsFinal
            && (
                $role === 'admin'
                || $isAssignedOfficer
            );

        $canManageStatus =
            $role === 'admin'
            && !$isFinal
            && !$projectIsFinal;

        $isLate =
            $production->end_date
            && $production->end_date->isPast()
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
                href="{{ route('productions.index') }}"
                class="text-sm font-semibold text-blue-600 hover:text-blue-800"
            >
                &larr; Kembali ke Produksi & Instalasi
            </a>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="text-sm font-semibold text-slate-500">
                            {{ $production->production_number }}
                        </p>

                        @if ($isLate)
                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                Terlambat
                            </span>
                        @endif
                    </div>

                    <h2 class="mt-2 text-2xl font-bold text-slate-900">
                        {{ $production->title }}
                    </h2>

                    <p class="mt-2 text-sm text-slate-500">
                        Planning:
                        <a
                            href="{{ route('plannings.show', $planning) }}"
                            class="font-semibold text-blue-600 hover:text-blue-800"
                        >
                            {{ $planning->planning_number }}
                        </a>
                    </p>
                </div>

                <span class="inline-flex w-fit rounded-full px-4 py-2 text-sm font-semibold {{ $statusColor }}">
                    {{ $statusLabel }}
                </span>
            </div>

            <div class="mt-6">
                <div class="mb-2 flex items-center justify-between gap-4">
                    <span class="text-sm font-semibold text-slate-700">
                        Progress Pekerjaan
                    </span>

                    <span class="text-lg font-bold text-blue-600">
                        {{ $progress }}%
                    </span>
                </div>

                <div class="h-3 overflow-hidden rounded-full bg-slate-200">
                    <div
                        class="h-full rounded-full bg-blue-600"
                        style="width: {{ $progress }}%"
                    ></div>
                </div>

                @if (
                    $production->status === 'completed'
                    && !$bapp
                )
                    <p class="mt-3 text-sm font-medium text-blue-600">
                        Produksi telah selesai dan siap dilanjutkan ke BAPP.
                    </p>
                @elseif ($bapp?->status === 'draft')
                    <p class="mt-3 text-sm font-medium text-slate-600">
                        BAPP telah dibuat dan masih berstatus Draft.
                    </p>
                @elseif ($bapp?->status === 'submitted')
                    <p class="mt-3 text-sm font-medium text-amber-600">
                        BAPP telah diajukan dan menunggu persetujuan.
                    </p>
                @elseif ($bapp?->status === 'approved')
                    <p class="mt-3 text-sm font-medium text-green-600">
                        BAPP telah disetujui dan Project telah dinyatakan selesai.
                    </p>
                @endif
            </div>
        </div>

        @if ($production->status === 'completed')
            <div class="rounded-xl border border-green-200 bg-green-50 p-5">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <h3 class="font-bold text-green-800">
                            Produksi & Instalasi Telah Selesai
                        </h3>

                        @if ($bapp)
                            <p class="mt-1 text-sm leading-6 text-green-700">
                                BAPP untuk pekerjaan ini telah dibuat. Lanjutkan proses melalui detail BAPP.
                            </p>
                        @else
                            <p class="mt-1 text-sm leading-6 text-green-700">
                                Progress pekerjaan telah mencapai 100%. Tahap berikutnya adalah pembuatan BAPP.
                            </p>
                        @endif
                    </div>

                    @if ($bapp)
                        <a
                            href="{{ route('bapps.show', $bapp) }}"
                            class="inline-flex shrink-0 items-center justify-center rounded-lg border border-green-300 bg-white px-4 py-2.5 text-sm font-semibold text-green-700 hover:bg-green-100"
                        >
                            Lihat BAPP
                        </a>
                    @elseif ($role === 'admin')
                        <a
                            href="{{ route('bapps.create') }}"
                            class="inline-flex shrink-0 items-center justify-center rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700"
                        >
                            Buat BAPP
                        </a>
                    @endif
                </div>
            </div>
        @elseif ($production->status === 'cancelled')
            <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                <h3 class="font-bold text-red-800">
                    Produksi & Instalasi Dibatalkan
                </h3>

                <p class="mt-1 text-sm leading-6 text-red-700">
                    Pekerjaan ini telah dibatalkan dan progress tidak dapat diperbarui kembali.
                </p>
            </div>
        @endif

        @if ($role === 'direktur')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <p class="text-sm font-semibold text-blue-800">
                    Mode Monitoring Direktur
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Direktur dapat memantau informasi dan progress Produksi & Instalasi, tetapi tidak dapat mengubah pekerjaan.
                </p>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900">
                    Informasi Pekerjaan
                </h3>

                <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Nomor Produksi
                        </dt>

                        <dd class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $production->production_number }}
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
                            Petugas Lapangan
                        </dt>

                        <dd class="mt-1 text-sm font-medium text-slate-800">
                            {{ $production->assignedUser?->name ?? 'Belum ditentukan' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Dibuat Oleh
                        </dt>

                        <dd class="mt-1 text-sm font-medium text-slate-800">
                            {{ $production->creator?->name ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Tanggal Mulai
                        </dt>

                        <dd class="mt-1 text-sm font-medium text-slate-800">
                            {{ $production->start_date?->format('d/m/Y') ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Tanggal Selesai
                        </dt>

                        <dd class="mt-1 text-sm font-medium {{ $isLate ? 'text-red-600' : 'text-slate-800' }}">
                            {{ $production->end_date?->format('d/m/Y') ?? '-' }}

                            @if ($isLate)
                                <span class="ml-1 text-xs font-semibold">
                                    Terlambat
                                </span>
                            @endif
                        </dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Deskripsi
                        </dt>

                        <dd class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">
                            {{ $production->description ?: 'Tidak ada deskripsi.' }}
                        </dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Catatan
                        </dt>

                        <dd class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">
                            {{ $production->notes ?: 'Tidak ada catatan.' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">
                        Project & Planning
                    </h3>

                    <div class="mt-5 space-y-5">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Project
                            </p>

                            <a
                                href="{{ route('projects.show', $project) }}"
                                class="mt-1 inline-block text-sm font-semibold text-blue-600 hover:text-blue-800"
                            >
                                {{ $project->project_number }} — {{ $project->title }}
                            </a>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Planning
                            </p>

                            <a
                                href="{{ route('plannings.show', $planning) }}"
                                class="mt-1 inline-block text-sm font-semibold text-blue-600 hover:text-blue-800"
                            >
                                {{ $planning->planning_number }} — {{ $planning->title }}
                            </a>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Work Estimate
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $project->workEstimate?->estimate_number ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Order
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $order?->order_number ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Petugas Project
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $project->assignedUser?->name ?? 'Belum ditentukan' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">
                        Update Progress
                    </h3>

                    @if ($canUpdateProgress)
                        <p class="mt-1 text-sm text-slate-500">
                            Progress menentukan status Produksi & Instalasi secara otomatis.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('productions.progress', $production) }}"
                            class="mt-5"
                        >
                            @csrf
                            @method('PATCH')

                            <label
                                for="progress"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                            >
                                Progress Pekerjaan
                            </label>

                            <div class="flex flex-col gap-3 sm:flex-row">
                                <div class="relative flex-1">
                                    <input
                                        type="number"
                                        id="progress"
                                        name="progress"
                                        min="0"
                                        max="100"
                                        value="{{ old('progress', $progress) }}"
                                        required
                                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 pr-10 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                    >

                                    <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-400">
                                        %
                                    </span>
                                </div>

                                <button
                                    type="submit"
                                    class="whitespace-nowrap rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                                >
                                    Update Progress
                                </button>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-2 text-xs sm:grid-cols-3">
                                <div class="rounded-lg bg-slate-50 p-3 text-slate-600">
                                    <span class="font-semibold">
                                        0%
                                    </span>
                                    = Menunggu
                                </div>

                                <div class="rounded-lg bg-amber-50 p-3 text-amber-700">
                                    <span class="font-semibold">
                                        1–99%
                                    </span>
                                    = Dalam Pengerjaan
                                </div>

                                <div class="rounded-lg bg-green-50 p-3 text-green-700">
                                    <span class="font-semibold">
                                        100%
                                    </span>
                                    = Selesai
                                </div>
                            </div>

                            <p class="mt-3 text-xs leading-5 text-slate-500">
                                Progress 100% menyelesaikan Produksi & Instalasi, tetapi Project baru selesai setelah BAPP disetujui.
                            </p>
                        </form>
                    @elseif ($role === 'direktur')
                        <div class="mt-5 rounded-lg border border-blue-200 bg-blue-50 p-4">
                            <p class="text-sm font-semibold text-blue-700">
                                Monitoring Progress
                            </p>

                            <p class="mt-1 text-xs leading-5 text-blue-600">
                                Direktur hanya dapat memantau progress pekerjaan.
                            </p>
                        </div>
                    @elseif ($production->status === 'completed')
                        <div class="mt-5 rounded-lg border border-green-200 bg-green-50 p-4">
                            <p class="text-sm font-semibold text-green-700">
                                Progress Terkunci di 100%
                            </p>

                            <p class="mt-1 text-xs leading-5 text-green-600">
                                Produksi & Instalasi telah selesai dan progress tidak dapat diubah kembali.
                            </p>
                        </div>
                    @elseif ($production->status === 'cancelled')
                        <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4">
                            <p class="text-sm font-semibold text-red-700">
                                Progress Dikunci
                            </p>

                            <p class="mt-1 text-xs leading-5 text-red-600">
                                Produksi & Instalasi telah dibatalkan.
                            </p>
                        </div>
                    @elseif ($projectIsFinal)
                        <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4">
                            <p class="text-sm font-semibold text-red-700">
                                Progress Dikunci
                            </p>

                            <p class="mt-1 text-xs leading-5 text-red-600">
                                Progress tidak dapat diperbarui karena Project sudah selesai atau dibatalkan.
                            </p>
                        </div>
                    @endif
                </div>

                @if ($role === 'admin')
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-slate-900">
                            Status Pekerjaan
                        </h3>

                        @if ($canManageStatus)
                            <p class="mt-1 text-sm text-slate-500">
                                Admin dapat memulai atau membatalkan pekerjaan. Status Selesai hanya diperoleh ketika progress mencapai 100%.
                            </p>

                            <div class="mt-5 space-y-3">
                                @if ($production->status === 'pending')
                                    <form
                                        method="POST"
                                        action="{{ route('productions.status', $production) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="in_progress"
                                        >

                                        <button
                                            type="submit"
                                            onclick="return confirm('Mulai Produksi & Instalasi ini?')"
                                            class="w-full rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700"
                                        >
                                            Mulai Produksi & Instalasi
                                        </button>
                                    </form>
                                @endif

                                <form
                                    method="POST"
                                    action="{{ route('productions.status', $production) }}"
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
                                        onclick="return confirm('Batalkan Produksi & Instalasi ini? Status yang dibatalkan tidak dapat diaktifkan kembali.')"
                                        class="w-full rounded-lg border border-red-300 bg-white px-5 py-3 text-sm font-semibold text-red-600 hover:bg-red-50"
                                    >
                                        Batalkan Produksi & Instalasi
                                    </button>
                                </form>
                            </div>
                        @elseif ($production->status === 'completed')
                            <div class="mt-5 rounded-lg border border-green-200 bg-green-50 p-4">
                                <p class="text-sm font-semibold text-green-700">
                                    Status Final: Selesai
                                </p>

                                <p class="mt-1 text-xs leading-5 text-green-600">
                                    Produksi & Instalasi selesai karena progress telah mencapai 100%.
                                </p>
                            </div>
                        @elseif ($production->status === 'cancelled')
                            <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4">
                                <p class="text-sm font-semibold text-red-700">
                                    Status Final: Dibatalkan
                                </p>

                                <p class="mt-1 text-xs leading-5 text-red-600">
                                    Pekerjaan yang dibatalkan tidak dapat diaktifkan kembali.
                                </p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">
                        BAPP
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Berita Acara Penyelesaian Pekerjaan dibuat setelah Produksi & Instalasi selesai.
                    </p>
                </div>

                @if ($bapp)
                    @if ($bapp->status === 'approved')
                        <span class="inline-flex w-fit rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                            Disetujui
                        </span>
                    @elseif ($bapp->status === 'submitted')
                        <span class="inline-flex w-fit rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                            Diajukan
                        </span>
                    @else
                        <span class="inline-flex w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                            Draft
                        </span>
                    @endif
                @endif
            </div>

            @if ($bapp)
                <div class="mt-5 rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">
                                {{ $bapp->bapp_number }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Tanggal penyelesaian:
                                {{ $bapp->completion_date?->format('d/m/Y') ?? '-' }}
                            </p>
                        </div>

                        <a
                            href="{{ route('bapps.show', $bapp) }}"
                            class="text-sm font-semibold text-blue-600 hover:text-blue-800"
                        >
                            Lihat BAPP
                        </a>
                    </div>
                </div>
            @elseif (
                $production->status === 'completed'
                && $progress === 100
            )
                <div class="mt-5 rounded-lg border border-green-200 bg-green-50 p-4">
                    <p class="text-sm font-semibold text-green-800">
                        Produksi & Instalasi Siap Dibuatkan BAPP
                    </p>

                    <p class="mt-1 text-sm leading-6 text-green-700">
                        Pekerjaan telah selesai 100%. BAPP dapat dibuat sebagai tahap penyelesaian Project.
                    </p>

                    @if ($role === 'admin')
                        <a
                            href="{{ route('bapps.create') }}"
                            class="mt-4 inline-flex rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700"
                        >
                            Buat BAPP
                        </a>
                    @endif
                </div>
            @else
                <div class="mt-5 rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm text-slate-600">
                        BAPP baru dapat dibuat setelah Produksi & Instalasi mencapai progress 100% dan berstatus Selesai.
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection