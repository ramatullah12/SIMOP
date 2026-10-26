@extends('layouts.app')

@section('title', 'Detail Produksi & Instalasi | SIMOP')
@section('page-title', 'Detail Produksi & Instalasi')

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
        <a href="{{ route('productions.index') }}"
           class="text-sm font-semibold text-blue-600 hover:text-blue-800">
            ← Kembali ke Produksi & Instalasi
        </a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">

            <div>
                <p class="text-sm font-medium text-slate-500">
                    {{ $production->production_number }}
                </p>

                <h2 class="mt-1 text-2xl font-bold text-slate-900">
                    {{ $production->title }}
                </h2>

                <p class="mt-2 text-sm text-slate-500">
                    Planning:
                    <span class="font-semibold text-slate-700">
                        {{ $production->planning->planning_number }}
                    </span>
                </p>
            </div>

            <div>
                @if ($production->status === 'pending')
                    <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                        Menunggu
                    </span>
                @elseif ($production->status === 'in_progress')
                    <span class="rounded-full bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-700">
                        Berjalan
                    </span>
                @elseif ($production->status === 'completed')
                    <span class="rounded-full bg-green-100 px-4 py-2 text-sm font-semibold text-green-700">
                        Selesai
                    </span>
                @elseif ($production->status === 'cancelled')
                    <span class="rounded-full bg-red-100 px-4 py-2 text-sm font-semibold text-red-700">
                        Dibatalkan
                    </span>
                @endif
            </div>

        </div>

        <div class="mt-6">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm font-semibold text-slate-700">
                    Progress Pekerjaan
                </span>

                <span class="text-sm font-bold text-blue-600">
                    {{ $production->progress }}%
                </span>
            </div>

            <div class="h-3 overflow-hidden rounded-full bg-slate-200">
                <div class="h-full rounded-full bg-blue-600"
                     style="width: {{ $production->progress }}%">
                </div>
            </div>
        </div>
    </div>

    @if ($production->status === 'completed')
        <div class="rounded-xl border border-green-200 bg-green-50 p-5">
            <h3 class="font-bold text-green-800">
                Produksi & Instalasi telah selesai
            </h3>

            <p class="mt-1 text-sm text-green-700">
                Progress pekerjaan telah mencapai 100%. Tahap berikutnya adalah pembuatan BAPP.
            </p>

            @if (auth()->user()?->role?->slug === 'admin')
                <a href="{{ route('bapps.create') }}"
                   class="mt-4 inline-flex rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700">
                    Buat BAPP
                </a>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900">
                Informasi Pekerjaan
            </h3>

            <div class="mt-5 space-y-5">

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Nomor Produksi
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $production->production_number }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Petugas Lapangan
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $production->assignedUser->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Tanggal Mulai
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $production->start_date?->format('d/m/Y') ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Tanggal Selesai
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $production->end_date?->format('d/m/Y') ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Dibuat Oleh
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $production->creator->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Deskripsi
                    </p>

                    <p class="mt-1 whitespace-pre-line text-sm text-slate-700">
                        {{ $production->description ?: '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Catatan
                    </p>

                    <p class="mt-1 whitespace-pre-line text-sm text-slate-700">
                        {{ $production->notes ?: '-' }}
                    </p>
                </div>

            </div>
        </div>

        <div class="space-y-6">

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900">
                    Project & Planning
                </h3>

                <div class="mt-5 space-y-5">

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">
                            Project
                        </p>

                        <a href="{{ route('projects.show', $production->planning->project) }}"
                           class="mt-1 inline-block text-sm font-semibold text-blue-600 hover:text-blue-800">
                            {{ $production->planning->project->project_number }}
                            - {{ $production->planning->project->title }}
                        </a>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">
                            Planning
                        </p>

                        <a href="{{ route('plannings.show', $production->planning) }}"
                           class="mt-1 inline-block text-sm font-semibold text-blue-600 hover:text-blue-800">
                            {{ $production->planning->planning_number }}
                            - {{ $production->planning->title }}
                        </a>
                    </div>

                </div>
            </div>

            @if (
                $production->status !== 'cancelled'
                && (
                    auth()->user()?->role?->slug === 'admin'
                    || (
                        auth()->user()?->role?->slug === 'petugas-lapangan'
                        && $production->assigned_to === auth()->id()
                    )
                )
            )
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

                    <h3 class="text-lg font-bold text-slate-900">
                        Update Progress
                    </h3>

                    <form method="POST"
                          action="{{ route('productions.progress', $production) }}"
                          class="mt-5">

                        @csrf
                        @method('PATCH')

                        <label for="progress"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Progress Pekerjaan
                        </label>

                        <div class="flex flex-col gap-3 sm:flex-row">

                            <input type="number"
                                   id="progress"
                                   name="progress"
                                   min="0"
                                   max="100"
                                   value="{{ $production->progress }}"
                                   required
                                   class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                            <button type="submit"
                                    class="whitespace-nowrap rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                                Update Progress
                            </button>

                        </div>

                        <p class="mt-2 text-xs text-slate-500">
                            Masukkan nilai progress antara 0 sampai 100.
                        </p>

                    </form>
                </div>
            @endif

            @if (auth()->user()?->role?->slug === 'admin')
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

                    <h3 class="text-lg font-bold text-slate-900">
                        Status Pekerjaan
                    </h3>

                    <form method="POST"
                          action="{{ route('productions.status', $production) }}"
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

                                <option value="pending"
                                    @selected($production->status === 'pending')>
                                    Menunggu
                                </option>

                                <option value="in_progress"
                                    @selected($production->status === 'in_progress')>
                                    Berjalan
                                </option>

                                <option value="completed"
                                    @selected($production->status === 'completed')>
                                    Selesai
                                </option>

                                <option value="cancelled"
                                    @selected($production->status === 'cancelled')>
                                    Dibatalkan
                                </option>

                            </select>

                            <button type="submit"
                                    class="whitespace-nowrap rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                                Update Status
                            </button>

                        </div>

                    </form>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection