@extends('layouts.app')

@section('title', 'Rating - SIMOP')
@section('page-title', 'Rating')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;
    @endphp

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                Rating & Ulasan
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                @if ($role === 'admin')
                    Lihat penilaian dan ulasan yang diberikan Client.
                @else
                    Berikan penilaian untuk Project yang telah selesai dan Invoice-nya sudah lunas.
                @endif
            </p>
        </div>

        @if ($role === 'admin')
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Project
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Client
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Rating
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Ulasan
                                </th>

                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($ratings as $rating)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-slate-900">
                                            {{ $rating->project?->title ?? '-' }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $rating->project?->project_number ?? '-' }}
                                        </p>
                                    </td>

                                    <td class="px-6 py-4">
                                        <p class="text-sm font-medium text-slate-800">
                                            {{ $rating->client?->name ?? '-' }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $rating->project?->workEstimate?->order?->company_name ?? '-' }}
                                        </p>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-1">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <span class="{{ $i <= $rating->rating ? 'text-amber-400' : 'text-slate-300' }}">
                                                    ★
                                                </span>
                                            @endfor
                                        </div>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $rating->rating }}/5
                                        </p>
                                    </td>

                                    <td class="max-w-sm px-6 py-4 text-sm text-slate-600">
                                        {{ \Illuminate\Support\Str::limit($rating->review ?? 'Tidak ada ulasan.', 80) }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <a href="{{ route('ratings.show', $rating) }}"
                                           class="inline-flex rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"
                                        class="px-6 py-12 text-center text-sm text-slate-500">
                                        Belum ada Rating dari Client.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($ratings->hasPages())
                    <div class="border-t border-slate-200 px-6 py-4">
                        {{ $ratings->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="grid gap-5">
                @forelse ($projects as $project)
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-lg font-bold text-slate-900">
                                        {{ $project->title }}
                                    </h2>

                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Invoice Lunas
                                    </span>
                                </div>

                                <p class="mt-2 text-sm text-slate-500">
                                    {{ $project->project_number }}
                                </p>

                                <div class="mt-4 grid gap-2 text-sm text-slate-600 sm:grid-cols-2">
                                    <p>
                                        <span class="font-medium text-slate-700">
                                            Event:
                                        </span>

                                        {{ $project->workEstimate?->order?->event_name ?? '-' }}
                                    </p>

                                    <p>
                                        <span class="font-medium text-slate-700">
                                            Lokasi:
                                        </span>

                                        {{ $project->workEstimate?->order?->event_location ?? '-' }}
                                    </p>
                                </div>
                            </div>

                            <div class="shrink-0">
                                @if ($project->rating)
                                    <div class="rounded-xl bg-amber-50 px-5 py-4">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">
                                            Rating Anda
                                        </p>

                                        <div class="mt-2 flex items-center gap-1">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <span class="{{ $i <= $project->rating->rating ? 'text-amber-400' : 'text-slate-300' }}">
                                                    ★
                                                </span>
                                            @endfor
                                        </div>

                                        <a href="{{ route('ratings.show', $project->rating) }}"
                                           class="mt-3 inline-flex text-sm font-semibold text-blue-600 hover:text-blue-700">
                                            Lihat Ulasan
                                        </a>
                                    </div>
                                @else
                                    <a href="{{ route('client.ratings.create', $project) }}"
                                       class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                                        Beri Rating
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-slate-200 bg-white px-6 py-12 text-center shadow-sm">
                        <div class="mx-auto max-w-md">
                            <div class="text-4xl text-slate-300">
                                ☆
                            </div>

                            <h2 class="mt-4 font-bold text-slate-800">
                                Belum Ada Project yang Dapat Dinilai
                            </h2>

                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Rating dapat diberikan setelah Project selesai dan Invoice telah berstatus Lunas.
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($projects->hasPages())
                <div>
                    {{ $projects->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection