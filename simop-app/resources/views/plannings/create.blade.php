@extends('layouts.app')

@section('title', 'Buat Planning | SIMOP')
@section('page-title', 'Buat Planning')

@section('content')
    @php
        $projectData = $projects->map(function ($project) {
            return [
                'id' => $project->id,
                'project_number' => $project->project_number,
                'title' => $project->title,
                'status' => $project->status,
                'work_estimate' => $project->workEstimate?->estimate_number ?? '-',
                'order_number' => $project->workEstimate?->order?->order_number ?? '-',
                'company_name' => $project->workEstimate?->order?->company_name ?? '-',
                'officer' => $project->assignedUser?->name ?? 'Belum ditentukan',
                'start_date' => $project->start_date?->format('d/m/Y') ?? '-',
                'deadline' => $project->deadline?->format('d/m/Y') ?? '-',
            ];
        })->values();

        $oldProjectId = (string) old('project_id', '');
    @endphp

    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <a
                href="{{ route('plannings.index') }}"
                class="text-sm font-semibold text-blue-600 hover:text-blue-800"
            >
                &larr; Kembali ke Planning
            </a>

            <h2 class="mt-3 text-2xl font-bold text-slate-900">
                Buat Planning Baru
            </h2>

            <p class="mt-1 text-sm leading-6 text-slate-500">
                Buat rencana pekerjaan untuk Project yang belum memiliki Planning.
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

        @if ($projects->isEmpty())
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-6">
                <div class="flex gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 font-bold text-amber-700">
                        !
                    </div>

                    <div>
                        <h3 class="font-semibold text-amber-800">
                            Belum Ada Project yang Tersedia
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-amber-700">
                            Planning hanya dapat dibuat dari Project yang belum memiliki Planning dan tidak berstatus Selesai atau Dibatalkan.
                        </p>

                        <a
                            href="{{ route('projects.index') }}"
                            class="mt-3 inline-flex text-sm font-semibold text-amber-800 underline hover:text-amber-900"
                        >
                            Lihat Project
                        </a>
                    </div>
                </div>
            </div>
        @else
            <form
                method="POST"
                action="{{ route('plannings.store') }}"
                class="space-y-6"
            >
                @csrf

                <div class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Pilih Project
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Planning akan terhubung langsung dengan Project yang dipilih.
                        </p>
                    </div>

                    <div>
                        <label
                            for="project_id"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Project <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="project_id"
                            name="project_id"
                            required
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >
                            <option value="">
                                -- Pilih Project --
                            </option>

                            @foreach ($projects as $project)
                                <option
                                    value="{{ $project->id }}"
                                    @selected(old('project_id') == $project->id)
                                >
                                    {{ $project->project_number }}
                                    — {{ $project->title }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs text-slate-500">
                            Hanya Project aktif yang belum memiliki Planning yang ditampilkan.
                        </p>

                        @error('project_id')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div
                        id="project-info"
                        class="hidden rounded-xl border border-blue-200 bg-blue-50 p-5"
                    >
                        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
                            <div>
                                <p
                                    id="info-project-title"
                                    class="font-semibold text-blue-900"
                                >
                                    -
                                </p>

                                <p
                                    id="info-project-number"
                                    class="mt-1 text-sm text-blue-700"
                                >
                                    -
                                </p>
                            </div>

                            <span
                                id="info-status"
                                class="inline-flex w-fit rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700"
                            >
                                -
                            </span>
                        </div>

                        <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-blue-600">
                                    Work Estimate
                                </p>

                                <p
                                    id="info-work-estimate"
                                    class="mt-1 text-sm font-semibold text-blue-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-blue-600">
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
                                <p class="text-xs font-medium uppercase tracking-wide text-blue-600">
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
                                <p class="text-xs font-medium uppercase tracking-wide text-blue-600">
                                    Petugas Lapangan
                                </p>

                                <p
                                    id="info-officer"
                                    class="mt-1 text-sm font-semibold text-blue-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-blue-600">
                                    Tanggal Mulai Project
                                </p>

                                <p
                                    id="info-start-date"
                                    class="mt-1 text-sm font-semibold text-blue-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-blue-600">
                                    Deadline Project
                                </p>

                                <p
                                    id="info-deadline"
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
                            Rencana Pekerjaan
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Tentukan detail dan jadwal pelaksanaan Planning.
                        </p>
                    </div>

                    <div>
                        <label
                            for="title"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Judul Planning <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title') }}"
                            required
                            placeholder="Contoh: Rencana Produksi dan Instalasi"
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >

                        <p class="mt-2 text-xs text-slate-500">
                            Judul akan mengikuti nama Project saat Project dipilih dan tetap dapat diubah.
                        </p>

                        @error('title')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="description"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Deskripsi Rencana
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                            placeholder="Jelaskan rencana pekerjaan yang akan dilakukan..."
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >{{ old('description') }}</textarea>

                        @error('description')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label
                                for="planned_start_date"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                            >
                                Rencana Mulai
                            </label>

                            <input
                                type="date"
                                id="planned_start_date"
                                name="planned_start_date"
                                value="{{ old('planned_start_date') }}"
                                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            >

                            @error('planned_start_date')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="planned_end_date"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                            >
                                Rencana Selesai
                            </label>

                            <input
                                type="date"
                                id="planned_end_date"
                                name="planned_end_date"
                                value="{{ old('planned_end_date') }}"
                                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            >

                            <p class="mt-2 text-xs text-slate-500">
                                Tanggal selesai tidak boleh lebih awal dari tanggal mulai.
                            </p>

                            @error('planned_end_date')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
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
                        Status Awal Planning
                    </p>

                    <p class="mt-1 text-sm leading-6 text-blue-700">
                        Planning baru akan dibuat dengan status Draft. Setelah dibuat, proses dilanjutkan secara berurutan dari Draft, Siap, Dalam Proses, hingga Selesai.
                    </p>
                </div>

                <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">
                    <a
                        href="{{ route('plannings.index') }}"
                        class="rounded-lg border border-slate-300 bg-white px-5 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        Buat Planning
                    </button>
                </div>
            </form>
        @endif
    </div>

    @if ($projects->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const projects = @json($projectData);
                const oldProjectId = @json($oldProjectId);

                const projectSelect = document.getElementById('project_id');
                const titleInput = document.getElementById('title');
                const projectInfo = document.getElementById('project-info');

                const projectTitle = document.getElementById('info-project-title');
                const projectNumber = document.getElementById('info-project-number');
                const projectStatus = document.getElementById('info-status');
                const workEstimate = document.getElementById('info-work-estimate');
                const order = document.getElementById('info-order');
                const company = document.getElementById('info-company');
                const officer = document.getElementById('info-officer');
                const startDate = document.getElementById('info-start-date');
                const deadline = document.getElementById('info-deadline');

                function getSelectedProject() {
                    return projects.find(function (project) {
                        return String(project.id) === String(projectSelect.value);
                    });
                }

                function formatStatus(status) {
                    if (status === 'planning') {
                        return 'Planning';
                    }

                    if (status === 'ongoing') {
                        return 'Dalam Pengerjaan';
                    }

                    return status
                        .replaceAll('_', ' ')
                        .replace(/\b\w/g, function (letter) {
                            return letter.toUpperCase();
                        });
                }

                function updateProjectInfo() {
                    const selected = getSelectedProject();

                    if (!selected) {
                        projectInfo.classList.add('hidden');
                        return;
                    }

                    projectTitle.textContent = selected.title;
                    projectNumber.textContent = selected.project_number;
                    projectStatus.textContent = formatStatus(selected.status);
                    workEstimate.textContent = selected.work_estimate;
                    order.textContent = selected.order_number;
                    company.textContent = selected.company_name;
                    officer.textContent = selected.officer;
                    startDate.textContent = selected.start_date;
                    deadline.textContent = selected.deadline;

                    projectInfo.classList.remove('hidden');
                }

                projectSelect.addEventListener('change', function () {
                    const previousTitle = titleInput.value.trim();

                    const selected = getSelectedProject();

                    updateProjectInfo();

                    if (
                        selected
                        && (
                            previousTitle === ''
                            || projects.some(function (project) {
                                return project.title === previousTitle;
                            })
                        )
                    ) {
                        titleInput.value = selected.title;
                    }
                });

                if (oldProjectId) {
                    projectSelect.value = oldProjectId;
                }

                updateProjectInfo();
            });
        </script>
    @endif
@endsection