@extends('layouts.app')

@section('title', 'Upload Dokumen | SIMOP')
@section('page-title', 'Upload Dokumen')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    <div>
        <a href="{{ route('documents.index') }}"
           class="text-sm font-semibold text-blue-600 hover:text-blue-800">
            ← Kembali
        </a>

        <h2 class="mt-3 text-2xl font-bold text-slate-900">
            Upload Dokumen
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Upload dokumen yang berkaitan dengan Project SIMOP.
        </p>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
            <ul class="list-inside list-disc text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($projects->isEmpty())
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-6">
            <h3 class="font-semibold text-amber-800">
                Belum ada Project yang tersedia
            </h3>

            <p class="mt-2 text-sm text-amber-700">
                Dokumen hanya dapat diunggah ke Project yang tersedia dan tidak dibatalkan.
            </p>

            <a href="{{ route('projects.index') }}"
               class="mt-4 inline-block text-sm font-semibold text-amber-800 hover:underline">
                Lihat Project
            </a>
        </div>
    @else
        <form method="POST"
              action="{{ route('documents.store') }}"
              enctype="multipart/form-data"
              class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

            @csrf

            <div class="space-y-6">

                <div>
                    <label for="project_id"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Project
                    </label>

                    <select id="project_id"
                            name="project_id"
                            required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                        <option value="">
                            Pilih Project
                        </option>

                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}"
                                @selected(old('project_id') == $project->id)>
                                {{ $project->project_number }}
                                - {{ $project->title }}
                            </option>
                        @endforeach
                    </select>

                    @error('project_id')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="title"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Nama Dokumen
                    </label>

                    <input type="text"
                           id="title"
                           name="title"
                           value="{{ old('title') }}"
                           required
                           placeholder="Contoh: BAPP Final Project"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                    @error('title')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="document_type"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Jenis Dokumen
                    </label>

                    <select id="document_type"
                            name="document_type"
                            required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                        <option value="">
                            Pilih Jenis Dokumen
                        </option>

                        <option value="po_spk"
                            @selected(old('document_type') === 'po_spk')>
                            PO / SPK
                        </option>

                        <option value="survey"
                            @selected(old('document_type') === 'survey')>
                            Survey
                        </option>

                        <option value="work_estimate"
                            @selected(old('document_type') === 'work_estimate')>
                            Work Estimate
                        </option>

                        <option value="production"
                            @selected(old('document_type') === 'production')>
                            Produksi & Instalasi
                        </option>

                        <option value="bapp"
                            @selected(old('document_type') === 'bapp')>
                            BAPP
                        </option>

                        <option value="invoice"
                            @selected(old('document_type') === 'invoice')>
                            Invoice
                        </option>

                        <option value="other"
                            @selected(old('document_type') === 'other')>
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
                    <label for="file"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        File Dokumen
                    </label>

                    <input type="file"
                           id="file"
                           name="file"
                           required
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                           class="block w-full rounded-lg border border-slate-300 bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:text-sm file:font-semibold file:text-slate-700 hover:file:bg-slate-200">

                    <p class="mt-2 text-xs text-slate-500">
                        Format: PDF, Word, Excel, JPG, JPEG, atau PNG. Maksimal 10 MB.
                    </p>

                    @error('file')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="description"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Deskripsi
                    </label>

                    <textarea id="description"
                              name="description"
                              rows="4"
                              placeholder="Keterangan mengenai dokumen..."
                              class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>

                    @error('description')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex items-start gap-3">

                        <input type="checkbox"
                               id="client_access"
                               name="client_access"
                               value="1"
                               @checked(old('client_access'))
                               class="mt-1 rounded border-slate-300 text-blue-600 focus:ring-blue-500">

                        <div>
                            <label for="client_access"
                                   class="text-sm font-semibold text-slate-800">
                                Dapat diakses Client
                            </label>

                            <p class="mt-1 text-sm text-slate-500">
                                Aktifkan jika dokumen ini boleh dilihat dan diunduh oleh Client pemilik Project.
                            </p>
                        </div>

                    </div>

                    @error('client_access')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <p class="text-sm text-blue-700">
                        File akan disimpan sebagai dokumen internal SIMOP. Client hanya dapat mengakses dokumen jika opsi
                        <span class="font-semibold">Dapat diakses Client</span>
                        diaktifkan.
                    </p>
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-200 pt-5">

                    <a href="{{ route('documents.index') }}"
                       class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Batal
                    </a>

                    <button type="submit"
                            class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                        Upload Dokumen
                    </button>

                </div>

            </div>
        </form>
    @endif

</div>
@endsection
