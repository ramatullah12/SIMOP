@extends('layouts.app')

@section('title', 'Project | SIMOP')
@section('page-title', 'Project')

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
                Daftar Project
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Kelola project yang berasal dari Work Estimate yang telah disetujui.
            </p>
        </div>

        @if (auth()->user()?->role?->slug === 'admin')
            <a href="{{ route('projects.create') }}"
               class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                Buat Project
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
                            No. Project
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Project
                        </th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Work Estimate
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
                    @forelse ($projects as $project)
                        <tr class="hover:bg-slate-50">
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                {{ $projects->firstItem() + $loop->index }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-slate-900">
                                {{ $project->project_number }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="font-medium text-slate-900">
                                    {{ $project->title }}
                                </div>

                                @if ($project->deadline)
                                    <div class="mt-1 text-xs text-slate-500">
                                        Deadline: {{ $project->deadline->format('d/m/Y') }}
                                    </div>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                {{ $project->workEstimate->estimate_number ?? '-' }}
                            </td>

                            <td class="px-5 py-4 text-sm text-slate-600">
                                {{ $project->assignedUser->name ?? 'Belum ditentukan' }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                @if ($project->status === 'planning')
                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                        Planning
                                    </span>
                                @elseif ($project->status === 'ongoing')
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                        Dalam Pengerjaan
                                    </span>
                                @elseif ($project->status === 'completed')
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Selesai
                                    </span>
                                @elseif ($project->status === 'cancelled')
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                        Dibatalkan
                                    </span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                @endif
                            </td>

                            <td class="min-w-40 px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-200">
                                        <div class="h-full rounded-full bg-blue-600"
                                             style="width: {{ $project->progress }}%">
                                        </div>
                                    </div>

                                    <span class="text-xs font-semibold text-slate-700">
                                        {{ $project->progress }}%
                                    </span>
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <a href="{{ route('projects.show', $project) }}"
                                   class="text-sm font-semibold text-blue-600 hover:text-blue-800">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <p class="font-medium text-slate-700">
                                    Belum ada Project.
                                </p>

                                @if (auth()->user()?->role?->slug === 'admin')
                                    <p class="mt-1 text-sm text-slate-500">
                                        Project dapat dibuat dari Work Estimate yang telah mendapatkan persetujuan final.
                                    </p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($projects->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</div>
@endsection