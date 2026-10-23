@extends('layouts.app')

@section('title', 'Produksi & Instalasi | SIMOP')
@section('page-title', 'Produksi & Instalasi')

@section('content')
<div class="space-y-6">

    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">
                Produksi & Instalasi
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Kelola pelaksanaan produksi dan instalasi berdasarkan Planning.
            </p>
        </div>

        @if (auth()->user()?->role?->slug === 'admin')
            <a href="{{ route('productions.create') }}"
               class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                Buat Produksi & Instalasi
            </a>
        @endif
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">

                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            No.
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            No. Produksi
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Pekerjaan
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Project
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Petugas
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Status
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Progress
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($productions as $production)
                        <tr class="hover:bg-slate-50">

                            <td class="px-5 py-4 text-sm text-slate-600">
                                {{ $productions->firstItem() + $loop->index }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-slate-900">
                                {{ $production->production_number }}
                            </td>

                            <td class="px-5 py-4">
                                <p class="font-medium text-slate-900">
                                    {{ $production->title }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $production->planning->planning_number ?? '-' }}
                                </p>
                            </td>

                            <td class="px-5 py-4">
                                <p class="text-sm font-medium text-slate-800">
                                    {{ $production->planning->project->title ?? '-' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $production->planning->project->project_number ?? '-' }}
                                </p>
                            </td>

                            <td class="px-5 py-4 text-sm text-slate-600">
                                {{ $production->assignedUser->name ?? 'Belum ditentukan' }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                @if ($production->status === 'pending')
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                        Menunggu
                                    </span>
                                @elseif ($production->status === 'in_progress')
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                        Berjalan
                                    </span>
                                @elseif ($production->status === 'completed')
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Selesai
                                    </span>
                                @elseif ($production->status === 'cancelled')
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                        Dibatalkan
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex min-w-32 items-center gap-3">
                                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-200">
                                        <div class="h-full rounded-full bg-blue-600"
                                             style="width: {{ $production->progress }}%">
                                        </div>
                                    </div>

                                    <span class="text-xs font-semibold text-slate-700">
                                        {{ $production->progress }}%
                                    </span>
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <a href="{{ route('productions.show', $production) }}"
                                   class="text-sm font-semibold text-blue-600 hover:text-blue-800">
                                    Detail
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8"
                                class="px-5 py-12 text-center">

                                <p class="font-medium text-slate-700">
                                    Belum ada Produksi & Instalasi.
                                </p>

                                <p class="mt-1 text-sm text-slate-500">
                                    Data akan tersedia setelah dibuat dari Planning.
                                </p>

                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        @if ($productions->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">
                {{ $productions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection