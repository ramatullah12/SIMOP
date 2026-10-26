@extends('layouts.app')

@section('title', 'Buat Produksi & Instalasi | SIMOP')
@section('page-title', 'Buat Produksi & Instalasi')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    <div>
        <a href="{{ route('productions.index') }}"
           class="text-sm font-semibold text-blue-600 hover:text-blue-800">
            ← Kembali
        </a>

        <h2 class="mt-3 text-2xl font-bold text-slate-900">
            Buat Produksi & Instalasi
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Buat pekerjaan produksi dan instalasi berdasarkan Planning yang telah selesai.
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

    @if ($plannings->isEmpty())
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-6">
            <h3 class="font-semibold text-amber-800">
                Belum ada Planning yang tersedia
            </h3>

            <p class="mt-2 text-sm text-amber-700">
                Planning harus berstatus Selesai dan belum memiliki Produksi & Instalasi.
            </p>

            <a href="{{ route('plannings.index') }}"
               class="mt-4 inline-block text-sm font-semibold text-amber-800 hover:underline">
                Lihat Planning
            </a>
        </div>
    @else
        <form method="POST"
              action="{{ route('productions.store') }}"
              class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

            @csrf

            <div class="space-y-6">

                <div>
                    <label for="planning_id"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Planning
                    </label>

                    <select id="planning_id"
                            name="planning_id"
                            required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                        <option value="">
                            Pilih Planning
                        </option>

                        @foreach ($plannings as $planning)
                            <option value="{{ $planning->id }}"
                                @selected(old('planning_id') == $planning->id)>
                                {{ $planning->planning_number }}
                                - {{ $planning->title }}
                                - {{ $planning->project->project_number ?? '-' }}
                            </option>
                        @endforeach
                    </select>

                    @error('planning_id')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="assigned_to"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Petugas Lapangan
                    </label>

                    <select id="assigned_to"
                            name="assigned_to"
                            required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                        <option value="">
                            Pilih Petugas Lapangan
                        </option>

                        @foreach ($fieldOfficers as $officer)
                            <option value="{{ $officer->id }}"
                                @selected(old('assigned_to') == $officer->id)>
                                {{ $officer->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('assigned_to')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="title"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Judul Pekerjaan
                    </label>

                    <input type="text"
                           id="title"
                           name="title"
                           value="{{ old('title') }}"
                           required
                           placeholder="Contoh: Produksi dan Instalasi Media Promosi"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                    @error('title')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    <div>
                        <label for="start_date"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Tanggal Mulai
                        </label>

                        <input type="date"
                               id="start_date"
                               name="start_date"
                               value="{{ old('start_date') }}"
                               class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                        @error('start_date')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Tanggal Selesai
                        </label>

                        <input type="date"
                               id="end_date"
                               name="end_date"
                               value="{{ old('end_date') }}"
                               class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                        @error('end_date')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

                <div>
                    <label for="description"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Deskripsi Pekerjaan
                    </label>

                    <textarea id="description"
                              name="description"
                              rows="5"
                              placeholder="Jelaskan pekerjaan produksi dan instalasi..."
                              class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>

                    @error('description')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="notes"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Catatan
                    </label>

                    <textarea id="notes"
                              name="notes"
                              rows="4"
                              placeholder="Catatan tambahan..."
                              class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>

                    @error('notes')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <p class="text-sm text-blue-700">
                        Produksi & Instalasi akan dibuat dengan status
                        <span class="font-semibold">Menunggu</span>
                        dan progress
                        <span class="font-semibold">0%</span>.
                    </p>
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-200 pt-5">

                    <a href="{{ route('productions.index') }}"
                       class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Batal
                    </a>

                    <button type="submit"
                            class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                        Buat Produksi & Instalasi
                    </button>

                </div>

            </div>
        </form>
    @endif

</div>
@endsection