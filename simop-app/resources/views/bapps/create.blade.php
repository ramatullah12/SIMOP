@extends('layouts.app')

@section('title', 'Buat BAPP | SIMOP')
@section('page-title', 'Buat BAPP')

@section('content')
    @php
        $productionData = $productions->map(function ($production) {
            $planning = $production->planning;
            $project = $planning?->project;
            $order = $project?->workEstimate?->order;

            return [
                'id' => $production->id,
                'production_number' => $production->production_number,
                'production_title' => $production->title,
                'progress' => (int) $production->progress,
                'project_number' => $project?->project_number ?? '-',
                'project_title' => $project?->title ?? '-',
                'planning_number' => $planning?->planning_number ?? '-',
                'order_number' => $order?->order_number ?? '-',
                'assigned_officer' => $production->assignedUser?->name ?? 'Belum ditentukan',
                'start_date' => $production->start_date?->format('d/m/Y') ?? '-',
                'end_date' => $production->end_date?->format('d/m/Y') ?? '-',
            ];
        })->values();

        $oldProductionId = (string) old('production_id', '');
    @endphp

    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <a
                href="{{ route('bapps.index') }}"
                class="text-sm font-semibold text-blue-600 hover:text-blue-800"
            >
                &larr; Kembali ke BAPP
            </a>

            <h2 class="mt-3 text-2xl font-bold text-slate-900">
                Buat BAPP
            </h2>

            <p class="mt-1 text-sm leading-6 text-slate-500">
                Buat Berita Acara Penyelesaian Pekerjaan dari Produksi & Instalasi yang telah selesai 100%.
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

        @if ($productions->isEmpty())
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-6">
                <div class="flex gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 font-bold text-amber-700">
                        !
                    </div>

                    <div>
                        <h3 class="font-semibold text-amber-800">
                            Belum Ada Pekerjaan yang Dapat Dibuatkan BAPP
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-amber-700">
                            Produksi & Instalasi harus berstatus Selesai, memiliki progress 100%, belum memiliki BAPP, dan Project masih aktif.
                        </p>

                        <a
                            href="{{ route('productions.index') }}"
                            class="mt-4 inline-flex text-sm font-semibold text-amber-800 underline hover:text-amber-900"
                        >
                            Lihat Produksi & Instalasi
                        </a>
                    </div>
                </div>
            </div>
        @else
            <form
                method="POST"
                action="{{ route('bapps.store') }}"
                class="space-y-6"
            >
                @csrf

                <div class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Produksi & Instalasi
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Pilih pekerjaan yang telah selesai 100% untuk dibuatkan BAPP.
                        </p>
                    </div>

                    <div>
                        <label
                            for="production_id"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Pekerjaan <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="production_id"
                            name="production_id"
                            required
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >
                            <option value="">
                                -- Pilih Produksi & Instalasi --
                            </option>

                            @foreach ($productions as $production)
                                <option
                                    value="{{ $production->id }}"
                                    @selected(old('production_id') == $production->id)
                                >
                                    {{ $production->production_number }}
                                    — {{ $production->title }}
                                    — {{ $production->planning?->project?->project_number ?? '-' }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs text-slate-500">
                            Hanya pekerjaan Selesai dengan progress 100% yang belum memiliki BAPP yang ditampilkan.
                        </p>

                        @error('production_id')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div
                        id="production-info"
                        class="hidden rounded-xl border border-green-200 bg-green-50 p-5"
                    >
                        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
                            <div>
                                <p
                                    id="info-production-title"
                                    class="font-semibold text-green-950"
                                >
                                    -
                                </p>

                                <p
                                    id="info-production-number"
                                    class="mt-1 text-sm text-green-700"
                                >
                                    -
                                </p>
                            </div>

                            <span class="inline-flex w-fit rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                Selesai 100%
                            </span>
                        </div>

                        <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-green-600">
                                    Project
                                </p>

                                <p
                                    id="info-project"
                                    class="mt-1 text-sm font-semibold text-green-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-green-600">
                                    Planning
                                </p>

                                <p
                                    id="info-planning"
                                    class="mt-1 text-sm font-semibold text-green-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-green-600">
                                    Order
                                </p>

                                <p
                                    id="info-order"
                                    class="mt-1 text-sm font-semibold text-green-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-green-600">
                                    Petugas Lapangan
                                </p>

                                <p
                                    id="info-officer"
                                    class="mt-1 text-sm font-semibold text-green-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-green-600">
                                    Tanggal Mulai
                                </p>

                                <p
                                    id="info-start-date"
                                    class="mt-1 text-sm font-semibold text-green-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-green-600">
                                    Tanggal Selesai
                                </p>

                                <p
                                    id="info-end-date"
                                    class="mt-1 text-sm font-semibold text-green-950"
                                >
                                    -
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Informasi BAPP
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Lengkapi informasi penyelesaian pekerjaan.
                        </p>
                    </div>

                    <div>
                        <label
                            for="completion_date"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Tanggal Penyelesaian Pekerjaan
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="date"
                            id="completion_date"
                            name="completion_date"
                            value="{{ old('completion_date') }}"
                            required
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >

                        @error('completion_date')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="location"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Lokasi Penyelesaian
                        </label>

                        <input
                            type="text"
                            id="location"
                            name="location"
                            value="{{ old('location') }}"
                            placeholder="Contoh: Palembang"
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >

                        @error('location')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="work_result"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Hasil Pekerjaan
                        </label>

                        <textarea
                            id="work_result"
                            name="work_result"
                            rows="5"
                            placeholder="Jelaskan hasil Produksi & Instalasi yang telah diselesaikan..."
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >{{ old('work_result') }}</textarea>

                        @error('work_result')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
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
                            placeholder="Catatan tambahan jika diperlukan..."
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
                        Alur Persetujuan BAPP
                    </p>

                    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div class="rounded-lg border border-blue-200 bg-white p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Tahap 1
                            </p>

                            <p class="mt-1 text-sm font-bold text-slate-800">
                                Draft
                            </p>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                BAPP dibuat dan diperiksa terlebih dahulu.
                            </p>
                        </div>

                        <div class="rounded-lg border border-blue-200 bg-white p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Tahap 2
                            </p>

                            <p class="mt-1 text-sm font-bold text-amber-700">
                                Diajukan
                            </p>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                BAPP diajukan setelah data dipastikan benar.
                            </p>
                        </div>

                        <div class="rounded-lg border border-blue-200 bg-white p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Tahap 3
                            </p>

                            <p class="mt-1 text-sm font-bold text-green-700">
                                Disetujui
                            </p>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Persetujuan BAPP menyelesaikan Project.
                            </p>
                        </div>
                    </div>

                    <p class="mt-4 text-sm leading-6 text-blue-700">
                        BAPP baru akan dibuat dengan status Draft. Project tetap berjalan sampai BAPP berstatus Disetujui.
                    </p>
                </div>

                <div class="flex flex-col-reverse justify-end gap-3 border-t border-slate-200 pt-5 sm:flex-row">
                    <a
                        href="{{ route('bapps.index') }}"
                        class="rounded-lg border border-slate-300 bg-white px-5 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        Buat BAPP
                    </button>
                </div>
            </form>
        @endif
    </div>

    @if ($productions->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const productions = @json($productionData);
                const oldProductionId = @json($oldProductionId);

                const productionSelect = document.getElementById('production_id');
                const productionInfo = document.getElementById('production-info');

                const productionTitle = document.getElementById('info-production-title');
                const productionNumber = document.getElementById('info-production-number');
                const project = document.getElementById('info-project');
                const planning = document.getElementById('info-planning');
                const order = document.getElementById('info-order');
                const officer = document.getElementById('info-officer');
                const startDate = document.getElementById('info-start-date');
                const endDate = document.getElementById('info-end-date');

                function getSelectedProduction() {
                    return productions.find(function (production) {
                        return String(production.id) === String(productionSelect.value);
                    });
                }

                function updateProductionInfo() {
                    const selected = getSelectedProduction();

                    if (!selected) {
                        productionInfo.classList.add('hidden');
                        return;
                    }

                    productionTitle.textContent = selected.production_title;
                    productionNumber.textContent = selected.production_number;
                    project.textContent =
                        selected.project_number + ' — ' + selected.project_title;
                    planning.textContent = selected.planning_number;
                    order.textContent = selected.order_number;
                    officer.textContent = selected.assigned_officer;
                    startDate.textContent = selected.start_date;
                    endDate.textContent = selected.end_date;

                    productionInfo.classList.remove('hidden');
                }

                productionSelect.addEventListener(
                    'change',
                    updateProductionInfo
                );

                if (oldProductionId) {
                    productionSelect.value = oldProductionId;
                }

                updateProductionInfo();
            });
        </script>
    @endif
@endsection