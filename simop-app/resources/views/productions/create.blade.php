@extends('layouts.app')

@section('title', 'Buat Produksi & Instalasi | SIMOP')
@section('page-title', 'Buat Produksi & Instalasi')

@section('content')
    @php
        $planningData = $plannings->map(function ($planning) {
            $project = $planning->project;
            $order = $project?->workEstimate?->order;

            return [
                'id' => $planning->id,
                'planning_number' => $planning->planning_number,
                'planning_title' => $planning->title,
                'project_number' => $project?->project_number ?? '-',
                'project_title' => $project?->title ?? '-',
                'order_number' => $order?->order_number ?? '-',
                'company_name' => $order?->company_name ?? '-',
                'project_officer_id' => $project?->assigned_to,
                'project_officer' => $project?->assignedUser?->name ?? 'Belum ditentukan',
                'planned_start_date' => $planning->planned_start_date?->format('d/m/Y') ?? '-',
                'planned_end_date' => $planning->planned_end_date?->format('d/m/Y') ?? '-',
            ];
        })->values();

        $oldPlanningId = (string) old('planning_id', '');
        $oldAssignedTo = (string) old('assigned_to', '');
    @endphp

    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <a
                href="{{ route('productions.index') }}"
                class="text-sm font-semibold text-blue-600 hover:text-blue-800"
            >
                &larr; Kembali ke Produksi & Instalasi
            </a>

            <h2 class="mt-3 text-2xl font-bold text-slate-900">
                Buat Produksi & Instalasi
            </h2>

            <p class="mt-1 text-sm leading-6 text-slate-500">
                Buat pekerjaan Produksi & Instalasi berdasarkan Planning yang telah selesai.
            </p>
        </div>

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

        @if ($plannings->isEmpty())
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-6">
                <div class="flex gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 font-bold text-amber-700">
                        !
                    </div>

                    <div>
                        <h3 class="font-semibold text-amber-800">
                            Belum Ada Planning yang Tersedia
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-amber-700">
                            Produksi & Instalasi hanya dapat dibuat dari Planning berstatus Selesai yang belum memiliki Produksi & Instalasi dan Project-nya masih aktif.
                        </p>

                        <a
                            href="{{ route('plannings.index') }}"
                            class="mt-3 inline-flex text-sm font-semibold text-amber-800 underline hover:text-amber-900"
                        >
                            Lihat Planning
                        </a>
                    </div>
                </div>
            </div>
        @else
            <form
                method="POST"
                action="{{ route('productions.store') }}"
                class="space-y-6"
            >
                @csrf

                <div class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Sumber Planning
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Pilih Planning yang telah selesai untuk dilanjutkan ke tahap Produksi & Instalasi.
                        </p>
                    </div>

                    <div>
                        <label
                            for="planning_id"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Planning <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="planning_id"
                            name="planning_id"
                            required
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >
                            <option value="">
                                -- Pilih Planning --
                            </option>

                            @foreach ($plannings as $planning)
                                <option
                                    value="{{ $planning->id }}"
                                    @selected(old('planning_id') == $planning->id)
                                >
                                    {{ $planning->planning_number }}
                                    — {{ $planning->title }}
                                    — {{ $planning->project?->project_number ?? '-' }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs text-slate-500">
                            Hanya Planning berstatus Selesai yang belum memiliki Produksi & Instalasi yang ditampilkan.
                        </p>

                        @error('planning_id')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div
                        id="planning-info"
                        class="hidden rounded-xl border border-blue-200 bg-blue-50 p-5"
                    >
                        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
                            <div>
                                <p
                                    id="info-planning-title"
                                    class="font-semibold text-blue-950"
                                >
                                    -
                                </p>

                                <p
                                    id="info-planning-number"
                                    class="mt-1 text-sm text-blue-700"
                                >
                                    -
                                </p>
                            </div>

                            <span class="inline-flex w-fit rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                Planning Selesai
                            </span>
                        </div>

                        <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                                    Project
                                </p>

                                <p
                                    id="info-project"
                                    class="mt-1 text-sm font-semibold text-blue-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                                    Order
                                </p>

                                <p
                                    id="info-order"
                                    class="mt-1 text-sm font-semibold text-blue-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                                    Client / Perusahaan
                                </p>

                                <p
                                    id="info-company"
                                    class="mt-1 text-sm font-semibold text-blue-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                                    Petugas Project
                                </p>

                                <p
                                    id="info-project-officer"
                                    class="mt-1 text-sm font-semibold text-blue-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                                    Rencana Mulai
                                </p>

                                <p
                                    id="info-planned-start"
                                    class="mt-1 text-sm font-semibold text-blue-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                                    Rencana Selesai
                                </p>

                                <p
                                    id="info-planned-end"
                                    class="mt-1 text-sm font-semibold text-blue-950"
                                >
                                    -
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Informasi Pekerjaan
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Tentukan petugas dan detail pekerjaan Produksi & Instalasi.
                        </p>
                    </div>

                    <div>
                        <label
                            for="assigned_to"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Petugas Lapangan <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="assigned_to"
                            name="assigned_to"
                            required
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >
                            <option value="">
                                -- Pilih Petugas Lapangan --
                            </option>

                            @foreach ($fieldOfficers as $officer)
                                <option
                                    value="{{ $officer->id }}"
                                    @selected(old('assigned_to') == $officer->id)
                                >
                                    {{ $officer->name }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs text-slate-500">
                            Petugas dari Project akan dipilih otomatis jika tersedia, tetapi Admin tetap dapat menggantinya.
                        </p>

                        @error('assigned_to')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="title"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Judul Pekerjaan <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title') }}"
                            required
                            placeholder="Contoh: Produksi dan Instalasi Media Promosi"
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >

                        <p class="mt-2 text-xs text-slate-500">
                            Judul akan mengikuti Planning yang dipilih dan tetap dapat diubah.
                        </p>

                        @error('title')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label
                                for="start_date"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                            >
                                Tanggal Mulai
                            </label>

                            <input
                                type="date"
                                id="start_date"
                                name="start_date"
                                value="{{ old('start_date') }}"
                                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            >

                            @error('start_date')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="end_date"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                            >
                                Tanggal Selesai
                            </label>

                            <input
                                type="date"
                                id="end_date"
                                name="end_date"
                                value="{{ old('end_date') }}"
                                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            >

                            <p class="mt-2 text-xs text-slate-500">
                                Tanggal selesai tidak boleh lebih awal dari tanggal mulai.
                            </p>

                            @error('end_date')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label
                            for="description"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Deskripsi Pekerjaan
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                            placeholder="Jelaskan pekerjaan produksi dan instalasi yang akan dilakukan..."
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >{{ old('description') }}</textarea>

                        @error('description')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="notes"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Catatan
                        </label>

                        <textarea
                            id="notes"
                            name="notes"
                            rows="4"
                            placeholder="Catatan tambahan jika diperlukan..."
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >{{ old('notes') }}</textarea>

                        @error('notes')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                    <p class="text-sm font-semibold text-blue-800">
                        Status Awal Produksi & Instalasi
                    </p>

                    <p class="mt-1 text-sm leading-6 text-blue-700">
                        Produksi & Instalasi akan dibuat dengan status Menunggu dan progress 0%. Progress 1–99% berarti Dalam Pengerjaan dan progress 100% berarti Selesai.
                    </p>

                    <p class="mt-2 text-sm leading-6 text-blue-700">
                        Project belum dinyatakan selesai ketika Produksi & Instalasi mencapai 100%. Penyelesaian final Project dilakukan setelah BAPP disetujui.
                    </p>
                </div>

                <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">
                    <a
                        href="{{ route('productions.index') }}"
                        class="rounded-lg border border-slate-300 bg-white px-5 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        Buat Produksi & Instalasi
                    </button>
                </div>
            </form>
        @endif
    </div>

    @if ($plannings->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const plannings = @json($planningData);
                const oldPlanningId = @json($oldPlanningId);
                const oldAssignedTo = @json($oldAssignedTo);

                const planningSelect = document.getElementById('planning_id');
                const assignedSelect = document.getElementById('assigned_to');
                const titleInput = document.getElementById('title');
                const planningInfo = document.getElementById('planning-info');

                const infoPlanningTitle = document.getElementById('info-planning-title');
                const infoPlanningNumber = document.getElementById('info-planning-number');
                const infoProject = document.getElementById('info-project');
                const infoOrder = document.getElementById('info-order');
                const infoCompany = document.getElementById('info-company');
                const infoProjectOfficer = document.getElementById('info-project-officer');
                const infoPlannedStart = document.getElementById('info-planned-start');
                const infoPlannedEnd = document.getElementById('info-planned-end');

                function getSelectedPlanning() {
                    return plannings.find(function (planning) {
                        return String(planning.id) === String(planningSelect.value);
                    });
                }

                function updatePlanningInfo() {
                    const selected = getSelectedPlanning();

                    if (!selected) {
                        planningInfo.classList.add('hidden');
                        return;
                    }

                    infoPlanningTitle.textContent = selected.planning_title;
                    infoPlanningNumber.textContent = selected.planning_number;
                    infoProject.textContent =
                        selected.project_number + ' — ' + selected.project_title;
                    infoOrder.textContent = selected.order_number;
                    infoCompany.textContent = selected.company_name;
                    infoProjectOfficer.textContent = selected.project_officer;
                    infoPlannedStart.textContent = selected.planned_start_date;
                    infoPlannedEnd.textContent = selected.planned_end_date;

                    planningInfo.classList.remove('hidden');
                }

                function fillProductionData() {
                    const selected = getSelectedPlanning();

                    if (!selected) {
                        return;
                    }

                    const currentTitle = titleInput.value.trim();

                    if (
                        currentTitle === ''
                        || plannings.some(function (planning) {
                            return planning.planning_title === currentTitle;
                        })
                    ) {
                        titleInput.value = selected.planning_title;
                    }

                    if (
                        oldAssignedTo === ''
                        && selected.project_officer_id
                    ) {
                        const optionExists = Array.from(
                            assignedSelect.options
                        ).some(function (option) {
                            return String(option.value) === String(selected.project_officer_id);
                        });

                        if (optionExists) {
                            assignedSelect.value = selected.project_officer_id;
                        }
                    }
                }

                planningSelect.addEventListener('change', function () {
                    updatePlanningInfo();
                    fillProductionData();
                });

                if (oldPlanningId) {
                    planningSelect.value = oldPlanningId;
                }

                if (oldAssignedTo) {
                    assignedSelect.value = oldAssignedTo;
                }

                updatePlanningInfo();

                if (!oldAssignedTo) {
                    fillProductionData();
                }
            });
        </script>
    @endif
@endsection