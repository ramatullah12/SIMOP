@extends('layouts.app')

@section('title', 'Invoice | SIMOP')
@section('page-title', 'Invoice')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;

        $statusLabels = [
            'draft' => 'Draft',
            'issued' => 'Diterbitkan',
            'paid' => 'Lunas',
            'cancelled' => 'Dibatalkan',
        ];

        $statusColors = [
            'draft' => 'bg-slate-100 text-slate-700',
            'issued' => 'bg-blue-100 text-blue-700',
            'paid' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-red-100 text-red-700',
        ];
    @endphp

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">
                    Invoice
                </h2>

                @if ($role === 'admin')
                    <p class="mt-1 text-sm text-slate-500">
                        Kelola Invoice Project yang telah menyelesaikan proses BAPP.
                    </p>
                @else
                    <p class="mt-1 text-sm text-slate-500">
                        Lihat Invoice yang telah diterbitkan untuk Project Anda.
                    </p>
                @endif
            </div>

            @if ($role === 'admin')
                <a
                    href="{{ route('invoices.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    + Buat Invoice
                </a>
            @endif
        </div>

        @if ($role === 'client')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <p class="text-sm font-semibold text-blue-800">
                    Invoice Project Anda
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Halaman ini hanya menampilkan Invoice yang telah diterbitkan oleh Admin untuk Project Anda.
                </p>
            </div>
        @endif

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                No.
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Invoice
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Project
                            </th>

                            @if ($role === 'admin')
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Client
                                </th>
                            @endif

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Tanggal
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Total
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Status
                            </th>

                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($invoices as $invoice)
                            @php
                                $project = $invoice->project;
                                $order = $project?->workEstimate?->order;

                                $statusLabel = $statusLabels[$invoice->status]
                                    ?? ucfirst($invoice->status);

                                $statusColor = $statusColors[$invoice->status]
                                    ?? 'bg-slate-100 text-slate-700';

                                $isOverdue = $invoice->status === 'issued'
                                    && $invoice->due_date
                                    && $invoice->due_date->isPast();
                            @endphp

                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                    {{ $invoices->firstItem() + $loop->index }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ $invoice->invoice_number }}
                                    </p>

                                    @if ($order?->order_number)
                                        <p class="mt-1 text-xs text-slate-500">
                                            Order: {{ $order->order_number }}
                                        </p>
                                    @endif
                                </td>

                                <td class="min-w-56 px-6 py-4">
                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $project?->title ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $project?->project_number ?? '-' }}
                                    </p>
                                </td>

                                @if ($role === 'admin')
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-medium text-slate-800">
                                            {{ $order?->client?->name ?? '-' }}
                                        </p>
                                    </td>
                                @endif

                                <td class="whitespace-nowrap px-6 py-4">
                                    <p class="text-sm font-medium text-slate-700">
                                        {{ $invoice->invoice_date?->format('d/m/Y') ?? '-' }}
                                    </p>

                                    @if ($invoice->due_date)
                                        <p class="mt-1 text-xs {{ $isOverdue ? 'font-semibold text-red-600' : 'text-slate-500' }}">
                                            Jatuh tempo:
                                            {{ $invoice->due_date->format('d/m/Y') }}
                                        </p>

                                        @if ($isOverdue)
                                            <p class="mt-1 text-xs font-semibold text-red-600">
                                                Melewati jatuh tempo
                                            </p>
                                        @endif
                                    @else
                                        <p class="mt-1 text-xs text-slate-400">
                                            Tanpa jatuh tempo
                                        </p>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <p class="text-sm font-bold text-slate-900">
                                        Rp {{ number_format((float) $invoice->total_amount, 0, ',', '.') }}
                                    </p>

                                    @if ($invoice->status === 'paid' && $invoice->paid_at)
                                        <p class="mt-1 text-xs text-green-600">
                                            Lunas {{ $invoice->paid_at->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusColor }}">
                                        {{ $statusLabel }}
                                    </span>

                                    @if ($invoice->status === 'issued' && $invoice->issued_at)
                                        <p class="mt-2 text-xs text-slate-400">
                                            {{ $invoice->issued_at->format('d/m/Y H:i') }}
                                        </p>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <a
                                        href="{{ route('invoices.show', $invoice) }}"
                                        class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100"
                                    >
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="{{ $role === 'admin' ? 8 : 7 }}"
                                    class="px-6 py-12 text-center"
                                >
                                    <div class="mx-auto max-w-md">
                                        <p class="font-semibold text-slate-700">
                                            Belum Ada Invoice
                                        </p>

                                        @if ($role === 'admin')
                                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                                Invoice dapat dibuat setelah Project selesai dan BAPP telah disetujui.
                                            </p>

                                            <a
                                                href="{{ route('invoices.create') }}"
                                                class="mt-4 inline-flex rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                                            >
                                                + Buat Invoice
                                            </a>
                                        @else
                                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                                Belum ada Invoice yang diterbitkan untuk Project Anda.
                                            </p>
                                        @endif
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

        @if ($role === 'admin')
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900">
                    Alur Status Invoice
                </h3>

                <div class="mt-4 flex flex-wrap items-center gap-2 text-sm">
                    <span class="rounded-full bg-slate-100 px-3 py-1.5 font-semibold text-slate-700">
                        Draft
                    </span>

                    <span class="text-slate-400">
                        →
                    </span>

                    <span class="rounded-full bg-blue-100 px-3 py-1.5 font-semibold text-blue-700">
                        Diterbitkan
                    </span>

                    <span class="text-slate-400">
                        →
                    </span>

                    <span class="rounded-full bg-green-100 px-3 py-1.5 font-semibold text-green-700">
                        Lunas
                    </span>
                </div>

                <p class="mt-3 text-xs leading-5 text-slate-500">
                    Invoice Draft atau Diterbitkan dapat dibatalkan. Invoice yang sudah Lunas atau Dibatalkan merupakan status final.
                </p>
            </div>
        @endif
    </div>
@endsection