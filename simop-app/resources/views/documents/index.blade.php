@extends('layouts.app')

@section('title', 'Dokumen | SIMOP')
@section('page-title', 'Dokumen')

@section('content')
<div class="space-y-6">

    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">
                Dokumen
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Kelola dan akses dokumen yang berkaitan dengan Project.
            </p>
        </div>

        @if (auth()->user()?->role?->slug === 'admin')
            <a href="{{ route('documents.create') }}"
               class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                Upload Dokumen
            </a>
        @endif
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">

                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            No.
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Nomor Dokumen
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Dokumen
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Project
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Jenis
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Akses Client
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($documents as $document)
                        <tr class="hover:bg-slate-50">

                            <td class="px-5 py-4 text-sm text-slate-600">
                                {{ $documents->firstItem() + $loop->index }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <p class="text-sm font-semibold text-slate-900">
                                    {{ $document->document_number }}
                                </p>
                            </td>

                            <td class="px-5 py-4">
                                <p class="text-sm font-medium text-slate-900">
                                    {{ $document->title }}
                                </p>

                                <p class="mt-1 max-w-xs truncate text-xs text-slate-500">
                                    {{ $document->file_name }}
                                </p>
                            </td>

                            <td class="px-5 py-4">
                                <p class="text-sm font-medium text-slate-800">
                                    {{ $document->project->title ?? '-' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $document->project->project_number ?? '-' }}
                                </p>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
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
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                @if ($document->client_access)
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Dapat Diakses
                                    </span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                        Internal
                                    </span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex items-center gap-3">

                                    <a href="{{ route('documents.show', $document) }}"
                                       class="text-sm font-semibold text-blue-600 hover:text-blue-800">
                                        Detail
                                    </a>

                                    <a href="{{ route('documents.download', $document) }}"
                                       class="text-sm font-semibold text-green-600 hover:text-green-800">
                                        Download
                                    </a>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <p class="font-medium text-slate-700">
                                    Belum ada dokumen.
                                </p>

                                @if (auth()->user()?->role?->slug === 'admin')
                                    <p class="mt-1 text-sm text-slate-500">
                                        Upload dokumen Project untuk mulai mengelola dokumen.
                                    </p>
                                @elseif (auth()->user()?->role?->slug === 'client')
                                    <p class="mt-1 text-sm text-slate-500">
                                        Belum ada dokumen Project yang tersedia untuk Anda.
                                    </p>
                                @else
                                    <p class="mt-1 text-sm text-slate-500">
                                        Belum ada dokumen dari Project yang ditugaskan kepada Anda.
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

</div>
@endsection