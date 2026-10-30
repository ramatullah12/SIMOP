@extends('layouts.app')

@section('title', 'Buat Project | SIMOP')
@section('page-title', 'Buat Project')

@section('content')
    @php
        $workEstimateData = $workEstimates->map(function ($workEstimate) {
            return [
                'id' => $workEstimate->id,
                'estimate_number' => $workEstimate->estimate_number,
                'title' => $workEstimate->title,
                'total_amount' => (float) $workEstimate->total_amount,
                'order_number' => $workEstimate->order?->order_number ?? '-',
                'company_name' => $workEstimate->order?->company_name ?? '-',
                'event_name' => $workEstimate->order?->event_name ?? '-',
                'event_location' => $workEstimate->order?->event_location ?? '-',
                'survey_location' => $workEstimate->survey?->location ?? '-',
            ];
        })->values();

        $oldWorkEstimateId = (string) old('work_estimate_id', '');
    @endphp

    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <a
                href="{{ route('projects.index') }}"
                class="text-sm font-semibold text-blue-600 hover:text-blue-800"
            >
                &larr; Kembali ke Project
            </a>

            <h2 class="mt-3 text-2xl font-bold text-slate-900">
                Buat Project Baru
            </h2>

            <p class="mt-1 text-sm leading-6 text-slate-500">
                Project hanya dapat dibuat dari Work Estimate yang telah mendapatkan persetujuan final dari Direktur.
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

        @if ($workEstimates->isEmpty())
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-6">
                <div class="flex gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 font-bold text-amber-700">
                        !
                    </div>

                    <div>
                        <h3 class="font-semibold text-amber-800">
                            Belum Ada Work Estimate yang Tersedia
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-amber-700">
                            Project hanya dapat dibuat dari Work Estimate yang sudah disetujui Direktur dan belum memiliki Project.
                        </p>

                        <a
                            href="{{ route('admin.work-estimates.index') }}"
                            class="mt-3 inline-flex text-sm font-semibold text-amber-800 underline hover:text-amber-900"
                        >
                            Lihat Work Estimate
                        </a>
                    </div>
                </div>
            </div>
        @else
            <form
                method="POST"
                action="{{ route('projects.store') }}"
                class="space-y-6"
            >
                @csrf

                <div class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Sumber Work Estimate
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Pilih Work Estimate yang telah mendapatkan persetujuan final Direktur.
                        </p>
                    </div>

                    <div>
                        <label
                            for="work_estimate_id"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Work Estimate <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="work_estimate_id"
                            name="work_estimate_id"
                            required
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >
                            <option value="">
                                -- Pilih Work Estimate --
                            </option>

                            @foreach ($workEstimates as $workEstimate)
                                <option
                                    value="{{ $workEstimate->id }}"
                                    @selected(old('work_estimate_id') == $workEstimate->id)
                                >
                                    {{ $workEstimate->estimate_number }}
                                    — {{ $workEstimate->title }}
                                    — Rp{{ number_format((float) $workEstimate->total_amount, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>

                        @error('work_estimate_id')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div
                        id="work-estimate-info"
                        class="hidden rounded-xl border border-green-200 bg-green-50 p-5"
                    >
                        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                            <div>
                                <p class="font-semibold text-green-900">
                                    Work Estimate Disetujui
                                </p>

                                <p class="mt-1 text-sm text-green-700">
                                    Work Estimate ini telah mendapatkan persetujuan final Direktur.
                                </p>
                            </div>

                            <span class="inline-flex w-fit rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                Approved
                            </span>
                        </div>

                        <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-green-700">
                                    Nomor WE
                                </p>

                                <p
                                    id="info-estimate-number"
                                    class="mt-1 font-semibold text-green-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-green-700">
                                    Nomor Order
                                </p>

                                <p
                                    id="info-order-number"
                                    class="mt-1 font-semibold text-green-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-green-700">
                                    Client / Perusahaan
                                </p>

                                <p
                                    id="info-company"
                                    class="mt-1 font-semibold text-green-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-green-700">
                                    Event
                                </p>

                                <p
                                    id="info-event"
                                    class="mt-1 font-semibold text-green-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-green-700">
                                    Lokasi Survey
                                </p>

                                <p
                                    id="info-survey"
                                    class="mt-1 font-semibold text-green-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-green-700">
                                    Total Work Estimate
                                </p>

                                <p
                                    id="info-total"
                                    class="mt-1 font-bold text-green-950"
                                >
                                    Rp0
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Informasi Project
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Tentukan nama Project, Petugas Lapangan, dan jadwal pelaksanaan.
                        </p>
                    </div>

                    <div>
                        <label
                            for="title"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Nama Project <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title') }}"
                            required
                            placeholder="Contoh: Instalasi Peralatan Kantor"
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >

                        <p class="mt-2 text-xs text-slate-500">
                            Nama Project akan mengikuti judul Work Estimate saat dipilih dan tetap dapat diubah.
                        </p>

                        @error('title')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="assigned_to"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Petugas Lapangan
                        </label>

                        <select
                            id="assigned_to"
                            name="assigned_to"
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >
                            <option value="">
                                -- Belum Ditentukan --
                            </option>

                            @foreach ($fieldOfficers as $officer)
                                <option
                                    value="{{ $officer->id }}"
                                    @selected(old('assigned_to') == $officer->id)
                                >
                                    {{ $officer->name }} — {{ $officer->email }}
                                </option>
                            @endforeach
                        </select>

                        @error('assigned_to')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                        @if ($fieldOfficers->isEmpty())
                            <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 p-3">
                                <p class="text-sm text-amber-700">
                                    Belum ada pengguna dengan role Petugas Lapangan.
                                </p>
                            </div>
                        @else
                            <p class="mt-2 text-xs text-slate-500">
                                Petugas Lapangan yang dipilih akan mendapatkan akses ke Project ini.
                            </p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label
                                for="start_date"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                            >
                                Tanggal Mulai
                            </label>

                            <input
                                type="date"
                                id="start_date"
                                name="start_date"
                                value="{{ old('start_date') }}"
                                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            >

                            @error('start_date')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="deadline"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                            >
                                Deadline
                            </label>

                            <input
                                type="date"
                                id="deadline"
                                name="deadline"
                                value="{{ old('deadline') }}"
                                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            >

                            <p class="mt-2 text-xs text-slate-500">
                                Deadline tidak boleh lebih awal dari tanggal mulai.
                            </p>

                            @error('deadline')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label
                            for="notes"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Catatan
                        </label>

                        <textarea
                            id="notes"
                            name="notes"
                            rows="4"
                            placeholder="Catatan Project jika diperlukan..."
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >{{ old('notes') }}</textarea>

                        @error('notes')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                    <p class="text-sm font-semibold text-blue-800">
                        Status Awal Project
                    </p>

                    <p class="mt-1 text-sm leading-6 text-blue-700">
                        Setelah dibuat, Project akan memiliki status Planning dengan progress 0%. Project baru dinyatakan selesai setelah seluruh proses operasional selesai dan BAPP mendapatkan persetujuan.
                    </p>
                </div>

                <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">
                    <a
                        href="{{ route('projects.index') }}"
                        class="rounded-lg border border-slate-300 bg-white px-5 py-3 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        Buat Project
                    </button>
                </div>
            </form>
        @endif
    </div>

    @if ($workEstimates->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const workEstimates = @json($workEstimateData);
                const oldWorkEstimateId = @json($oldWorkEstimateId);

                const workEstimateSelect = document.getElementById('work_estimate_id');
                const titleInput = document.getElementById('title');
                const infoContainer = document.getElementById('work-estimate-info');
                const estimateNumber = document.getElementById('info-estimate-number');
                const orderNumber = document.getElementById('info-order-number');
                const company = document.getElementById('info-company');
                const event = document.getElementById('info-event');
                const survey = document.getElementById('info-survey');
                const total = document.getElementById('info-total');

                const rupiah = function (value) {
                    return 'Rp' + new Intl.NumberFormat('id-ID', {
                        maximumFractionDigits: 0
                    }).format(value);
                };

                function updateWorkEstimateInfo() {
                    const selected = workEstimates.find(function (item) {
                        return String(item.id) === String(workEstimateSelect.value);
                    });

                    if (!selected) {
                        infoContainer.classList.add('hidden');
                        estimateNumber.textContent = '-';
                        orderNumber.textContent = '-';
                        company.textContent = '-';
                        event.textContent = '-';
                        survey.textContent = '-';
                        total.textContent = 'Rp0';
                        return;
                    }

                    estimateNumber.textContent = selected.estimate_number;
                    orderNumber.textContent = selected.order_number;
                    company.textContent = selected.company_name;
                    event.textContent = selected.event_name;
                    survey.textContent = selected.survey_location;
                    total.textContent = rupiah(selected.total_amount);

                    infoContainer.classList.remove('hidden');

                    if (!titleInput.value.trim()) {
                        titleInput.value = selected.title;
                    }
                }

                workEstimateSelect.addEventListener('change', function () {
                    const previousTitle = titleInput.value.trim();

                    updateWorkEstimateInfo();

                    const selected = workEstimates.find(function (item) {
                        return String(item.id) === String(workEstimateSelect.value);
                    });

                    if (
                        selected &&
                        (
                            previousTitle === '' ||
                            workEstimates.some(function (item) {
                                return item.title === previousTitle;
                            })
                        )
                    ) {
                        titleInput.value = selected.title;
                    }
                });

                if (oldWorkEstimateId) {
                    workEstimateSelect.value = oldWorkEstimateId;
                }

                updateWorkEstimateInfo();
            });
        </script>
    @endif
@endsection