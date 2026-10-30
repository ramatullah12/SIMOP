@extends('layouts.app')

@section('title', 'Detail Project | SIMOP')
@section('page-title', 'Detail Project')

@section('content')
<div class="mx-auto max-w-6xl space-y-6">

    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
            <ul class="list-inside list-disc text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $role = auth()->user()?->role?->slug;
        $planning = $project->planning;
        $production = $planning?->production;
        $bapp = $production?->bapp;
        $invoice = $project->invoice;

        $canUpdateProgress =
            in_array($role, ['admin', 'petugas-lapangan'], true)
            && $project->status !== 'completed'
            && $project->status !== 'cancelled'
            && (
                $role === 'admin'
                || $project->assigned_to === auth()->id()
            );

        $canUpdateStatus =
            $role === 'admin'
            && $project->status !== 'completed';

        $bappApproved =
            $bapp
            && $bapp->status === 'approved';
    @endphp

    <div>
        <a
            href="{{ route('projects.index') }}"
            class="text-sm font-semibold text-blue-600 hover:text-blue-800"
        >
            ← Kembali ke Project
        </a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">
                    {{ $project->project_number }}
                </p>

                <h2 class="mt-1 text-2xl font-bold text-slate-900">
                    {{ $project->title }}
                </h2>

                <p class="mt-2 text-sm text-slate-500">
                    Work Estimate:
                    <span class="font-semibold text-slate-700">
                        {{ $project->workEstimate->estimate_number ?? '-' }}
                    </span>
                </p>
            </div>

            <div>
                @if ($project->status === 'planning')
                    <span class="rounded-full bg-blue-100 px-4 py-2 text-sm font-semibold text-blue-700">
                        Planning
                    </span>
                @elseif ($project->status === 'ongoing')
                    <span class="rounded-full bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-700">
                        Dalam Pengerjaan
                    </span>
                @elseif ($project->status === 'completed')
                    <span class="rounded-full bg-green-100 px-4 py-2 text-sm font-semibold text-green-700">
                        Selesai
                    </span>
                @elseif ($project->status === 'cancelled')
                    <span class="rounded-full bg-red-100 px-4 py-2 text-sm font-semibold text-red-700">
                        Dibatalkan
                    </span>
                @else
                    <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                        {{ ucfirst($project->status) }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900">
                Informasi Project
            </h3>

            <div class="mt-5 space-y-4">
                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Nomor Project
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $project->project_number }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Petugas Lapangan
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $project->assignedUser->name ?? 'Belum ditentukan' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Tanggal Mulai
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $project->start_date?->format('d/m/Y') ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Deadline
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $project->deadline?->format('d/m/Y') ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Dibuat Oleh
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $project->creator->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Catatan
                    </p>

                    <p class="mt-1 whitespace-pre-line text-sm text-slate-700">
                        {{ $project->notes ?: '-' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Progress Project
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Progress pekerjaan Project saat ini.
                        </p>
                    </div>

                    <span class="text-2xl font-bold text-blue-600">
                        {{ $project->progress }}%
                    </span>
                </div>

                <div class="mt-5">
                    <div class="h-3 overflow-hidden rounded-full bg-slate-200">
                        <div
                            class="h-full rounded-full bg-blue-600"
                            style="width: {{ min(100, max(0, $project->progress)) }}%"
                        ></div>
                    </div>
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
                                value="{{ old('progress', $project->progress) }}"
                                required
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            >

                            <button
                                type="submit"
                                class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                            >
                                Simpan
                            </button>
                        </div>

                        <p class="mt-3 text-xs leading-5 text-slate-500">
                            Progress 100% tidak otomatis menyelesaikan Project. Project dinyatakan selesai setelah BAPP disetujui.
                        </p>
                    </form>
                @elseif ($project->status === 'completed')
                    <div class="mt-5 rounded-lg border border-green-200 bg-green-50 p-4">
                        <p class="text-sm font-semibold text-green-700">
                            Project telah selesai.
                        </p>

                        <p class="mt-1 text-xs leading-5 text-green-600">
                            Progress dikunci karena BAPP telah disetujui dan Project telah dinyatakan selesai.
                        </p>
                    </div>
                @elseif ($project->status === 'cancelled')
                    <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4">
                        <p class="text-sm font-semibold text-red-700">
                            Project dibatalkan.
                        </p>

                        <p class="mt-1 text-xs leading-5 text-red-600">
                            Progress tidak dapat diperbarui selama Project berstatus dibatalkan.
                        </p>
                    </div>
                @endif
            </div>

            @if ($role === 'admin')
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">
                        Status Project
                    </h3>

                    @if ($canUpdateStatus)
                        <p class="mt-1 text-sm text-slate-500">
                            Status Selesai tidak dapat dipilih secara manual.
                        </p>

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
                                Ubah Status
                            </label>

                            <div class="flex flex-col gap-3 sm:flex-row">
                                <select
                                    id="status"
                                    name="status"
                                    class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
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
                                Project otomatis berstatus Selesai setelah BAPP mendapatkan persetujuan.
                            </p>
                        </div>
                    @else
                        <div class="mt-5 rounded-lg border border-green-200 bg-green-50 p-4">
                            <p class="text-sm font-semibold text-green-700">
                                Status Project: Selesai
                            </p>

                            <p class="mt-1 text-xs leading-5 text-green-600">
                                Status telah dikunci dan tidak dapat diubah secara manual.
                            </p>
                        </div>
                    @endif
                </div>
            @endif

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900">
                    Tahapan Operasional
                </h3>

                <div class="mt-5 space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-slate-600">
                            Planning
                        </span>

                        @if ($planning)
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                {{ ucfirst(str_replace('_', ' ', $planning->status)) }}
                            </span>
                        @else
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">
                                Belum Ada
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-slate-600">
                            Produksi & Instalasi
                        </span>

                        @if ($production)
                            @if ($production->status === 'completed')
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                    Selesai
                                </span>
                            @elseif ($production->status === 'in_progress')
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                    Dalam Pengerjaan
                                </span>
                            @elseif ($production->status === 'cancelled')
                                <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                    Dibatalkan
                                </span>
                            @else
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                    Menunggu
                                </span>
                            @endif
                        @else
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">
                                Belum Ada
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-slate-600">
                            BAPP
                        </span>

                        @if ($bappApproved)
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                Disetujui
                            </span>
                        @elseif ($bapp?->status === 'submitted')
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                Diajukan
                            </span>
                        @elseif ($bapp)
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                Draft
                            </span>
                        @else
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">
                                Belum Ada
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-slate-600">
                            Invoice
                        </span>

                        @if ($invoice?->status === 'paid')
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                Lunas
                            </span>
                        @elseif ($invoice?->status === 'issued')
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                Diterbitkan
                            </span>
                        @elseif ($invoice?->status === 'cancelled')
                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                Dibatalkan
                            </span>
                        @elseif ($invoice)
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                Draft
                            </span>
                        @else
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">
                                Belum Ada
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-bold text-slate-900">
            Riwayat Persetujuan
        </h3>

        <p class="mt-1 text-sm text-slate-500">
            Persetujuan final Work Estimate dilakukan oleh Direktur.
        </p>

        <div class="mt-5 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                            Tahap
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                            Status
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                            Diproses Oleh
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                            Tanggal
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                            Catatan
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse (
                        $project->workEstimate->approvals
                            ->where('stage', 'direktur')
                        as $approval
                    )
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-slate-700">
                                Direktur
                            </td>

                            <td class="px-4 py-3">
                                @if ($approval->status === 'approved')
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Disetujui
                                    </span>
                                @elseif ($approval->status === 'rejected')
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                        Menunggu
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ $approval->approver->name ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ $approval->approved_at?->format('d/m/Y H:i') ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-sm text-slate-600">
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