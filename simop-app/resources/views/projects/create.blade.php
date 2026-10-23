@extends('layouts.app')

@section('title', 'Buat Project | SIMOP')
@section('page-title', 'Buat Project')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    <div>
        <a href="{{ route('projects.index') }}"
           class="text-sm font-semibold text-blue-600 hover:text-blue-800">
            ← Kembali ke Project
        </a>

        <h2 class="mt-3 text-2xl font-bold text-slate-900">
            Buat Project Baru
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Project hanya dapat dibuat dari Work Estimate yang telah mendapatkan persetujuan final.
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

    @if ($workEstimates->isEmpty())
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-6">
            <h3 class="font-semibold text-amber-800">
                Belum ada Work Estimate yang tersedia
            </h3>

            <p class="mt-2 text-sm text-amber-700">
                Work Estimate harus mendapatkan persetujuan final dari CEO sebelum dapat dibuat menjadi Project.
            </p>

            <a href="{{ route('admin.work-estimates.index') }}"
               class="mt-4 inline-block text-sm font-semibold text-amber-800 hover:underline">
                Lihat Work Estimate
            </a>
        </div>
    @else
        <form method="POST"
              action="{{ route('projects.store') }}"
              class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

            @csrf

            <div class="space-y-6">

                <div>
                    <label for="work_estimate_id"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Work Estimate
                    </label>

                    <select id="work_estimate_id"
                            name="work_estimate_id"
                            required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                        <option value="">
                            Pilih Work Estimate
                        </option>

                        @foreach ($workEstimates as $workEstimate)
                            <option value="{{ $workEstimate->id }}"
                                @selected(old('work_estimate_id') == $workEstimate->id)>
                                {{ $workEstimate->estimate_number }}
                                - {{ $workEstimate->title }}
                                - Rp {{ number_format((float) $workEstimate->total_amount, 0, ',', '.') }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div>
                    <label for="title"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Nama Project
                    </label>

                    <input type="text"
                           id="title"
                           name="title"
                           value="{{ old('title') }}"
                           required
                           placeholder="Contoh: Instalasi Peralatan Kantor"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="assigned_to"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Petugas Lapangan
                    </label>

                    <select id="assigned_to"
                            name="assigned_to"
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                        <option value="">
                            Belum ditentukan
                        </option>

                        @foreach ($fieldOfficers as $officer)
                            <option value="{{ $officer->id }}"
                                @selected(old('assigned_to') == $officer->id)>
                                {{ $officer->name }} - {{ $officer->email }}
                            </option>
                        @endforeach

                    </select>

                    @if ($fieldOfficers->isEmpty())
                        <p class="mt-2 text-xs text-amber-600">
                            Belum ada pengguna dengan role Petugas Lapangan.
                        </p>
                    @endif
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
                    </div>

                    <div>
                        <label for="deadline"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Deadline
                        </label>

                        <input type="date"
                               id="deadline"
                               name="deadline"
                               value="{{ old('deadline') }}"
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
                              placeholder="Catatan Project (opsional)"
                              class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-200 pt-5">

                    <a href="{{ route('projects.index') }}"
                       class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Batal
                    </a>

                    <button type="submit"
                            class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                        Buat Project
                    </button>

                </div>

            </div>
        </form>
    @endif

</div>
@endsection