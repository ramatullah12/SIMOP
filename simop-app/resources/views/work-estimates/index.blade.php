@extends('layouts.app')

@section('title', 'Work Estimate | SIMOP')
@section('page-title', 'Work Estimate')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;

        $statusLabels = [
            'draft' => 'Draft',
            'pending' => 'Menunggu Approval',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];

        $statusColors = [
            'draft' => 'bg-slate-100 text-slate-700',
            'pending' => 'bg-amber-100 text-amber-700',
            'approved' => 'bg-green-100 text-green-700',
            'rejected' => 'bg-red-100 text-red-700',
        ];
    @endphp

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold">
                    Terdapat data yang perlu diperbaiki:
                </p>

                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">
                    Daftar Work Estimate
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    @if ($role === 'admin')
                        Kelola estimasi biaya pekerjaan berdasarkan hasil Survey yang telah selesai.
                    @elseif ($role === 'direktur')
                        Periksa Work Estimate dan pantau proses evaluasi serta approval.
                    @endif
                </p>
            </div>

            @if ($role === 'admin')
                <a
                    href="{{ route('admin.work-estimates.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                    + Buat Work Estimate
                </a>
            @endif
        </div>

        @if ($role === 'direktur')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                <p class="text-sm font-semibold text-blue-800">
                    Pemeriksaan Work Estimate
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Direktur dapat melihat rincian Work Estimate untuk proses evaluasi dan approval. Pembuatan Work Estimate dilakukan oleh Admin.
                </p>
            </div>
        @endif

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                No.
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Nomor WE
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Judul
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Order
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Survey
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Total
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Status
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($estimates as $estimate)
                            @php
                                $statusLabel = $statusLabels[$estimate->status]
                                    ?? ucfirst(str_replace('_', ' ', $estimate->status));

                                $statusClass = $statusColors[$estimate->status]
                                    ?? 'bg-slate-100 text-slate-700';
                            @endphp

                            <tr class="transition hover:bg-slate-50">
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                    {{ $estimates->firstItem() + $loop->index }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ $estimate->estimate_number }}
                                    </p>
                                </td>

                                <td class="px-5 py-4">
                                    <p class="text-sm font-medium text-slate-800">
                                        {{ $estimate->title }}
                                    </p>
                                </td>

                                <td class="px-5 py-4">
                                    <p class="text-sm font-medium text-slate-700">
                                        {{ $estimate->order?->order_number ?? 'Order #'.$estimate->order_id }}
                                    </p>

                                    @if ($estimate->order?->company_name)
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $estimate->order->company_name }}
                                        </p>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    @if ($estimate->survey)
                                        <p class="text-sm font-medium text-slate-700">
                                            {{ $estimate->survey->location }}
                                        </p>

                                        <p class="mt-1 text-xs text-green-600">
                                            Survey selesai
                                        </p>
                                    @else
                                        <span class="text-sm text-slate-400">
                                            -
                                        </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <p class="text-sm font-bold text-slate-900">
                                        Rp{{ number_format((float) $estimate->total_amount, 0, ',', '.') }}
                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <a
                                        href="{{ route('admin.work-estimates.show', $estimate) }}"
                                        class="text-sm font-semibold text-blue-600 hover:text-blue-800"
                                    >
                                        @if ($role === 'direktur')
                                            Periksa
                                        @else
                                            Detail
                                        @endif
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-14 text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-500">
                                        ≡
                                    </div>

                                    <p class="mt-4 font-semibold text-slate-700">
                                        Belum Ada Work Estimate
                                    </p>

                                    <p class="mx-auto mt-1 max-w-md text-sm leading-6 text-slate-500">
                                        @if ($role === 'admin')
                                            Work Estimate dapat dibuat setelah Survey selesai dan belum memiliki Work Estimate.
                                        @elseif ($role === 'direktur')
                                            Belum ada Work Estimate yang tersedia untuk diperiksa.
                                        @endif
                                    </p>

                                    @if ($role === 'admin')
                                        <a
                                            href="{{ route('admin.work-estimates.create') }}"
                                            class="mt-5 inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                                        >
                                            + Buat Work Estimate
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($estimates->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $estimates->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection