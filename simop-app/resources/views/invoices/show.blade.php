@extends('layouts.app')

@section('title', 'Detail Invoice | SIMOP')
@section('page-title', 'Detail Invoice')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;

        $project = $invoice->project;
        $workEstimate = $project?->workEstimate;
        $order = $workEstimate?->order;
        $client = $order?->client;
        $production = $project?->planning?->production;
        $bapp = $production?->bapp;
        $rating = $project?->rating;

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

        $projectStatusLabels = [
            'planning' => 'Planning',
            'ongoing' => 'Dalam Pengerjaan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        $projectStatusColors = [
            'planning' => 'bg-slate-100 text-slate-700',
            'ongoing' => 'bg-blue-100 text-blue-700',
            'completed' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-red-100 text-red-700',
        ];

        $statusLabel = $statusLabels[$invoice->status]
            ?? ucfirst($invoice->status);

        $statusColor = $statusColors[$invoice->status]
            ?? 'bg-slate-100 text-slate-700';

        $projectStatusLabel = $projectStatusLabels[$project?->status]
            ?? '-';

        $projectStatusColor = $projectStatusColors[$project?->status]
            ?? 'bg-slate-100 text-slate-700';

        $isOverdue = $invoice->status === 'issued'
            && $invoice->due_date
            && $invoice->due_date->isPast();
    @endphp

    <div class="mx-auto max-w-6xl space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <a
                    href="{{ route('invoices.index') }}"
                    class="text-sm font-semibold text-blue-600 hover:text-blue-800"
                >
                    &larr; Kembali ke Invoice
                </a>

                <h2 class="mt-3 text-2xl font-bold text-slate-900">
                    {{ $invoice->invoice_number }}
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Detail Invoice Project
                </p>
            </div>

            <span class="inline-flex w-fit rounded-full px-4 py-2 text-sm font-semibold {{ $statusColor }}">
                {{ $statusLabel }}
            </span>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                <p class="font-semibold text-red-700">
                    Data belum dapat diproses.
                </p>

                <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($invoice->status === 'paid')
            <div class="rounded-xl border border-green-200 bg-green-50 p-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="font-semibold text-green-800">
                            Invoice Sudah Lunas
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-green-700">
                            Pembayaran Invoice ini telah dikonfirmasi.
                        </p>

                        @if ($invoice->paid_at)
                            <p class="mt-2 text-sm font-medium text-green-700">
                                Dibayar pada {{ $invoice->paid_at->format('d/m/Y H:i') }}
                            </p>
                        @endif
                    </div>

                    @if ($role === 'client')
                        @if ($rating)
                            <a
                                href="{{ route('ratings.show', $rating) }}"
                                class="inline-flex items-center justify-center rounded-lg border border-green-300 bg-white px-5 py-2.5 text-sm font-semibold text-green-700 hover:bg-green-100"
                            >
                                Lihat Rating
                            </a>
                        @else
                            <a
                                href="{{ route('client.ratings.create', $project) }}"
                                class="inline-flex items-center justify-center rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-700"
                            >
                                Beri Rating & Ulasan
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        @elseif ($invoice->status === 'cancelled')
            <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                <h3 class="font-semibold text-red-800">
                    Invoice Dibatalkan
                </h3>

                <p class="mt-1 text-sm leading-6 text-red-700">
                    Invoice ini telah dibatalkan dan tidak dapat diproses kembali.
                </p>
            </div>
        @elseif ($invoice->status === 'draft')
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                <h3 class="font-semibold text-amber-800">
                    Invoice Masih Draft
                </h3>

                <p class="mt-1 text-sm leading-6 text-amber-700">
                    Invoice belum dapat dilihat oleh Client sampai Admin menerbitkannya.
                </p>
            </div>
        @elseif ($invoice->status === 'issued')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h3 class="font-semibold text-blue-800">
                            Invoice Telah Diterbitkan
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-blue-700">
                            Invoice sudah dapat dilihat oleh Client dan menunggu konfirmasi pembayaran.
                        </p>

                        @if ($invoice->issued_at)
                            <p class="mt-2 text-sm font-medium text-blue-700">
                                Diterbitkan pada {{ $invoice->issued_at->format('d/m/Y H:i') }}
                            </p>
                        @endif
                    </div>

                    @if ($isOverdue)
                        <span class="inline-flex w-fit rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                            Melewati Jatuh Tempo
                        </span>
                    @endif
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900">
                    Informasi Invoice
                </h3>

                <div class="mt-5 space-y-4">
                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                        <span class="text-sm text-slate-500">
                            Nomor Invoice
                        </span>

                        <span class="text-right text-sm font-semibold text-slate-900">
                            {{ $invoice->invoice_number }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                        <span class="text-sm text-slate-500">
                            Status
                        </span>

                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                        <span class="text-sm text-slate-500">
                            Tanggal Invoice
                        </span>

                        <span class="text-right text-sm font-medium text-slate-900">
                            {{ $invoice->invoice_date?->format('d/m/Y') ?? '-' }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                        <span class="text-sm text-slate-500">
                            Jatuh Tempo
                        </span>

                        <div class="text-right">
                            <span class="text-sm font-medium {{ $isOverdue ? 'text-red-600' : 'text-slate-900' }}">
                                {{ $invoice->due_date?->format('d/m/Y') ?? '-' }}
                            </span>

                            @if ($isOverdue)
                                <p class="mt-1 text-xs font-semibold text-red-600">
                                    Melewati jatuh tempo
                                </p>
                            @endif
                        </div>
                    </div>

                    @if ($invoice->issued_at)
                        <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                            <span class="text-sm text-slate-500">
                                Diterbitkan
                            </span>

                            <span class="text-right text-sm font-medium text-slate-900">
                                {{ $invoice->issued_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    @endif

                    @if ($invoice->paid_at)
                        <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                            <span class="text-sm text-slate-500">
                                Dibayar
                            </span>

                            <span class="text-right text-sm font-semibold text-green-700">
                                {{ $invoice->paid_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    @endif

                    @if ($role === 'admin')
                        <div class="flex justify-between gap-4">
                            <span class="text-sm text-slate-500">
                                Dibuat Oleh
                            </span>

                            <span class="text-right text-sm font-medium text-slate-900">
                                {{ $invoice->creator?->name ?? '-' }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900">
                    Informasi Project
                </h3>

                <div class="mt-5 space-y-4">
                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                        <span class="text-sm text-slate-500">
                            Nomor Project
                        </span>

                        <span class="text-right text-sm font-semibold text-slate-900">
                            {{ $project?->project_number ?? '-' }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                        <span class="text-sm text-slate-500">
                            Nama Project
                        </span>

                        <span class="max-w-xs text-right text-sm font-medium text-slate-900">
                            {{ $project?->title ?? '-' }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                        <span class="text-sm text-slate-500">
                            Order
                        </span>

                        <span class="text-right text-sm font-medium text-slate-900">
                            {{ $order?->order_number ?? '-' }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                        <span class="text-sm text-slate-500">
                            Client
                        </span>

                        <span class="text-right text-sm font-medium text-slate-900">
                            {{ $client?->name ?? '-' }}
                        </span>
                    </div>

                    @if ($role === 'admin')
                        <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                            <span class="text-sm text-slate-500">
                                Work Estimate
                            </span>

                            <span class="text-right text-sm font-medium text-slate-900">
                                {{ $workEstimate?->estimate_number ?? '-' }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                            <span class="text-sm text-slate-500">
                                BAPP
                            </span>

                            <span class="text-right text-sm font-medium text-slate-900">
                                {{ $bapp?->bapp_number ?? '-' }}
                            </span>
                        </div>
                    @endif

                    <div class="flex justify-between gap-4">
                        <span class="text-sm text-slate-500">
                            Status Project
                        </span>

                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $projectStatusColor }}">
                            {{ $projectStatusLabel }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h3 class="text-lg font-bold text-slate-900">
                    Rincian Pembayaran
                </h3>
            </div>

            <div class="p-6">
                <div class="ml-auto max-w-md space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-slate-500">
                            Subtotal
                        </span>

                        <span class="font-medium text-slate-900">
                            Rp {{ number_format((float) $invoice->subtotal, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-slate-500">
                            Pajak
                        </span>

                        <span class="font-medium text-slate-900">
                            Rp {{ number_format((float) $invoice->tax_amount, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="border-t border-slate-200 pt-4">
                        <div class="flex items-center justify-between gap-4">
                            <span class="font-bold text-slate-900">
                                Total Invoice
                            </span>

                            <span class="text-xl font-bold text-slate-900">
                                Rp {{ number_format((float) $invoice->total_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($invoice->notes)
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900">
                    Catatan
                </h3>

                <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-600">
                    {{ $invoice->notes }}
                </p>
            </div>
        @endif

        @if ($role === 'admin' && $invoice->status === 'draft')
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900">
                    Kelola Invoice
                </h3>

                <p class="mt-1 text-sm leading-6 text-slate-500">
                    Invoice masih Draft. Terbitkan Invoice agar dapat dilihat oleh Client.
                </p>

                <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                    <form
                        action="{{ route('invoices.status', $invoice) }}"
                        method="POST"
                        onsubmit="return confirm('Terbitkan Invoice ini kepada Client?')"
                    >
                        @csrf
                        @method('PATCH')

                        <input
                            type="hidden"
                            name="status"
                            value="issued"
                        >

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 sm:w-auto"
                        >
                            Terbitkan Invoice
                        </button>
                    </form>

                    <form
                        action="{{ route('invoices.status', $invoice) }}"
                        method="POST"
                        onsubmit="return confirm('Yakin ingin membatalkan Invoice ini?')"
                    >
                        @csrf
                        @method('PATCH')

                        <input
                            type="hidden"
                            name="status"
                            value="cancelled"
                        >

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-lg border border-red-300 bg-white px-5 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 sm:w-auto"
                        >
                            Batalkan Invoice
                        </button>
                    </form>
                </div>
            </div>
        @elseif ($role === 'admin' && $invoice->status === 'issued')
            <div class="rounded-xl border border-blue-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900">
                    Konfirmasi Pembayaran
                </h3>

                <p class="mt-1 text-sm leading-6 text-slate-500">
                    Invoice telah diterbitkan. Tandai Lunas setelah pembayaran Client telah dikonfirmasi.
                </p>

                <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                    <form
                        action="{{ route('invoices.status', $invoice) }}"
                        method="POST"
                        onsubmit="return confirm('Konfirmasi bahwa Invoice ini sudah dibayar?')"
                    >
                        @csrf
                        @method('PATCH')

                        <input
                            type="hidden"
                            name="status"
                            value="paid"
                        >

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-700 sm:w-auto"
                        >
                            Tandai Lunas
                        </button>
                    </form>

                    <form
                        action="{{ route('invoices.status', $invoice) }}"
                        method="POST"
                        onsubmit="return confirm('Yakin ingin membatalkan Invoice ini?')"
                    >
                        @csrf
                        @method('PATCH')

                        <input
                            type="hidden"
                            name="status"
                            value="cancelled"
                        >

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-lg border border-red-300 bg-white px-5 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 sm:w-auto"
                        >
                            Batalkan Invoice
                        </button>
                    </form>
                </div>
            </div>
        @elseif ($role === 'admin' && $invoice->status === 'paid')
            <div class="rounded-xl border border-green-200 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-100 font-bold text-green-700">
                        ✓
                    </div>

                    <div>
                        <h3 class="font-bold text-green-700">
                            Pembayaran Selesai
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Invoice sudah lunas dan tidak dapat diubah kembali. Client dapat memberikan Rating dan ulasan untuk Project ini.
                        </p>

                        @if ($rating)
                            <a
                                href="{{ route('ratings.show', $rating) }}"
                                class="mt-3 inline-flex text-sm font-semibold text-green-700 hover:text-green-800"
                            >
                                Lihat Rating Client
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @elseif ($role === 'admin' && $invoice->status === 'cancelled')
            <div class="rounded-xl border border-red-200 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 font-bold text-red-700">
                        ×
                    </div>

                    <div>
                        <h3 class="font-bold text-red-700">
                            Invoice Dibatalkan
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Invoice telah dibatalkan dan statusnya tidak dapat diubah kembali.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if ($role === 'client' && $invoice->status === 'issued')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <h3 class="font-semibold text-blue-800">
                    Menunggu Konfirmasi Pembayaran
                </h3>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Setelah pembayaran dikonfirmasi oleh Admin, status Invoice akan berubah menjadi Lunas. Setelah itu Anda dapat memberikan Rating dan ulasan untuk Project.
                </p>
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900">
                Alur Invoice
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

                <span class="text-slate-400">
                    →
                </span>

                <span class="rounded-full bg-amber-100 px-3 py-1.5 font-semibold text-amber-700">
                    Rating & Ulasan
                </span>
            </div>

            <p class="mt-3 text-xs leading-5 text-slate-500">
                Invoice Draft atau Diterbitkan dapat dibatalkan. Invoice Lunas dan Dibatalkan merupakan status final.
            </p>
        </div>
    </div>
@endsection