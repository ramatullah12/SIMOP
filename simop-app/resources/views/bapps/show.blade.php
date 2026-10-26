@extends('layouts.app')

@section('title', 'Detail BAPP | SIMOP')
@section('page-title', 'Detail BAPP')

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
        <a href="{{ route('bapps.index') }}"
           class="text-sm font-semibold text-blue-600 hover:text-blue-800">
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
                @endif
            </div>

        </div>
    </div>

    @if ($bapp->status === 'approved')
        <div class="rounded-xl border border-green-200 bg-green-50 p-5">
            <h3 class="font-bold text-green-800">
                BAPP telah disetujui
            </h3>

            <p class="mt-1 text-sm text-green-700">
                BAPP telah disahkan dan Project telah dinyatakan selesai dengan progress 100%.
            </p>
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

                        <a href="{{ route('productions.show', $bapp->production) }}"
                           class="mt-1 inline-block text-sm font-semibold text-blue-600 hover:text-blue-800">
                            {{ $bapp->production->production_number }}
                            - {{ $bapp->production->title }}
                        </a>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">
                            Project
                        </p>

                        <a href="{{ route('projects.show', $bapp->production->planning->project) }}"
                           class="mt-1 inline-block text-sm font-semibold text-blue-600 hover:text-blue-800">
                            {{ $bapp->production->planning->project->project_number }}
                            - {{ $bapp->production->planning->project->title }}
                        </a>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">
                            Petugas Lapangan
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $bapp->production->assignedUser->name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">
                            Progress Pekerjaan
                        </p>

                        <p class="mt-1 text-sm font-bold text-green-600">
                            {{ $bapp->production->progress }}%
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">
                            Status Project
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            @if ($bapp->production->planning->project->status === 'planning')
                                Planning
                            @elseif ($bapp->production->planning->project->status === 'ongoing')
                                Berjalan
                            @elseif ($bapp->production->planning->project->status === 'completed')
                                Selesai
                            @elseif ($bapp->production->planning->project->status === 'cancelled')
                                Dibatalkan
                            @else
                                {{ $bapp->production->planning->project->status }}
                            @endif
                        </p>
                    </div>

                </div>
            </div>

            @if (
                auth()->user()?->role?->slug === 'admin'
                && $bapp->status !== 'approved'
            )
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">
                        Status BAPP
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Proses BAPP dilakukan secara berurutan dari Draft, Diajukan, kemudian Disetujui.
                    </p>

                    <form method="POST"
                          action="{{ route('bapps.status', $bapp) }}"
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

                                @if ($bapp->status === 'draft')
                                    <option value="draft" selected>
                                        Draft
                                    </option>

                                    <option value="submitted">
                                        Diajukan
                                    </option>
                                @elseif ($bapp->status === 'submitted')
                                    <option value="submitted" selected>
                                        Diajukan
                                    </option>

                                    <option value="approved">
                                        Disetujui
                                    </option>
                                @endif

                            </select>

                            <button type="submit"
                                    class="whitespace-nowrap rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                                Update Status
                            </button>

                        </div>
                    </form>
                </div>
            @endif

            @if ($bapp->status === 'approved')
                <div class="rounded-xl border border-green-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">
                        Status Akhir
                    </h3>

                    <div class="mt-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 text-green-700">
                            ✓
                        </div>

                        <div>
                            <p class="font-semibold text-green-700">
                                BAPP Disetujui
                            </p>

                            <p class="text-sm text-slate-500">
                                Proses pelaksanaan pekerjaan telah selesai.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

</div>
@endsection