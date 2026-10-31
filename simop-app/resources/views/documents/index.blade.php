@extends('layouts.app')

@section('title', 'Dokumen Umum | SIMOP')
@section('page-title', 'Dokumen Umum')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;

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

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">
                    Dokumen Umum
                </h2>

                @if ($role === 'admin')
                    <p class="mt-1 text-sm text-slate-500">
                        Kelola seluruh dokumen pendukung yang berkaitan dengan proyek SIMOP.
                    </p>
                @elseif ($role === 'petugas-lapangan')
                    <p class="mt-1 text-sm text-slate-500">
                        Akses dokumen dari proyek yang ditugaskan kepada Anda.
                    </p>
                @elseif ($role === 'client')
                    <p class="mt-1 text-sm text-slate-500">
                        Akses dokumen proyek yang telah tersedia untuk Anda.
                    </p>
                @endif
            </div>

            @if ($role === 'admin')
                <a
                    href="{{ route('documents.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    + Unggah Dokumen
                </a>
            @endif
        </div>

        @if ($role === 'petugas-lapangan')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <p class="text-sm font-semibold text-blue-800">
                    Dokumen Proyek Saya
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Daftar ini hanya menampilkan dokumen dari proyek yang ditugaskan kepada Anda.
                </p>
            </div>
        @elseif ($role === 'client')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <p class="text-sm font-semibold text-blue-800">
                    Dokumen Proyek Anda
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Hanya dokumen yang telah diberikan akses oleh Administrator yang dapat Anda lihat dan unduh.
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
                                Dokumen
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Proyek
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Jenis
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Berkas
                            </th>

                            @if ($role === 'admin')
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Akses Klien
                                </th>
                            @endif

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($documents as $document)
                            @php
                                $project = $document->project;
                                $order = $project?->order;

                                $typeLabel = $typeLabels[$document->document_type]
                                    ?? 'Lainnya';

                                $typeColor = $typeColors[$document->document_type]
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

                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                    {{ $documents->firstItem() + $loop->index }}
                                </td>

                                <td class="min-w-64 px-5 py-4">
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ $document->title }}
                                    </p>

                                    <p class="mt-1 text-xs font-medium text-slate-500">
                                        {{ $document->document_number }}
                                    </p>

                                    @if ($role === 'admin')
                                        <p class="mt-1 text-xs text-slate-400">
                                            Diunggah:
                                            {{ $document->uploader?->name ?? '-' }}
                                        </p>
                                    @endif
                                </td>

                                <td class="min-w-56 px-5 py-4">
                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $project?->title ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $project?->project_number ?? '-' }}
                                    </p>

                                    @if ($order?->order_number)
                                        <p class="mt-1 text-xs text-slate-400">
                                            Pesanan: {{ $order->order_number }}
                                        </p>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $typeColor }}">
                                        {{ $typeLabel }}
                                    </span>
                                </td>

                                <td class="min-w-48 px-5 py-4">
                                    <p
                                        class="max-w-48 truncate text-sm font-medium text-slate-700"
                                        title="{{ $document->file_name }}"
                                    >
                                        {{ $document->file_name }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $formattedFileSize }}
                                    </p>
                                </td>

                                @if ($role === 'admin')
                                    <td class="whitespace-nowrap px-5 py-4">
                                        @if ($document->client_access)
                                            <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                                Dapat Diakses
                                            </span>

                                            <p class="mt-2 text-xs text-green-600">
                                                Terlihat oleh Klien
                                            </p>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                                Internal
                                            </span>

                                            <p class="mt-2 text-xs text-slate-400">
                                                Tidak terlihat Klien
                                            </p>
                                        @endif
                                    </td>
                                @endif

                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <a
                                            href="{{ route('documents.show', $document) }}"
                                            class="text-sm font-semibold text-blue-600 hover:text-blue-800"
                                        >
                                            Detail
                                        </a>

                                        <a
                                            href="{{ route('documents.download', $document) }}"
                                            class="text-sm font-semibold text-green-600 hover:text-green-800"
                                        >
                                            Unduh
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="{{ $role === 'admin' ? 7 : 6 }}"
                                    class="px-5 py-12 text-center"
                                >
                                    <p class="font-semibold text-slate-700">
                                        Belum Ada Dokumen
                                    </p>

                                    @if ($role === 'admin')
                                        <p class="mt-1 text-sm text-slate-500">
                                            Unggah dokumen proyek untuk mulai mengelola dokumen.
                                        </p>

                                        <a
                                            href="{{ route('documents.create') }}"
                                            class="mt-4 inline-flex rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                                        >
                                            + Unggah Dokumen
                                        </a>
                                    @elseif ($role === 'client')
                                        <p class="mt-1 text-sm text-slate-500">
                                            Belum ada dokumen proyek yang tersedia untuk Anda.
                                        </p>
                                    @else
                                        <p class="mt-1 text-sm text-slate-500">
                                            Belum ada dokumen dari proyek yang ditugaskan kepada Anda.
                                        </p>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($documents->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>

        @if ($role === 'admin')
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 font-bold text-slate-600">
                            I
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-slate-800">
                                Dokumen Internal
                            </p>

                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Dokumen tanpa akses Klien hanya dapat diakses oleh pihak internal yang memiliki hak akses terhadap proyek.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-green-200 bg-green-50 p-5">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-100 font-bold text-green-700">
                            ✓
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-green-800">
                                Akses Klien
                            </p>

                            <p class="mt-1 text-sm leading-6 text-green-700">
                                Dokumen dengan akses Klien dapat dilihat dan diunduh oleh Klien pemilik proyek.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
