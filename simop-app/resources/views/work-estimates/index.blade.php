
@extends('layouts.app')

@section('title', 'Work Estimate | SIMOP')
@section('page-title', 'Work Estimate')

@section('content')
<div class="space-y-6">

    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Daftar Estimasi Biaya</h2>
            <p class="mt-1 text-sm text-slate-500">
                Kelola estimasi biaya pekerjaan berdasarkan Order dan Survey.
            </p>
        </div>

        <a href="{{ route('admin.work-estimates.create') }}"
           class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">
            + Buat Estimasi
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">No.</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">Nomor Estimasi</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">Judul</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">Order</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">Total Biaya</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">Status</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($estimates as $estimate)
                        <tr class="hover:bg-slate-50">
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                {{ $estimates->firstItem() + $loop->index }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-slate-900">
                                {{ $estimate->estimate_number }}
                            </td>

                            <td class="px-5 py-4 text-sm text-slate-700">
                                {{ $estimate->title }}
                            </td>

                            <td class="px-5 py-4 text-sm text-slate-600">
                                {{ $estimate->order->order_number ?? 'Order #'.$estimate->order_id }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-slate-900">
                                Rp{{ number_format((float) $estimate->total_amount, 0, ',', '.') }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm">
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-slate-100 text-slate-700',
                                        'menunggu_approval' => 'bg-amber-100 text-amber-700',
                                        'disetujui' => 'bg-green-100 text-green-700',
                                        'ditolak' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp

                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusColors[$estimate->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ ucfirst(str_replace('_', ' ', $estimate->status)) }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm">
                                <a href="{{ route('admin.work-estimates.show', $estimate) }}"
                                   class="font-semibold text-blue-600 hover:text-blue-800">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <p class="font-semibold text-slate-700">Belum ada estimasi biaya</p>
                                <p class="mt-1 text-sm text-slate-500">
                                    Klik Buat Estimasi untuk menambahkan estimasi pertama.
                                </p>
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
