@extends('layouts.app')

@section('title', 'Detail Project | SIMOP')
@section('page-title', 'Detail Project')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;
        $planning = $project->planning;
        $production = $planning?->production;
        $bapp = $production?->bapp;
        $invoice = $project->invoice;
        $rating = $project->rating;
        $workEstimate = $project->workEstimate;
        $order = $workEstimate?->order;

        $progress = max(
            0,
            min(100, (int) $project->progress)
        );

        $canUpdateProgress =
            in_array($role, ['admin', 'petugas-lapangan'], true)
            && !in_array(
                $project->status,
                ['completed', 'cancelled'],
                true
            )
            && (
                $role === 'admin'
                || $project->assigned_to === auth()->id()
            );

        $canUpdateStatus =
            $role === 'admin'
            && !in_array(
                $project->status,
                ['completed', 'cancelled'],
                true
            );

        $bappApproved =
            $bapp
            && $bapp->status === 'approved';

        $isLate =
            $project->deadline
            && $project->deadline->isPast()
            && !in_array(
                $project->status,
                ['completed', 'cancelled'],
                true
            );

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

        $statusLabel = $statusLabels[$project->status]
            ?? ucfirst(str_replace('_', ' ', $project->status));

        $statusColor = $statusColors[$project->status]
            ?? 'bg-slate-100 text-slate-700';
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
                href="{{ route('projects.index') }}"
                class="text-sm font-semibold text-blue-600 hover:text-blue-800"
            >
                &larr; Kembali ke Project
            </a>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="text-sm font-semibold text-slate-500">
                            {{ $project->project_number }}
                        </p>

                        @if ($isLate)
                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                Terlambat
                            </span>
                        @endif
                    </div>

                    <h2 class="mt-2 text-2xl font-bold text-slate-900">
                        {{ $project->title }}
                    </h2>

                    <div class="mt-3 flex flex-wrap gap-x-6 gap-y-2 text-sm text-slate-500">
                        <p>
                            Work Estimate:
                            <span class="font-semibold text-slate-700">
                                {{ $workEstimate?->estimate_number ?? '-' }}
                            </span>
                        </p>

                        <p>
                            Order:
                            <span class="font-semibold text-slate-700">
                                {{ $order?->order_number ?? '-' }}
                            </span>
                        </p>
                    </div>
                </div>

                <span class="inline-flex w-fit rounded-full px-4 py-2 text-sm font-semibold {{ $statusColor }}">
                    {{ $statusLabel }}
                </span>
            </div>
        </div>

        @if ($project->status === 'completed')
            <div class="rounded-xl border border-green-200 bg-green-50 p-5">
                <p class="font-semibold text-green-800">
                    Project Telah Selesai
                </p>

                <p class="mt-1 text-sm leading-6 text-green-700">
                    BAPP telah mendapatkan persetujuan dan Project telah dinyatakan selesai dengan progress 100%.
                </p>
            </div>
        @elseif ($project->status === 'cancelled')
            <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                <p class="font-semibold text-red-800">
                    Project Dibatalkan
                </p>

                <p class="mt-1 text-sm leading-6 text-red-700">
                    Project ini telah dibatalkan dan proses operasional tidak dapat dilanjutkan.
                </p>
            </div>
        @elseif ($progress === 100 && !$bappApproved)
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                <p class="font-semibold text-amber-800">
                    Progress Mencapai 100%
                </p>

                <p class="mt-1 text-sm leading-6 text-amber-700">
                    Progress pekerjaan telah mencapai 100%, tetapi Project belum dinyatakan selesai sampai BAPP mendapatkan persetujuan.
                </p>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
                <h3 class="text-lg font-bold text-slate-900">
                    Informasi Project
                </h3>

                <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-slate-500">
                            Nomor Project
                        </dt>

                        <dd class="mt-1 font-semibold text-slate-900">
                            {{ $project->project_number }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Nama Project
                        </dt>

                        <dd class="mt-1 font-semibold text-slate-900">
                            {{ $project->title }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Nomor Order
                        </dt>

                        <dd class="mt-1 font-medium text-slate-800">
                            {{ $order?->order_number ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Client / Perusahaan
                        </dt>

                        <dd class="mt-1 font-medium text-slate-800">
                            {{ $order?->company_name ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Work Estimate
                        </dt>

                        <dd class="mt-1 font-medium text-slate-800">
                            {{ $workEstimate?->estimate_number ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Nilai Work Estimate
                        </dt>

                        <dd class="mt-1 font-semibold text-slate-900">
                            Rp{{ number_format(
                                (float) ($workEstimate?->total_amount ?? 0),
                                0,
                                ',',
                                '.'
                            ) }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Petugas Lapangan
                        </dt>

                        <dd class="mt-1 font-medium text-slate-800">
                            {{ $project->assignedUser?->name ?? 'Belum ditentukan' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Dibuat Oleh
                        </dt>

                        <dd class="mt-1 font-medium text-slate-800">
                            {{ $project->creator?->name ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Tanggal Mulai
                        </dt>

                        <dd class="mt-1 font-medium text-slate-800">
                            {{ $project->start_date?->format('d/m/Y') ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Deadline
                        </dt>

                        <dd class="mt-1 font-medium {{ $isLate ? 'text-red-600' : 'text-slate-800' }}">
                            {{ $project->deadline?->format('d/m/Y') ?? '-' }}

                            @if ($isLate)
                                <span class="ml-1 text-xs font-semibold">
                                    Terlambat
                                </span>
                            @endif
                        </dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-sm text-slate-500">
                            Catatan
                        </dt>

                        <dd class="mt-1 whitespace-pre-line text-slate-700">
                            {{ $project->notes ?: 'Tidak ada catatan.' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Progress
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Progress pekerjaan saat ini.
                        </p>
                    </div>

                    <span class="text-2xl font-bold text-blue-600">
                        {{ $progress }}%
                    </span>
                </div>

                <div class="mt-5 h-3 overflow-hidden rounded-full bg-slate-200">
                    <div
                        class="h-full rounded-full bg-blue-600 transition-all"
                        style="width: {{ $progress }}%"
                    ></div>
                </div>

                @if ($canUpdateProgress)
                    <form
                        method="POST"
                        action="{{ route('projects.progress', $project) }}"
                        class="mt-6"
                    >
                        @csrf
                        @method('PATCH')

                        <label
                            for="progress"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Update Progress
                        </label>

                        <div class="flex gap-3">
                            <input
                                type="number"
                                id="progress"
                                name="progress"
                                min="0"
                                max="100"
                                value="{{ old('progress', $progress) }}"
                                required
                                class="min-w-0 flex-1 rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            >

                            <button
                                type="submit"
                                class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                            >
                                Simpan
                            </button>
                        </div>

                        <p class="mt-3 text-xs leading-5 text-slate-500">
                            Progress 100% tidak otomatis menyelesaikan Project. Project selesai setelah BAPP disetujui.
                        </p>
                    </form>
                @elseif ($role === 'direktur')
                    <div class="mt-5 rounded-lg border border-blue-200 bg-blue-50 p-4">
                        <p class="text-sm font-semibold text-blue-700">
                            Mode Monitoring
                        </p>

                        <p class="mt-1 text-xs leading-5 text-blue-600">
                            Direktur dapat memantau progress tetapi tidak mengubah progress Project.
                        </p>
                    </div>
                @elseif ($project->status === 'completed')
                    <div class="mt-5 rounded-lg border border-green-200 bg-green-50 p-4">
                        <p class="text-sm font-semibold text-green-700">
                            Progress Dikunci
                        </p>

                        <p class="mt-1 text-xs leading-5 text-green-600">
                            Project telah selesai setelah BAPP disetujui.
                        </p>
                    </div>
                @elseif ($project->status === 'cancelled')
                    <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4">
                        <p class="text-sm font-semibold text-red-700">
                            Progress Dikunci
                        </p>

                        <p class="mt-1 text-xs leading-5 text-red-600">
                            Project yang dibatalkan tidak dapat diperbarui.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        @if ($role === 'admin')
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Pengaturan Status Project
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Admin dapat mengatur status operasional Project. Status Selesai tidak dapat dipilih secara manual.
                        </p>
                    </div>

                    <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $statusColor }}">
                        {{ $statusLabel }}
                    </span>
                </div>

                @if ($canUpdateStatus)
                    <form
                        method="POST"
                        action="{{ route('projects.status', $project) }}"
                        class="mt-5"
                    >
                        @csrf
                        @method('PATCH')

                        <label
                            for="status"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Status Project
                        </label>

                        <div class="flex flex-col gap-3 sm:flex-row">
                            <select
                                id="status"
                                name="status"
                                class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            >
                                <option
                                    value="planning"
                                    @selected(old('status', $project->status) === 'planning')
                                >
                                    Planning
                                </option>

                                <option
                                    value="ongoing"
                                    @selected(old('status', $project->status) === 'ongoing')
                                >
                                    Dalam Pengerjaan
                                </option>

                                <option
                                    value="cancelled"
                                    @selected(old('status', $project->status) === 'cancelled')
                                >
                                    Dibatalkan
                                </option>
                            </select>

                            <button
                                type="submit"
                                onclick="return confirm('Ubah status Project ini?')"
                                class="rounded-lg bg-slate-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-900"
                            >
                                Update Status
                            </button>
                        </div>
                    </form>

                    <div class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-4">
                        <p class="text-sm font-semibold text-blue-700">
                            Penyelesaian Project
                        </p>

                        <p class="mt-1 text-xs leading-5 text-blue-600">
                            Project hanya berubah menjadi Selesai secara otomatis setelah BAPP mendapatkan persetujuan.
                        </p>
                    </div>
                @elseif ($project->status === 'completed')
                    <div class="mt-5 rounded-lg border border-green-200 bg-green-50 p-4">
                        <p class="text-sm font-semibold text-green-700">
                            Project Telah Selesai
                        </p>

                        <p class="mt-1 text-sm text-green-600">
                            Status dikunci karena BAPP telah disetujui.
                        </p>
                    </div>
                @elseif ($project->status === 'cancelled')
                    <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4">
                        <p class="text-sm font-semibold text-red-700">
                            Project Telah Dibatalkan
                        </p>

                        <p class="mt-1 text-sm text-red-600">
                            Status Project yang telah dibatalkan dikunci dan tidak dapat diubah kembali.
                        </p>
                    </div>
                @endif
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <h3 class="text-lg font-bold text-slate-900">
                    Tahapan Operasional
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Ringkasan tahapan pekerjaan Project sampai proses administrasi akhir.
                </p>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-sm font-semibold text-slate-900">
                        Planning
                    </p>

                    <div class="mt-3">
                        @if ($planning)
                            @if ($planning->status === 'completed')
                                <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                    Selesai
                                </span>
                            @elseif ($planning->status === 'in_progress')
                                <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                    Dalam Proses
                                </span>
                            @elseif ($planning->status === 'ready')
                                <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                    Siap
                                </span>
                            @elseif ($planning->status === 'cancelled')
                                <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                    Dibatalkan
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                    Draft
                                </span>
                            @endif

                            <p class="mt-3 text-xs text-slate-500">
                                {{ $planning->planning_number }}
                            </p>
                        @else
                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">
                                Belum Ada
                            </span>
                        @endif
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-sm font-semibold text-slate-900">
                        Produksi & Instalasi
                    </p>

                    <div class="mt-3">
                        @if ($production)
                            @if ($production->status === 'completed')
                                <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                    Selesai
                                </span>
                            @elseif ($production->status === 'in_progress')
                                <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                    Dalam Pengerjaan
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

                            <p class="mt-3 text-xs text-slate-500">
                                Progress {{ (int) $production->progress }}%
                            </p>
                        @else
                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">
                                Belum Ada
                            </span>
                        @endif
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-sm font-semibold text-slate-900">
                        BAPP
                    </p>

                    <div class="mt-3">
                        @if ($bapp?->status === 'approved')
                            <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                Disetujui
                            </span>
                        @elseif ($bapp?->status === 'submitted')
                            <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                Diajukan
                            </span>
                        @elseif ($bapp)
                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                Draft
                            </span>
                        @else
                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">
                                Belum Ada
                            </span>
                        @endif

                        @if ($bapp)
                            <p class="mt-3 text-xs text-slate-500">
                                {{ $bapp->bapp_number }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-sm font-semibold text-slate-900">
                        Invoice
                    </p>

                    <div class="mt-3">
                        @if ($invoice?->status === 'paid')
                            <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                Lunas
                            </span>
                        @elseif ($invoice?->status === 'issued')
                            <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                Diterbitkan
                            </span>
                        @elseif ($invoice?->status === 'cancelled')
                            <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                Dibatalkan
                            </span>
                        @elseif ($invoice)
                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                Draft
                            </span>
                        @else
                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">
                                Belum Ada
                            </span>
                        @endif

                        @if ($invoice)
                            <p class="mt-3 text-xs text-slate-500">
                                {{ $invoice->invoice_number }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            @if ($invoice?->status === 'paid')
                <div class="mt-4 rounded-lg border border-green-200 bg-green-50 p-4">
                    @if ($rating)
                        <p class="text-sm font-semibold text-green-800">
                            Client Telah Memberikan Rating
                        </p>

                        <p class="mt-1 text-sm text-green-700">
                            Rating: {{ $rating->rating }}/5
                        </p>
                    @else
                        <p class="text-sm font-semibold text-green-800">
                            Menunggu Rating Client
                        </p>

                        <p class="mt-1 text-sm text-green-700">
                            Invoice telah lunas. Client sudah dapat memberikan rating dan review terhadap Project.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900">
                Riwayat Persetujuan Work Estimate
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Persetujuan final Work Estimate dilakukan oleh Direktur sebelum Project dibuat.
            </p>

            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Tahap
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Status
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Diproses Oleh
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Tanggal
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Catatan
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse (
                            $workEstimate->approvals
                                ->where('stage', 'direktur')
                                ->sortByDesc('created_at')
                            as $approval
                        )
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-slate-700">
                                    Direktur
                                </td>

                                <td class="px-4 py-3">
                                    @if ($approval->status === 'approved')
                                        <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                            Disetujui
                                        </span>
                                    @elseif ($approval->status === 'rejected')
                                        <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                            Menunggu
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-sm text-slate-600">
                                    {{ $approval->approver?->name ?? '-' }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-600">
                                    {{ $approval->approved_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>

                                <td class="min-w-56 px-4 py-3 text-sm text-slate-600">
                                    {{ $approval->notes ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="5"
                                    class="px-4 py-8 text-center text-sm text-slate-500"
                                >
                                    Belum ada riwayat persetujuan Direktur.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection