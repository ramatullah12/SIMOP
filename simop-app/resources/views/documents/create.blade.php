@extends('layouts.app')

@section('title', 'Upload Dokumen | SIMOP')
@section('page-title', 'Upload Dokumen')

@section('content')
    @php
        $projectData = $projects->map(function ($project) {
            $order = $project->workEstimate?->order;

            $statusLabels = [
                'planning' => 'Planning',
                'ongoing' => 'Berjalan',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan',
            ];

            return [
                'id' => $project->id,
                'project_number' => $project->project_number,
                'title' => $project->title,
                'status' => $project->status,
                'status_label' => $statusLabels[$project->status] ?? ucfirst($project->status),
                'progress' => max(0, min(100, (int) $project->progress)),
                'order_number' => $order?->order_number ?? '-',
                'client_name' => $order?->client?->name ?? '-',
                'assigned_officer' => $project->assignedUser?->name ?? 'Belum ditentukan',
            ];
        })->values();

        $oldProjectId = (string) old('project_id', '');
    @endphp

    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <a
                href="{{ route('documents.index') }}"
                class="text-sm font-semibold text-blue-600 hover:text-blue-800"
            >
                &larr; Kembali ke Dokumen
            </a>

            <h2 class="mt-3 text-2xl font-bold text-slate-900">
                Upload Dokumen
            </h2>

            <p class="mt-1 text-sm leading-6 text-slate-500">
                Upload dan simpan dokumen yang berkaitan dengan Project SIMOP.
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
                <h3 class="font-semibold text-amber-800">
                    Belum Ada Project yang Tersedia
                </h3>

                <p class="mt-2 text-sm leading-6 text-amber-700">
                    Dokumen hanya dapat diunggah ke Project yang tersedia dan tidak berstatus Dibatalkan.
                </p>

                <a
                    href="{{ route('projects.index') }}"
                    class="mt-4 inline-flex text-sm font-semibold text-amber-800 underline hover:text-amber-900"
                >
                    Lihat Project
                </a>
            </div>
        @else
            <form
                method="POST"
                action="{{ route('documents.store') }}"
                enctype="multipart/form-data"
                class="space-y-6"
            >
                @csrf

                <div class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Pilih Project
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Tentukan Project yang berkaitan dengan dokumen ini.
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
                                    class="font-semibold text-blue-950"
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
                                id="info-project-status"
                                class="inline-flex w-fit rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700"
                            >
                                -
                            </span>
                        </div>

                        <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
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
                                    Client
                                </p>

                                <p
                                    id="info-client"
                                    class="mt-1 text-sm font-semibold text-blue-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
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
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                                    Progress Project
                                </p>

                                <p
                                    id="info-progress"
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
                            Informasi Dokumen
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Lengkapi informasi dokumen yang akan disimpan.
                        </p>
                    </div>

                    <div>
                        <label
                            for="title"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Nama Dokumen <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title') }}"
                            required
                            maxlength="255"
                            placeholder="Contoh: BAPP Final Project"
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >

                        @error('title')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="document_type"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Jenis Dokumen <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="document_type"
                            name="document_type"
                            required
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >
                            <option value="">
                                -- Pilih Jenis Dokumen --
                            </option>

                            <option
                                value="po_spk"
                                @selected(old('document_type') === 'po_spk')
                            >
                                PO / SPK
                            </option>

                            <option
                                value="survey"
                                @selected(old('document_type') === 'survey')
                            >
                                Survey
                            </option>

                            <option
                                value="work_estimate"
                                @selected(old('document_type') === 'work_estimate')
                            >
                                Work Estimate
                            </option>

                            <option
                                value="production"
                                @selected(old('document_type') === 'production')
                            >
                                Produksi & Instalasi
                            </option>

                            <option
                                value="bapp"
                                @selected(old('document_type') === 'bapp')
                            >
                                BAPP
                            </option>

                            <option
                                value="invoice"
                                @selected(old('document_type') === 'invoice')
                            >
                                Invoice
                            </option>

                            <option
                                value="other"
                                @selected(old('document_type') === 'other')
                            >
                                Lainnya
                            </option>
                        </select>

                        @error('document_type')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="file"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            File Dokumen <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="file"
                            id="file"
                            name="file"
                            required
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                            class="block w-full rounded-lg border border-slate-300 bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:text-sm file:font-semibold file:text-slate-700 hover:file:bg-slate-200"
                        >

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Format yang diperbolehkan: PDF, Word, Excel, JPG, JPEG, dan PNG. Ukuran maksimal 10 MB.
                        </p>

                        <div
                            id="file-info"
                            class="mt-3 hidden rounded-lg border border-slate-200 bg-slate-50 p-3"
                        >
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                File Dipilih
                            </p>

                            <p
                                id="file-name"
                                class="mt-1 break-all text-sm font-semibold text-slate-700"
                            >
                                -
                            </p>

                            <p
                                id="file-size"
                                class="mt-1 text-xs text-slate-500"
                            >
                                -
                            </p>
                        </div>

                        @error('file')
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
                            Deskripsi
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            maxlength="5000"
                            placeholder="Keterangan mengenai dokumen..."
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >{{ old('description') }}</textarea>

                        @error('description')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">
                        Hak Akses Dokumen
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Tentukan apakah dokumen dapat diakses oleh Client.
                    </p>

                    <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-start gap-3">
                            <input
                                type="checkbox"
                                id="client_access"
                                name="client_access"
                                value="1"
                                @checked(old('client_access'))
                                class="mt-1 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            >

                            <div>
                                <label
                                    for="client_access"
                                    class="cursor-pointer text-sm font-semibold text-slate-800"
                                >
                                    Dapat Diakses Client
                                </label>

                                <p class="mt-1 text-sm leading-6 text-slate-500">
                                    Aktifkan jika dokumen ini boleh dilihat dan diunduh oleh Client pemilik Project.
                                </p>
                            </div>
                        </div>

                        @error('client_access')
                            <p class="mt-3 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-4">
                        <p class="text-sm leading-6 text-blue-700">
                            Jika akses Client tidak diaktifkan, dokumen tetap menjadi dokumen internal SIMOP. Client tidak dapat melihat maupun mengunduh file tersebut.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col-reverse justify-end gap-3 border-t border-slate-200 pt-5 sm:flex-row">
                    <a
                        href="{{ route('documents.index') }}"
                        class="rounded-lg border border-slate-300 bg-white px-5 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        Upload Dokumen
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
                const projectInfo = document.getElementById('project-info');

                const projectTitle = document.getElementById('info-project-title');
                const projectNumber = document.getElementById('info-project-number');
                const projectStatus = document.getElementById('info-project-status');
                const order = document.getElementById('info-order');
                const client = document.getElementById('info-client');
                const officer = document.getElementById('info-officer');
                const progress = document.getElementById('info-progress');

                function getSelectedProject() {
                    return projects.find(function (project) {
                        return String(project.id) === String(projectSelect.value);
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
                    projectStatus.textContent = selected.status_label;
                    order.textContent = selected.order_number;
                    client.textContent = selected.client_name;
                    officer.textContent = selected.assigned_officer;
                    progress.textContent = selected.progress + '%';

                    projectInfo.classList.remove('hidden');
                }

                projectSelect.addEventListener(
                    'change',
                    updateProjectInfo
                );

                if (oldProjectId) {
                    projectSelect.value = oldProjectId;
                }

                updateProjectInfo();
            });

            document.addEventListener('DOMContentLoaded', function () {
                const fileInput = document.getElementById('file');
                const fileInfo = document.getElementById('file-info');
                const fileName = document.getElementById('file-name');
                const fileSize = document.getElementById('file-size');

                fileInput.addEventListener('change', function () {
                    const file = fileInput.files[0];

                    if (!file) {
                        fileInfo.classList.add('hidden');
                        return;
                    }

                    fileName.textContent = file.name;

                    const sizeInMb = file.size / 1024 / 1024;

                    if (sizeInMb >= 1) {
                        fileSize.textContent =
                            sizeInMb.toFixed(2) + ' MB';
                    } else {
                        fileSize.textContent =
                            (file.size / 1024).toFixed(2) + ' KB';
                    }

                    fileInfo.classList.remove('hidden');
                });
            });
        </script>
    @endif
@endsection