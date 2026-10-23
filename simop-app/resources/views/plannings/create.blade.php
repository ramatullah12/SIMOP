@extends('layouts.app')

@section('title', 'Buat Planning | SIMOP')
@section('page-title', 'Buat Planning')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    <div>
        <a href="{{ route('plannings.index') }}"
           class="text-sm font-semibold text-blue-600 hover:text-blue-800">
            ← Kembali ke Planning
        </a>

        <h2 class="mt-3 text-2xl font-bold text-slate-900">
            Buat Planning Baru
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Buat rencana pekerjaan berdasarkan Project yang tersedia.
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
                Planning hanya dapat dibuat dari Project yang belum memiliki Planning dan belum selesai atau dibatalkan.
            </p>

            <a href="{{ route('projects.index') }}"
               class="mt-4 inline-block text-sm font-semibold text-amber-800 hover:underline">
                Lihat Project
            </a>
        </div>
    @else
        <form method="POST"
              action="{{ route('plannings.store') }}"
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

                    <p class="mt-2 text-xs text-slate-500">
                        Hanya Project yang belum memiliki Planning yang ditampilkan.
                    </p>
                </div>

                <div>
                    <label for="title"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Judul Planning
                    </label>

                    <input type="text"
                           id="title"
                           name="title"
                           value="{{ old('title') }}"
                           required
                           placeholder="Contoh: Rencana Produksi dan Instalasi"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="description"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Deskripsi Rencana
                    </label>

                    <textarea id="description"
                              name="description"
                              rows="5"
                              placeholder="Jelaskan rencana pekerjaan yang akan dilakukan..."
                              class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    <div>
                        <label for="planned_start_date"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Rencana Mulai
                        </label>

                        <input type="date"
                               id="planned_start_date"
                               name="planned_start_date"
                               value="{{ old('planned_start_date') }}"
                               class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="planned_end_date"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Rencana Selesai
                        </label>

                        <input type="date"
                               id="planned_end_date"
                               name="planned_end_date"
                               value="{{ old('planned_end_date') }}"
                               class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                </div>

                <div>
                    <label for="notes"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Catatan
                    </label>

                    <textarea id="notes"
                              name="notes"
                              rows="4"
                              placeholder="Catatan tambahan (opsional)"
                              class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>

                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <p class="text-sm text-blue-700">
                        Planning baru akan dibuat dengan status
                        <span class="font-semibold">Draft</span>.
                        Status dapat diperbarui setelah Planning dibuat.
                    </p>
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-200 pt-5">

                    <a href="{{ route('plannings.index') }}"
                       class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Batal
                    </a>

                    <button type="submit"
                            class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                        Buat Planning
                    </button>

                </div>

            </div>
        </form>
    @endif

</div>
@endsection