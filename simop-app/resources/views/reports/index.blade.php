@extends('layouts.app')

@section('title', 'Laporan - SIMOP')
@section('page-title', 'Laporan')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Laporan
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Ringkasan data operasional dan keuangan SIMOP berdasarkan periode.
                </p>
            </div>

            <div class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600">
                {{ $startDate->format('d/m/Y') }}
                -
                {{ $endDate->format('d/m/Y') }}
            </div>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                <ul class="list-disc space-y-1 pl-5 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <form
                method="GET"
                action="{{ request()->user()?->role?->slug === 'direktur'
                    ? route('direktur.reports.index')
                    : route('admin.reports.index') }}"
                class="grid gap-4 md:grid-cols-2 lg:grid-cols-[1fr_1fr_auto]"
            >
                <div>
                    <label
                        for="start_date"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Tanggal Mulai
                    </label>

                    <input
                        id="start_date"
                        type="date"
                        name="start_date"
                        value="{{ $startDate->format('Y-m-d') }}"
                        class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label
                        for="end_date"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Tanggal Akhir
                    </label>

                    <input
                        id="end_date"
                        type="date"
                        name="end_date"
                        value="{{ $endDate->format('Y-m-d') }}"
                        class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>

                <div class="flex items-end gap-2">
                    <button
                        type="submit"
                        class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800"
                    >
                        Tampilkan
                    </button>

                    <a
                        href="{{ request()->user()?->role?->slug === 'direktur'
                            ? route('direktur.reports.index')
                            : route('admin.reports.index') }}"
                        class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                    >
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">
                    Total Order
                </p>

                <p class="mt-3 text-3xl font-bold text-slate-900">
                    {{ number_format($totalOrders, 0, ',', '.') }}
                </p>

                <p class="mt-2 text-xs text-slate-400">
                    Order pada periode laporan
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">
                    Total Project
                </p>

                <p class="mt-3 text-3xl font-bold text-slate-900">
                    {{ number_format($totalProjects, 0, ',', '.') }}
                </p>

                <p class="mt-2 text-xs text-slate-400">
                    Project dibuat pada periode
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">
                    Project Selesai
                </p>

                <p class="mt-3 text-3xl font-bold text-emerald-600">
                    {{ number_format($completedProjects, 0, ',', '.') }}
                </p>

                <p class="mt-2 text-xs text-slate-400">
                    Project berstatus selesai
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">
                    Rating Rata-rata
                </p>

                <div class="mt-3 flex items-end gap-2">
                    <p class="text-3xl font-bold text-amber-500">
                        {{ number_format($averageRating, 1, ',', '.') }}
                    </p>

                    <p class="pb-1 text-sm font-semibold text-slate-400">
                        / 5
                    </p>
                </div>

                <p class="mt-2 text-xs text-slate-400">
                    Penilaian Client
                </p>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">
                    Total Nilai Invoice
                </p>

                <p class="mt-3 text-2xl font-bold text-slate-900">
                    Rp {{ number_format($totalInvoiceValue, 0, ',', '.') }}
                </p>

                <p class="mt-2 text-xs text-slate-400">
                    Invoice terbit dan sudah dibayar
                </p>
            </div>

            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                <p class="text-sm font-medium text-emerald-700">
                    Pembayaran Masuk
                </p>

                <p class="mt-3 text-2xl font-bold text-emerald-700">
                    Rp {{ number_format($paidInvoiceValue, 0, ',', '.') }}
                </p>

                <p class="mt-2 text-xs text-emerald-600">
                    Invoice berstatus dibayar
                </p>
            </div>

            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <p class="text-sm font-medium text-amber-700">
                    Piutang Berjalan
                </p>

                <p class="mt-3 text-2xl font-bold text-amber-700">
                    Rp {{ number_format($outstandingInvoiceValue, 0, ',', '.') }}
                </p>

                <p class="mt-2 text-xs text-amber-600">
                    Invoice terbit yang belum dibayar
                </p>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <h2 class="text-lg font-bold text-slate-900">
                    Project
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Project terbaru pada periode laporan.
                </p>
            </div>

            @if ($recentProjects->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Project
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Order
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Status
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Progress
                                </th>

                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach ($recentProjects as $project)
                                @php
                                    $projectStatus = match ($project->status) {
                                        'planning' => 'Planning',
                                        'ongoing' => 'Berjalan',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan',
                                        default => ucfirst($project->status),
                                    };

                                    $projectStatusClass = match ($project->status) {
                                        'planning' => 'bg-blue-100 text-blue-700',
                                        'ongoing' => 'bg-amber-100 text-amber-700',
                                        'completed' => 'bg-emerald-100 text-emerald-700',
                                        'cancelled' => 'bg-red-100 text-red-700',
                                        default => 'bg-slate-100 text-slate-700',
                                    };
                                @endphp

                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-slate-900">
                                            {{ $project->title }}
                                        </p>

                                        <p class="mt-1 text-xs font-medium text-blue-600">
                                            {{ $project->project_number }}
                                        </p>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($project->workEstimate?->order)
                                            <p class="text-sm font-medium text-slate-700">
                                                {{ $project->workEstimate->order->event_name ?? '-' }}
                                            </p>

                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ $project->workEstimate->order->order_number ?? '-' }}
                                            </p>

                                            @if ($project->workEstimate->order->company_name)
                                                <p class="mt-1 text-xs text-slate-400">
                                                    {{ $project->workEstimate->order->company_name }}
                                                </p>
                                            @endif
                                        @else
                                            <span class="text-sm text-slate-400">
                                                -
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $projectStatusClass }}">
                                            {{ $projectStatus }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="w-36">
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="font-semibold text-slate-700">
                                                    {{ $project->progress }}%
                                                </span>
                                            </div>

                                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                                                <div
                                                    class="h-full rounded-full bg-blue-600"
                                                    style="width: {{ min(100, max(0, $project->progress)) }}%"
                                                ></div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <a
                                            href="{{ route('projects.show', $project) }}"
                                            class="text-sm font-semibold text-blue-600 hover:text-blue-700"
                                        >
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <p class="font-semibold text-slate-700">
                        Belum ada Project
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Tidak ada Project pada periode yang dipilih.
                    </p>
                </div>
            @endif
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <h2 class="text-lg font-bold text-slate-900">
                    Invoice
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Invoice terbaru pada periode laporan.
                </p>
            </div>

            @if ($recentInvoices->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Invoice
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Project
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Tanggal
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Nilai
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Status
                                </th>

                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach ($recentInvoices as $invoice)
                                @php
                                    $invoiceStatus = match ($invoice->status) {
                                        'draft' => 'Draft',
                                        'issued' => 'Terbit',
                                        'paid' => 'Dibayar',
                                        'cancelled' => 'Dibatalkan',
                                        default => ucfirst($invoice->status),
                                    };

                                    $invoiceStatusClass = match ($invoice->status) {
                                        'draft' => 'bg-slate-100 text-slate-700',
                                        'issued' => 'bg-amber-100 text-amber-700',
                                        'paid' => 'bg-emerald-100 text-emerald-700',
                                        'cancelled' => 'bg-red-100 text-red-700',
                                        default => 'bg-slate-100 text-slate-700',
                                    };
                                @endphp

                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-slate-900">
                                            {{ $invoice->invoice_number }}
                                        </p>
                                    </td>

                                    <td class="px-6 py-4">
                                        <p class="text-sm font-medium text-slate-700">
                                            {{ $invoice->project?->title ?? '-' }}
                                        </p>

                                        @if ($invoice->project)
                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ $invoice->project->project_number }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                        {{ $invoice->invoice_date?->format('d/m/Y') ?? '-' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-900">
                                        Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $invoiceStatusClass }}">
                                            {{ $invoiceStatus }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <a
                                            href="{{ route('invoices.show', $invoice) }}"
                                            class="text-sm font-semibold text-blue-600 hover:text-blue-700"
                                        >
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <p class="font-semibold text-slate-700">
                        Belum ada Invoice
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Tidak ada Invoice pada periode yang dipilih.
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection