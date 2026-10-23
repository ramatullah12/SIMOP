@extends('layouts.app')

@section('title', 'BAPP | SIMOP')
@section('page-title', 'BAPP')

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
                Berita Acara Penyelesaian Pekerjaan
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Kelola BAPP dari pekerjaan Produksi & Instalasi yang telah selesai.
            </p>
        </div>

        @if (auth()->user()?->role?->slug === 'admin')
            <a href="{{ route('bapps.create') }}"
               class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                Buat BAPP
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
                            Nomor BAPP
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Pekerjaan
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Project
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Tanggal Selesai
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
                    @forelse ($bapps as $bapp)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4 text-sm text-slate-600">
                                {{ $bapps->firstItem() + $loop->index }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-slate-900">
                                {{ $bapp->bapp_number }}
                            </td>

                            <td class="px-5 py-4">
                                <p class="text-sm font-medium text-slate-900">
                                    {{ $bapp->production->title ?? '-' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $bapp->production->production_number ?? '-' }}
                                </p>
                            </td>

                            <td class="px-5 py-4">
                                <p class="text-sm font-medium text-slate-800">
                                    {{ $bapp->production->planning->project->title ?? '-' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $bapp->production->planning->project->project_number ?? '-' }}
                                </p>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                {{ $bapp->completion_date?->format('d/m/Y') ?? '-' }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                @if ($bapp->status === 'draft')
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                        Draft
                                    </span>
                                @elseif ($bapp->status === 'submitted')
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                        Diajukan
                                    </span>
                                @elseif ($bapp->status === 'approved')
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Disetujui
                                    </span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <a href="{{ route('bapps.show', $bapp) }}"
                                   class="text-sm font-semibold text-blue-600 hover:text-blue-800">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <p class="font-medium text-slate-700">
                                    Belum ada BAPP.
                                </p>

                                <p class="mt-1 text-sm text-slate-500">
                                    BAPP dapat dibuat setelah Produksi & Instalasi selesai 100%.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($bapps->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">
                {{ $bapps->links() }}
            </div>
        @endif
    </div>

</div>
@endsection