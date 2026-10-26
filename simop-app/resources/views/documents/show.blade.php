@extends('layouts.app')

@section('title', 'Detail Dokumen | SIMOP')
@section('page-title', 'Detail Dokumen')

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

    <div>
        <a href="{{ route('documents.index') }}"
           class="text-sm font-semibold text-blue-600 hover:text-blue-800">
            ← Kembali ke Dokumen
        </a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">

            <div>
                <p class="text-sm font-medium text-slate-500">
                    {{ $document->document_number }}
                </p>

                <h2 class="mt-1 text-2xl font-bold text-slate-900">
                    {{ $document->title }}
                </h2>

                <p class="mt-2 text-sm text-slate-500">
                    Diupload oleh
                    <span class="font-semibold text-slate-700">
                        {{ $document->uploader->name ?? '-' }}
                    </span>
                </p>
            </div>

            <a href="{{ route('documents.download', $document) }}"
               class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                Download Dokumen
            </a>

        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

            <h3 class="text-lg font-bold text-slate-900">
                Informasi Dokumen
            </h3>

            <div class="mt-5 space-y-5">

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Nomor Dokumen
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $document->document_number }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Nama Dokumen
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $document->title }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Jenis Dokumen
                    </p>

                    <div class="mt-2">
                        @if ($document->document_type === 'po_spk')
                            <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700">
                                PO / SPK
                            </span>
                        @elseif ($document->document_type === 'survey')
                            <span class="rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold text-cyan-700">
                                Survey
                            </span>
                        @elseif ($document->document_type === 'work_estimate')
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                Work Estimate
                            </span>
                        @elseif ($document->document_type === 'production')
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                Produksi & Instalasi
                            </span>
                        @elseif ($document->document_type === 'bapp')
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                BAPP
                            </span>
                        @elseif ($document->document_type === 'invoice')
                            <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
                                Invoice
                            </span>
                        @else
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                Lainnya
                            </span>
                        @endif
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Nama File
                    </p>

                    <p class="mt-1 break-all text-sm font-medium text-slate-800">
                        {{ $document->file_name }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Ukuran File
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        @if ($document->file_size)
                            @if ($document->file_size >= 1048576)
                                {{ number_format($document->file_size / 1048576, 2) }} MB
                            @else
                                {{ number_format($document->file_size / 1024, 2) }} KB
                            @endif
                        @else
                            -
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Deskripsi
                    </p>

                    <p class="mt-1 whitespace-pre-line text-sm text-slate-700">
                        {{ $document->description ?: '-' }}
                    </p>
                </div>

            </div>
        </div>

        <div class="space-y-6">

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

                <h3 class="text-lg font-bold text-slate-900">
                    Informasi Project
                </h3>

                <div class="mt-5 space-y-5">

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">
                            Project
                        </p>

                        @if (auth()->user()?->role?->slug !== 'client')
                            <a href="{{ route('projects.show', $document->project) }}"
                               class="mt-1 inline-block text-sm font-semibold text-blue-600 hover:text-blue-800">
                                {{ $document->project->project_number }}
                                - {{ $document->project->title }}
                            </a>
                        @else
                            <p class="mt-1 text-sm font-semibold text-slate-800">
                                {{ $document->project->project_number }}
                                - {{ $document->project->title }}
                            </p>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">
                            Petugas Lapangan
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $document->project->assignedUser->name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">
                            Status Project
                        </p>

                        <div class="mt-2">
                            @if ($document->project->status === 'planning')
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                    Planning
                                </span>
                            @elseif ($document->project->status === 'ongoing')
                                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                    Berjalan
                                </span>
                            @elseif ($document->project->status === 'completed')
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                    Selesai
                                </span>
                            @elseif ($document->project->status === 'cancelled')
                                <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                    Dibatalkan
                                </span>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

                <h3 class="text-lg font-bold text-slate-900">
                    Akses Dokumen
                </h3>

                <div class="mt-4">
                    @if ($document->client_access)
                        <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                            <p class="font-semibold text-green-800">
                                Dapat Diakses Client
                            </p>

                            <p class="mt-1 text-sm text-green-700">
                                Dokumen ini dapat dilihat dan diunduh oleh Client pemilik Project.
                            </p>
                        </div>
                    @else
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <p class="font-semibold text-slate-800">
                                Dokumen Internal
                            </p>

                            <p class="mt-1 text-sm text-slate-600">
                                Dokumen ini tidak dapat diakses oleh Client.
                            </p>
                        </div>
                    @endif
                </div>

            </div>

            @if (auth()->user()?->role?->slug === 'admin')
                <div class="rounded-xl border border-red-200 bg-white p-6 shadow-sm">

                    <h3 class="text-lg font-bold text-slate-900">
                        Hapus Dokumen
                    </h3>

                    <p class="mt-2 text-sm text-slate-500">
                        Dokumen yang dihapus akan dihapus dari database dan penyimpanan file.
                    </p>

                    <form method="POST"
                          action="{{ route('documents.destroy', $document) }}"
                          class="mt-5"
                          onsubmit="return confirm('Yakin ingin menghapus dokumen ini?')">

                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700">
                            Hapus Dokumen
                        </button>

                    </form>

                </div>
            @endif

        </div>

    </div>

</div>
@endsection