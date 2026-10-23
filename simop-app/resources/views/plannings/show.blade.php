@extends('layouts.app')

@section('title', 'Detail Planning | SIMOP')
@section('page-title', 'Detail Planning')

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
        <a href="{{ route('plannings.index') }}"
           class="text-sm font-semibold text-blue-600 hover:text-blue-800">
            ← Kembali ke Planning
        </a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">

            <div>
                <p class="text-sm font-medium text-slate-500">
                    {{ $planning->planning_number }}
                </p>

                <h2 class="mt-1 text-2xl font-bold text-slate-900">
                    {{ $planning->title }}
                </h2>

                <p class="mt-2 text-sm text-slate-500">
                    Project:
                    <span class="font-semibold text-slate-700">
                        {{ $planning->project->project_number }}
                        - {{ $planning->project->title }}
                    </span>
                </p>
            </div>

            <div>
                @if ($planning->status === 'draft')
                    <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                        Draft
                    </span>
                @elseif ($planning->status === 'ready')
                    <span class="rounded-full bg-blue-100 px-4 py-2 text-sm font-semibold text-blue-700">
                        Siap
                    </span>
                @elseif ($planning->status === 'in_progress')
                    <span class="rounded-full bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-700">
                        Berjalan
                    </span>
                @elseif ($planning->status === 'completed')
                    <span class="rounded-full bg-green-100 px-4 py-2 text-sm font-semibold text-green-700">
                        Selesai
                    </span>
                @elseif ($planning->status === 'cancelled')
                    <span class="rounded-full bg-red-100 px-4 py-2 text-sm font-semibold text-red-700">
                        Dibatalkan
                    </span>
                @endif
            </div>

        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

            <h3 class="text-lg font-bold text-slate-900">
                Informasi Planning
            </h3>

            <div class="mt-5 space-y-5">

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Nomor Planning
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $planning->planning_number }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Rencana Mulai
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $planning->planned_start_date?->format('d/m/Y') ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Rencana Selesai
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $planning->planned_end_date?->format('d/m/Y') ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Dibuat Oleh
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $planning->creator->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Deskripsi
                    </p>

                    <p class="mt-1 whitespace-pre-line text-sm text-slate-700">
                        {{ $planning->description ?: '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Catatan
                    </p>

                    <p class="mt-1 whitespace-pre-line text-sm text-slate-700">
                        {{ $planning->notes ?: '-' }}
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
                            Nomor Project
                        </p>

                        <a href="{{ route('projects.show', $planning->project) }}"
                           class="mt-1 inline-block text-sm font-semibold text-blue-600 hover:text-blue-800">
                            {{ $planning->project->project_number }}
                        </a>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">
                            Nama Project
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $planning->project->title }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">
                            Petugas Lapangan
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $planning->project->assignedUser->name ?? 'Belum ditentukan' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">
                            Progress Project
                        </p>

                        <div class="mt-2 flex items-center gap-3">

                            <div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-200">
                                <div class="h-full rounded-full bg-blue-600"
                                     style="width: {{ $planning->project->progress }}%">
                                </div>
                            </div>

                            <span class="text-sm font-semibold text-slate-700">
                                {{ $planning->project->progress }}%
                            </span>

                        </div>
                    </div>

                </div>
            </div>

            @if (
                auth()->user()?->role?->slug === 'admin'
                || (
                    auth()->user()?->role?->slug === 'petugas-lapangan'
                    && $planning->project->assigned_to === auth()->id()
                )
            )

                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

                    <h3 class="text-lg font-bold text-slate-900">
                        Status Planning
                    </h3>

                    <form method="POST"
                          action="{{ route('plannings.status', $planning) }}"
                          class="mt-5">

                        @csrf
                        @method('PATCH')

                        <label for="status"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Ubah Status
                        </label>

                        <div class="flex flex-col gap-3 sm:flex-row">

                            <select id="status"
                                    name="status"
                                    required
                                    class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                                @if (auth()->user()?->role?->slug === 'admin')
                                    <option value="draft"
                                        @selected($planning->status === 'draft')>
                                        Draft
                                    </option>
                                @endif

                                <option value="ready"
                                    @selected($planning->status === 'ready')>
                                    Siap
                                </option>

                                <option value="in_progress"
                                    @selected($planning->status === 'in_progress')>
                                    Berjalan
                                </option>

                                <option value="completed"
                                    @selected($planning->status === 'completed')>
                                    Selesai
                                </option>

                                @if (auth()->user()?->role?->slug === 'admin')
                                    <option value="cancelled"
                                        @selected($planning->status === 'cancelled')>
                                        Dibatalkan
                                    </option>
                                @endif

                            </select>

                            <button type="submit"
                                    class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                                Update
                            </button>

                        </div>
                    </form>

                </div>

            @endif

        </div>
    </div>

</div>
@endsection