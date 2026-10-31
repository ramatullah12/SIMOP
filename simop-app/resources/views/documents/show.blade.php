@extends('layouts.app')

@section('title', 'Detail Dokumen | SIMOP')
@section('page-title', 'Detail Dokumen')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;

        $project = $document->project;
        $order = $project?->order;

        $typeLabels = [
            'contract' => 'SPK / Kontrak Kerja',
            'technical_drawing' => 'Gambar Teknis / Tata Letak',
            'po_spk' => 'PO / SPK',
            'survey' => 'Survei',
            'production' => 'Produksi & Instalasi',
            'other' => 'Lainnya',
        ];

        $typeColors = [
            'contract' => 'bg-purple-100 text-purple-700',
            'technical_drawing' => 'bg-cyan-100 text-cyan-700',
            'po_spk' => 'bg-purple-100 text-purple-700',
            'survey' => 'bg-cyan-100 text-cyan-700',
            'production' => 'bg-blue-100 text-blue-700',
            'other' => 'bg-slate-100 text-slate-700',
        ];

        $projectStatusLabels = [
            'planning' => 'Perencanaan',
            'ongoing' => 'Sedang Dikerjakan',
            'in_progress' => 'Sedang Dikerjakan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        $projectStatusColors = [
            'planning' => 'bg-slate-100 text-slate-700',
            'ongoing' => 'bg-blue-100 text-blue-700',
            'in_progress' => 'bg-blue-100 text-blue-700',
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
                &larr; Kembali ke Dokumen Umum
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
                        Diunggah oleh
                        <span class="font-semibold text-slate-700">
                            {{ $document->uploader?->name ?? '-' }}
                        </span>
                    </p>
                </div>

                <a
                    href="{{ route('documents.download', $document) }}"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    Unduh Dokumen
                </a>
            </div>
        </div>

        @if ($role === 'petugas-lapangan')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <p class="text-sm font-semibold text-blue-800">
                    Dokumen Proyek Anda
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Dokumen ini berasal dari proyek yang ditugaskan kepada Anda. Anda dapat melihat detail dan mengunduh berkas.
                </p>
            </div>
        @elseif ($role === 'client')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <p class="text-sm font-semibold text-blue-800">
                    Dokumen Proyek
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Dokumen ini telah diberikan akses kepada Anda dan dapat diunduh melalui tombol Unduh Dokumen.
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
                            Nama Berkas
                        </dt>

                        <dd class="mt-1 break-all text-sm font-medium text-slate-800">
                            {{ $document->file_name }}
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Ukuran Berkas
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-800">
                                {{ $formattedFileSize }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Format Berkas
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-800">
                                {{ $document->mime_type ?: '-' }}
                            </dd>
                        </div>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Diunggah Oleh
                        </dt>

                        <dd class="mt-1 text-sm font-medium text-slate-800">
                            {{ $document->uploader?->name ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Tanggal Unggah
                        </dt>

                        <dd class="mt-1 text-sm font-medium text-slate-800">
                            {{ $document->created_at?->format('d/m/Y H:i') ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Catatan / Deskripsi
                        </dt>

                        <dd class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">
                            {{ $document->notes ?: ($document->description ?: 'Tidak ada catatan.') }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">
                        Informasi Proyek
                    </h3>

                    <div class="mt-5 space-y-5">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Proyek
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
                                Pesanan
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $order?->order_number ?? '-' }}
                            </p>
                        </div>

                        @if ($role !== 'client')
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
                                Status Proyek
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
                                    Kemajuan Proyek
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
                                    Dapat Diakses Klien
                                </p>

                                <p class="mt-1 text-sm leading-6 text-green-700">
                                    Dokumen ini dapat dilihat dan diunduh oleh Klien pemilik proyek.
                                </p>
                            </div>
                        @else
                            <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
                                <p class="font-semibold text-slate-800">
                                    Dokumen Internal
                                </p>

                                <p class="mt-1 text-sm leading-6 text-slate-600">
                                    Dokumen ini tidak ditampilkan kepada Klien.
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="rounded-xl border border-red-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-slate-900">
                            Hapus Dokumen
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Dokumen akan dihapus dari SIMOP dan berkas akan dihapus dari penyimpanan. Tindakan ini tidak dapat dibatalkan.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('documents.destroy', $document) }}"
                            class="mt-5"
                            data-confirm-title="Hapus Dokumen Proyek"
                            data-confirm-detail="{{ $document->document_number }} - {{ $document->title }}"
                            data-confirm-message="Dokumen ini beserta berkasnya akan dihapus secara permanen dari penyimpanan."
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
