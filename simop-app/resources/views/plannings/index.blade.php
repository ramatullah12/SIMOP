@extends('layouts.app')

@section('title', 'Planning | SIMOP')
@section('page-title', 'Planning')

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
                Daftar Planning
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Kelola perencanaan pekerjaan untuk setiap Project.
            </p>
        </div>

        @if (auth()->user()?->role?->slug === 'admin')
            <a href="{{ route('plannings.create') }}"
               class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                Buat Planning
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
                            No. Planning
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Planning
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Project
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Petugas
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Periode
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Status
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse ($plannings as $planning)
                        <tr class="hover:bg-slate-50">

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                {{ $plannings->firstItem() + $loop->index }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-slate-900">
                                {{ $planning->planning_number }}
                            </td>

                            <td class="px-5 py-4">
                                <p class="font-medium text-slate-900">
                                    {{ $planning->title }}
                                </p>
                            </td>

                            <td class="px-5 py-4">
                                <p class="text-sm font-medium text-slate-800">
                                    {{ $planning->project->title ?? '-' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $planning->project->project_number ?? '-' }}
                                </p>
                            </td>

                            <td class="px-5 py-4 text-sm text-slate-600">
                                {{ $planning->project->assignedUser->name ?? 'Belum ditentukan' }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                @if ($planning->planned_start_date || $planning->planned_end_date)
                                    <div>
                                        {{ $planning->planned_start_date?->format('d/m/Y') ?? '-' }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-400">
                                        s/d {{ $planning->planned_end_date?->format('d/m/Y') ?? '-' }}
                                    </div>
                                @else
                                    -
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                @if ($planning->status === 'draft')
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                        Draft
                                    </span>
                                @elseif ($planning->status === 'ready')
                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                        Siap
                                    </span>
                                @elseif ($planning->status === 'in_progress')
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                        Berjalan
                                    </span>
                                @elseif ($planning->status === 'completed')
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Selesai
                                    </span>
                                @elseif ($planning->status === 'cancelled')
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                        Dibatalkan
                                    </span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                        {{ ucfirst($planning->status) }}
                                    </span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <a href="{{ route('plannings.show', $planning) }}"
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
                                    Belum ada Planning.
                                </p>

                                @if (auth()->user()?->role?->slug === 'admin')
                                    <p class="mt-1 text-sm text-slate-500">
                                        Buat Planning dari Project yang tersedia.
                                    </p>
                                @endif

                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>

        @if ($plannings->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">
                {{ $plannings->links() }}
            </div>
        @endif
    </div>

</div>
@endsection