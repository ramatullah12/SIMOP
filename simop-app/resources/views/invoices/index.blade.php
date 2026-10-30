@extends('layouts.app')

@section('title', 'Invoice - SIMOP')
@section('page-title', 'Invoice')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Daftar Invoice
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    @if ($role === 'admin')
                        Kelola Invoice Project yang telah menyelesaikan proses BAPP.
                    @else
                        Lihat Invoice yang telah diterbitkan untuk Project Anda.
                    @endif
                </p>
            </div>

            @if ($role === 'admin')
                <a href="{{ route('invoices.create') }}"
                   class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                    + Buat Invoice
                </a>
            @endif
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Invoice
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Project
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Tanggal
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Total
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Status
                            </th>

                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($invoices as $invoice)
                            @php
                                $statusClass = match ($invoice->status) {
                                    'draft' => 'bg-slate-100 text-slate-700',
                                    'issued' => 'bg-blue-100 text-blue-700',
                                    'paid' => 'bg-green-100 text-green-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default => 'bg-slate-100 text-slate-700',
                                };

                                $statusLabel = match ($invoice->status) {
                                    'draft' => 'Draft',
                                    'issued' => 'Diterbitkan',
                                    'paid' => 'Lunas',
                                    'cancelled' => 'Dibatalkan',
                                    default => ucfirst($invoice->status),
                                };
                            @endphp

                            <tr class="transition hover:bg-slate-50">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <p class="font-semibold text-slate-900">
                                        {{ $invoice->invoice_number }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $invoice->project?->workEstimate?->order?->company_name ?? '-' }}
                                    </p>
                                </td>

                                <td class="px-6 py-4">
                                    <p class="font-medium text-slate-800">
                                        {{ $invoice->project?->title ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $invoice->project?->project_number ?? '-' }}
                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                    {{ $invoice->invoice_date?->format('d/m/Y') ?? '-' }}

                                    @if ($invoice->due_date)
                                        <p class="mt-1 text-xs text-slate-400">
                                            Jatuh tempo:
                                            {{ $invoice->due_date->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <p class="font-semibold text-slate-900">
                                        Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <a href="{{ route('invoices.show', $invoice) }}"
                                       class="inline-flex rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"
                                    class="px-6 py-12 text-center">

                                    <div class="mx-auto max-w-md">
                                        <p class="font-semibold text-slate-700">
                                            Belum ada Invoice
                                        </p>

                                        <p class="mt-2 text-sm text-slate-500">
                                            @if ($role === 'admin')
                                                Invoice dapat dibuat setelah BAPP Project disetujui.
                                            @else
                                                Belum ada Invoice yang diterbitkan untuk Anda.
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($invoices->hasPages())
                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection