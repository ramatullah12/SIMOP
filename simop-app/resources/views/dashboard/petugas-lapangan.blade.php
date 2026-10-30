@extends('layouts.app')

@section('title', 'Dashboard Petugas Lapangan - SIMOP')
@section('page-title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                Dashboard Petugas Lapangan
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Pantau survey, tugas lapangan, produksi, dan progress pekerjaan Anda.
            </p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Survey Hari Ini
                        </p>

                        <p class="mt-3 text-3xl font-bold text-slate-900">
                            {{ $surveyHariIni }}
                        </p>
                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-xl text-blue-600">
                        ◎
                    </div>
                </div>

                <p class="mt-4 text-xs text-slate-400">
                    Survey yang dijadwalkan hari ini
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Belum Dilaporkan
                        </p>

                        <p class="mt-3 text-3xl font-bold text-slate-900">
                            {{ $belumDilaporkan }}
                        </p>
                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-xl text-amber-600">
                        !
                    </div>
                </div>

                <p class="mt-4 text-xs text-slate-400">
                    Survey yang masih perlu diselesaikan
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Tugas Aktif
                        </p>

                        <p class="mt-3 text-3xl font-bold text-slate-900">
                            {{ $tugasAktif }}
                        </p>
                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-100 text-xl text-violet-600">
                        ◈
                    </div>
                </div>

                <p class="mt-4 text-xs text-slate-400">
                    Produksi dan pekerjaan aktif
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Progress Rata-rata
                        </p>

                        <p class="mt-3 text-3xl font-bold text-slate-900">
                            {{ $progressRataRata }}%
                        </p>
                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-100 text-xl text-green-600">
                        ✓
                    </div>
                </div>

                <p class="mt-4 text-xs text-slate-400">
                    Rata-rata progress pekerjaan Anda
                </p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
                <div class="flex flex-col gap-3 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">
                            Jadwal Hari Ini
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Daftar survey yang ditugaskan kepada Anda hari ini.
                        </p>
                    </div>

                    @if (\Illuminate\Support\Facades\Route::has('admin.surveys.index'))
                        <a
                            href="{{ route('admin.surveys.index') }}"
                            class="text-sm font-semibold text-blue-600 hover:text-blue-700"
                        >
                            Lihat Semua Survey
                        </a>
                    @endif
                </div>

                <div class="p-6">
                    @forelse ($jadwalHariIni as $survey)
                        @php
                            $statusClass = match ($survey->status) {
                                'dijadwalkan' => 'bg-blue-100 text-blue-700',
                                'berlangsung' => 'bg-amber-100 text-amber-700',
                                'selesai' => 'bg-green-100 text-green-700',
                                'dibatalkan' => 'bg-red-100 text-red-700',
                                default => 'bg-slate-100 text-slate-700',
                            };

                            $statusLabel = match ($survey->status) {
                                'dijadwalkan' => 'Dijadwalkan',
                                'berlangsung' => 'Berlangsung',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                                default => ucfirst($survey->status),
                            };
                        @endphp

                        <div class="border-b border-slate-100 py-5 first:pt-0 last:border-b-0 last:pb-0">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex gap-4">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-100 font-semibold text-blue-700">
                                        {{ $survey->scheduled_at?->format('H:i') ?? '--:--' }}
                                    </div>

                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="font-semibold text-slate-900">
                                                {{ $survey->order?->event_name ?? 'Survey Lapangan' }}
                                            </h3>

                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </div>

                                        <p class="mt-2 text-sm text-slate-500">
                                            {{ $survey->location ?? $survey->order?->event_location ?? 'Lokasi belum ditentukan' }}
                                        </p>

                                        @if ($survey->order?->company_name)
                                            <p class="mt-1 text-xs text-slate-400">
                                                {{ $survey->order->company_name }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                @if (\Illuminate\Support\Facades\Route::has('admin.surveys.show'))
                                    <a
                                        href="{{ route('admin.surveys.show', $survey) }}"
                                        class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100"
                                    >
                                        Detail
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 px-6 py-12 text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-400">
                                ◎
                            </div>

                            <h3 class="mt-4 font-semibold text-slate-800">
                                Belum Ada Jadwal Hari Ini
                            </h3>

                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                                Survey yang ditugaskan kepada Anda untuk hari ini akan ditampilkan di bagian ini.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">
                    Akses Cepat
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Buka modul pekerjaan lapangan.
                </p>

                <div class="mt-5 space-y-3">
                    @if (\Illuminate\Support\Facades\Route::has('admin.surveys.index'))
                        <a
                            href="{{ route('admin.surveys.index') }}"
                            class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-4 transition hover:border-blue-300 hover:bg-blue-50"
                        >
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                                    ◎
                                </span>

                                <span class="text-sm font-semibold text-slate-800">
                                    Survey
                                </span>
                            </div>

                            <span class="text-slate-400">
                                →
                            </span>
                        </a>
                    @endif

                    @if (\Illuminate\Support\Facades\Route::has('productions.index'))
                        <a
                            href="{{ route('productions.index') }}"
                            class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-4 transition hover:border-blue-300 hover:bg-blue-50"
                        >
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-violet-100 text-violet-600">
                                    ◈
                                </span>

                                <span class="text-sm font-semibold text-slate-800">
                                    Produksi & Instalasi
                                </span>
                            </div>

                            <span class="text-slate-400">
                                →
                            </span>
                        </a>
                    @endif

                    @if (\Illuminate\Support\Facades\Route::has('bapps.index'))
                        <a
                            href="{{ route('bapps.index') }}"
                            class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-4 transition hover:border-blue-300 hover:bg-blue-50"
                        >
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-green-100 text-green-600">
                                    ✓
                                </span>

                                <span class="text-sm font-semibold text-slate-800">
                                    BAPP
                                </span>
                            </div>

                            <span class="text-slate-400">
                                →
                            </span>
                        </a>
                    @endif

                    @if (\Illuminate\Support\Facades\Route::has('projects.index'))
                        <a
                            href="{{ route('projects.index') }}"
                            class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-4 transition hover:border-blue-300 hover:bg-blue-50"
                        >
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                                    ▤
                                </span>

                                <span class="text-sm font-semibold text-slate-800">
                                    Project
                                </span>
                            </div>

                            <span class="text-slate-400">
                                →
                            </span>
                        </a>
                    @endif

                    @if (\Illuminate\Support\Facades\Route::has('documents.index'))
                        <a
                            href="{{ route('documents.index') }}"
                            class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-4 transition hover:border-blue-300 hover:bg-blue-50"
                        >
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                                    ▧
                                </span>

                                <span class="text-sm font-semibold text-slate-800">
                                    Dokumen
                                </span>
                            </div>

                            <span class="text-slate-400">
                                →
                            </span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-700">
                    i
                </div>

                <div>
                    <h2 class="font-semibold text-blue-800">
                        Informasi Petugas Lapangan
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-blue-700">
                        Survey, Project, Produksi dan Instalasi yang ditampilkan pada akun Anda hanya pekerjaan yang telah ditugaskan kepada Anda.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection