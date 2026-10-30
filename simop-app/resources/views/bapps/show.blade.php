@extends('layouts.app')

@section('title', 'Detail BAPP | SIMOP')
@section('page-title', 'Detail BAPP')

@section('content')
@php
    $role = auth()->user()?->role?->slug;
    $production = $bapp->production;
    $planning = $production?->planning;
    $project = $planning?->project;
@endphp

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
        <a
            href="{{ route('bapps.index') }}"
            class="text-sm font-semibold text-blue-600 hover:text-blue-800"
        >
            ← Kembali ke BAPP
        </a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

            <div>
                <p class="text-sm font-medium text-slate-500">
                    Berita Acara Penyelesaian Pekerjaan
                </p>

                <h2 class="mt-1 text-2xl font-bold text-slate-900">
                    {{ $bapp->bapp_number }}
                </h2>

                <p class="mt-2 text-sm text-slate-500">
                    Dibuat oleh
                    <span class="font-semibold text-slate-700">
                        {{ $bapp->creator->name ?? '-' }}
                    </span>
                </p>
            </div>

            <div>
                @if ($bapp->status === 'draft')
                    <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                        Draft
                    </span>
                @elseif ($bapp->status === 'submitted')
                    <span class="rounded-full bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-700">
                        Diajukan
                    </span>
                @elseif ($bapp->status === 'approved')
                    <span class="rounded-full bg-green-100 px-4 py-2 text-sm font-semibold text-green-700">
                        Disetujui
                    </span>
                @else
                    <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                        {{ ucfirst($bapp->status) }}
                    </span>
                @endif
            </div>

        </div>
    </div>

    @if ($bapp->status === 'draft')
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-200 font-bold text-slate-700">
                    1
                </div>

                <div>
                    <h3 class="font-bold text-slate-800">
                        BAPP masih Draft
                    </h3>

                    <p class="mt-1 text-sm leading-6 text-slate-600">
                        Data BAPP telah dibuat dan belum diajukan. Admin dapat mengajukan BAPP untuk melanjutkan proses penyelesaian Project.
                    </p>
                </div>
            </div>
        </div>
    @elseif ($bapp->status === 'submitted')
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 font-bold text-amber-700">
                    2
                </div>

                <div>
                    <h3 class="font-bold text-amber-800">
                        BAPP telah diajukan
                    </h3>

                    <p class="mt-1 text-sm leading-6 text-amber-700">
                        BAPP telah masuk ke tahap pengajuan dan siap disetujui oleh Admin.
                    </p>
                </div>
            </div>
        </div>
    @elseif ($bapp->status === 'approved')
        <div class="rounded-xl border border-green-200 bg-green-50 p-5">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-100 font-bold text-green-700">
                    ✓
                </div>

                <div>
                    <h3 class="font-bold text-green-800">
                        BAPP telah disetujui
                    </h3>

                    <p class="mt-1 text-sm leading-6 text-green-700">
                        BAPP telah disahkan. Project telah dinyatakan selesai dengan progress 100%.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900">
                Informasi BAPP
            </h3>

            <div class="mt-5 space-y-5">

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Nomor BAPP
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $bapp->bapp_number }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Tanggal Penyelesaian
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $bapp->completion_date?->format('d/m/Y') ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Lokasi
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $bapp->location ?: '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Hasil Pekerjaan
                    </p>

                    <p class="mt-1 whitespace-pre-line text-sm text-slate-700">
                        {{ $bapp->work_result ?: '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Catatan
                    </p>

                    <p class="mt-1 whitespace-pre-line text-sm text-slate-700">
                        {{ $bapp->notes ?: '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Dibuat Oleh
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $bapp->creator->name ?? '-' }}
                    </p>
                </div>

            </div>
        </div>

        <div class="space-y-6">

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900">
                    Pekerjaan
                </h3>

                <div class="mt-5 space-y-5">

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">
                            Produksi & Instalasi
                        </p>

                        @if ($production)
                            <a
                                href="{{ route('productions.show', $production) }}"
                                class="mt-1 inline-block text-sm font-semibold text-blue-600 hover:text-blue-800"
                            >
                                {{ $production->production_number }}
                                - {{ $production->title }}
                            </a>
                        @else
                            <p class="mt-1 text-sm text-slate-500">
                                -
                            </p>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">
                            Project
                        </p>

                        @if ($project)
                            <a
                                href="{{ route('projects.show', $project) }}"
                                class="mt-1 inline-block text-sm font-semibold text-blue-600 hover:text-blue-800"
                            >
                                {{ $project->project_number }}
                                - {{ $project->title }}
                            </a>
                        @else
                            <p class="mt-1 text-sm text-slate-500">
                                -
                            </p>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">
                            Petugas Lapangan
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $production?->assignedUser?->name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">
                            Progress Pekerjaan
                        </p>

                        <div class="mt-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-slate-700">
                                    Produksi & Instalasi
                                </span>

                                <span class="text-sm font-bold text-green-600">
                                    {{ $production?->progress ?? 0 }}%
                                </span>
                            </div>

                            <div class="mt-2 h-2.5 overflow-hidden rounded-full bg-slate-200">
                                <div
                                    class="h-full rounded-full bg-green-500"
                                    style="width: {{ min(100, max(0, $production?->progress ?? 0)) }}%"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">
                            Status Produksi
                        </p>

                        <div class="mt-2">
                            @if ($production?->status === 'completed')
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                    Selesai
                                </span>
                            @elseif ($production?->status === 'in_progress')
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                    Dalam Pengerjaan
                                </span>
                            @elseif ($production?->status === 'cancelled')
                                <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                    Dibatalkan
                                </span>
                            @else
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                    Menunggu
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">
                            Status Project
                        </p>

                        <div class="mt-2">
                            @if ($project?->status === 'planning')
                                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                    Planning
                                </span>
                            @elseif ($project?->status === 'ongoing')
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                    Dalam Pengerjaan
                                </span>
                            @elseif ($project?->status === 'completed')
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                    Selesai
                                </span>
                            @elseif ($project?->status === 'cancelled')
                                <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                    Dibatalkan
                                </span>
                            @else
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                    -
                                </span>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900">
                    Alur BAPP
                </h3>

                <div class="mt-6 space-y-0">

                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-500 text-sm font-bold text-white">
                                ✓
                            </div>

                            <div class="h-10 w-0.5 bg-green-300"></div>
                        </div>

                        <div class="pb-5">
                            <p class="font-semibold text-green-700">
                                Draft
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                BAPP telah dibuat.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            @if (in_array($bapp->status, ['submitted', 'approved'], true))
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-500 text-sm font-bold text-white">
                                    ✓
                                </div>
                            @else
                                <div class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-blue-500 bg-white text-sm font-bold text-blue-600">
                                    2
                                </div>
                            @endif

                            <div class="h-10 w-0.5 {{ in_array($bapp->status, ['submitted', 'approved'], true) ? 'bg-green-300' : 'bg-slate-200' }}"></div>
                        </div>

                        <div class="pb-5">
                            <p class="font-semibold {{ in_array($bapp->status, ['submitted', 'approved'], true) ? 'text-green-700' : 'text-slate-600' }}">
                                Diajukan
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                BAPP diajukan untuk proses persetujuan.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div>
                            @if ($bapp->status === 'approved')
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-500 text-sm font-bold text-white">
                                    ✓
                                </div>
                            @else
                                <div class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-slate-300 bg-white text-sm font-bold text-slate-400">
                                    3
                                </div>
                            @endif
                        </div>

                        <div>
                            <p class="font-semibold {{ $bapp->status === 'approved' ? 'text-green-700' : 'text-slate-600' }}">
                                Disetujui
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Project dinyatakan selesai setelah BAPP disetujui.
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            @if ($role === 'admin' && $bapp->status === 'draft')
                <div class="rounded-xl border border-blue-200 bg-blue-50 p-6">
                    <h3 class="text-lg font-bold text-blue-900">
                        Ajukan BAPP
                    </h3>

                    <p class="mt-1 text-sm leading-6 text-blue-700">
                        Setelah diajukan, BAPP akan masuk ke tahap Submitted dan tidak dapat dikembalikan menjadi Draft.
                    </p>

                    <form
                        method="POST"
                        action="{{ route('bapps.status', $bapp) }}"
                        class="mt-5"
                    >
                        @csrf
                        @method('PATCH')

                        <input
                            type="hidden"
                            name="status"
                            value="submitted"
                        >

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                        >
                            Ajukan BAPP
                        </button>
                    </form>
                </div>
            @endif

            @if ($role === 'admin' && $bapp->status === 'submitted')
                <div class="rounded-xl border border-green-200 bg-green-50 p-6">
                    <h3 class="text-lg font-bold text-green-900">
                        Setujui BAPP
                    </h3>

                    <p class="mt-1 text-sm leading-6 text-green-700">
                        Persetujuan BAPP akan menyelesaikan Project dan mengubah progress Project menjadi 100%.
                    </p>

                    <form
                        method="POST"
                        action="{{ route('bapps.status', $bapp) }}"
                        class="mt-5"
                    >
                        @csrf
                        @method('PATCH')

                        <input
                            type="hidden"
                            name="status"
                            value="approved"
                        >

                        <button
                            type="submit"
                            onclick="return confirm('Setujui BAPP ini dan selesaikan Project?')"
                            class="inline-flex items-center justify-center rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-700"
                        >
                            Setujui BAPP
                        </button>
                    </form>
                </div>
            @endif

            @if ($bapp->status === 'approved')
                <div class="rounded-xl border border-green-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">
                        Status Akhir
                    </h3>

                    <div class="mt-4 flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-100 font-bold text-green-700">
                            ✓
                        </div>

                        <div>
                            <p class="font-semibold text-green-700">
                                BAPP Disetujui
                            </p>

                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Proses pelaksanaan pekerjaan telah selesai dan Project telah ditutup dengan progress 100%.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($role !== 'admin' && $bapp->status !== 'approved')
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                    <p class="text-sm font-semibold text-slate-700">
                        Status BAPP
                    </p>

                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Perubahan status BAPP hanya dapat dilakukan oleh Administrator.
                    </p>
                </div>
            @endif

        </div>
    </div>

</div>
@endsection