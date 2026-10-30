@extends('layouts.app')

@section('title', 'Detail Dokumen | SIMOP')
@section('page-title', 'Detail Dokumen')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;

        $project = $document->project;
        $workEstimate = $project?->workEstimate;
        $order = $workEstimate?->order;

        $typeLabels = [
            'po_spk' => 'PO / SPK',
            'survey' => 'Survey',
            'work_estimate' => 'Work Estimate',
            'production' => 'Produksi & Instalasi',
            'bapp' => 'BAPP',
            'invoice' => 'Invoice',
            'other' => 'Lainnya',
        ];

        $typeColors = [
            'po_spk' => 'bg-purple-100 text-purple-700',
            'survey' => 'bg-cyan-100 text-cyan-700',
            'work_estimate' => 'bg-amber-100 text-amber-700',
            'production' => 'bg-blue-100 text-blue-700',
            'bapp' => 'bg-green-100 text-green-700',
            'invoice' => 'bg-indigo-100 text-indigo-700',
            'other' => 'bg-slate-100 text-slate-700',
        ];

        $projectStatusLabels = [
            'planning' => 'Planning',
            'ongoing' => 'Berjalan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        $projectStatusColors = [
            'planning' => 'bg-slate-100 text-slate-700',
            'ongoing' => 'bg-blue-100 text-blue-700',
            'completed' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-red-100 text-red-700',
        ];

        $typeLabel = $typeLabels[$document->document_type] ?? 'Lainnya';

        $typeColor = $typeColors[$document->document_type]
            ?? 'bg-slate-100 text-slate-700';

        $projectStatusLabel = $projectStatusLabels[$project?->status]
            ?? '-';

        $projectStatusColor = $projectStatusColors[$project?->status]
            ?? 'bg-slate-100 text-slate-700';

        $fileSize = (int) ($document->file_size ?? 0);

        if ($fileSize >= 1048576) {
            $formattedFileSize = number_format(
                $fileSize / 1048576,
                2
            ) . ' MB';
        } elseif ($fileSize >= 1024) {
            $formattedFileSize = number_format(
                $fileSize / 1024,
                2
            ) . ' KB';
        } elseif ($fileSize > 0) {
            $formattedFileSize = $fileSize . ' B';
        } else {
            $formattedFileSize = '-';
        }
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
                    Terdapat kesalahan:
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
                href="{{ route('documents.index') }}"
                class="text-sm font-semibold text-blue-600 hover:text-blue-800"
            >
                &larr; Kembali ke Dokumen
            </a>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <p class="text-sm font-semibold text-slate-500">
                            {{ $document->document_number }}
                        </p>

                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $typeColor }}">
                            {{ $typeLabel }}
                        </span>
                    </div>

                    <h2 class="mt-2 text-2xl font-bold text-slate-900">
                        {{ $document->title }}
                    </h2>

                    <p class="mt-2 text-sm text-slate-500">
                        Diupload oleh
                        <span class="font-semibold text-slate-700">
                            {{ $document->uploader?->name ?? '-' }}
                        </span>
                    </p>
                </div>

                <a
                    href="{{ route('documents.download', $document) }}"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    Download Dokumen
                </a>
            </div>
        </div>

        @if ($role === 'petugas-lapangan')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <p class="text-sm font-semibold text-blue-800">
                    Dokumen Project Anda
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Dokumen ini berasal dari Project yang ditugaskan kepada Anda. Anda dapat melihat detail dan mengunduh file.
                </p>
            </div>
        @elseif ($role === 'client')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <p class="text-sm font-semibold text-blue-800">
                    Dokumen Project
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Dokumen ini telah diberikan akses kepada Anda dan dapat diunduh melalui tombol Download Dokumen.
                </p>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900">
                    Informasi Dokumen
                </h3>

                <dl class="mt-5 space-y-5">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Nomor Dokumen
                        </dt>

                        <dd class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $document->document_number }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Nama Dokumen
                        </dt>

                        <dd class="mt-1 text-sm font-medium text-slate-800">
                            {{ $document->title }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Jenis Dokumen
                        </dt>

                        <dd class="mt-2">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $typeColor }}">
                                {{ $typeLabel }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Nama File
                        </dt>

                        <dd class="mt-1 break-all text-sm font-medium text-slate-800">
                            {{ $document->file_name }}
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Ukuran File
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-800">
                                {{ $formattedFileSize }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Format File
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-800">
                                {{ $document->mime_type ?: '-' }}
                            </dd>
                        </div>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Diupload Oleh
                        </dt>

                        <dd class="mt-1 text-sm font-medium text-slate-800">
                            {{ $document->uploader?->name ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Tanggal Upload
                        </dt>

                        <dd class="mt-1 text-sm font-medium text-slate-800">
                            {{ $document->created_at?->format('d/m/Y H:i') ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Deskripsi
                        </dt>

                        <dd class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">
                            {{ $document->description ?: 'Tidak ada deskripsi.' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">
                        Informasi Project
                    </h3>

                    <div class="mt-5 space-y-5">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Project
                            </p>

                            @if ($project)
                                @if ($role === 'client')
                                    <p class="mt-1 text-sm font-semibold text-slate-800">
                                        {{ $project->project_number }}
                                        — {{ $project->title }}
                                    </p>
                                @else
                                    <a
                                        href="{{ route('projects.show', $project) }}"
                                        class="mt-1 inline-block text-sm font-semibold text-blue-600 hover:text-blue-800"
                                    >
                                        {{ $project->project_number }}
                                        — {{ $project->title }}
                                    </a>
                                @endif
                            @else
                                <p class="mt-1 text-sm text-slate-500">
                                    -
                                </p>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Order
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $order?->order_number ?? '-' }}
                            </p>
                        </div>

                        @if ($role !== 'client')
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Work Estimate
                                </p>

                                <p class="mt-1 text-sm font-medium text-slate-800">
                                    {{ $workEstimate?->estimate_number ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Petugas Lapangan
                                </p>

                                <p class="mt-1 text-sm font-medium text-slate-800">
                                    {{ $project?->assignedUser?->name ?? '-' }}
                                </p>
                            </div>
                        @endif

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Status Project
                            </p>

                            <div class="mt-2">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $projectStatusColor }}">
                                    {{ $projectStatusLabel }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between gap-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Progress Project
                                </p>

                                <span class="text-sm font-bold text-blue-600">
                                    {{ max(0, min(100, (int) ($project?->progress ?? 0))) }}%
                                </span>
                            </div>

                            <div class="mt-2 h-2.5 overflow-hidden rounded-full bg-slate-200">
                                <div
                                    class="h-full rounded-full bg-blue-500"
                                    style="width: {{ max(0, min(100, (int) ($project?->progress ?? 0))) }}%"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($role === 'admin')
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-slate-900">
                            Akses Dokumen
                        </h3>

                        @if ($document->client_access)
                            <div class="mt-4 rounded-lg border border-green-200 bg-green-50 p-4">
                                <p class="font-semibold text-green-800">
                                    Dapat Diakses Client
                                </p>

                                <p class="mt-1 text-sm leading-6 text-green-700">
                                    Dokumen ini dapat dilihat dan diunduh oleh Client pemilik Project.
                                </p>
                            </div>
                        @else
                            <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
                                <p class="font-semibold text-slate-800">
                                    Dokumen Internal
                                </p>

                                <p class="mt-1 text-sm leading-6 text-slate-600">
                                    Dokumen ini tidak ditampilkan kepada Client.
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="rounded-xl border border-red-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-slate-900">
                            Hapus Dokumen
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Dokumen akan dihapus dari SIMOP dan file akan dihapus dari penyimpanan. Tindakan ini tidak dapat dibatalkan.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('documents.destroy', $document) }}"
                            class="mt-5"
                            onsubmit="return confirm('Yakin ingin menghapus dokumen ini? File juga akan dihapus dari penyimpanan.')"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700"
                            >
                                Hapus Dokumen
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection