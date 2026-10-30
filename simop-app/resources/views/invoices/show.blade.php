@extends('layouts.app')

@section('title', 'Detail Invoice - SIMOP')
@section('page-title', 'Detail Invoice')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;
        $project = $invoice->project;
        $order = $project?->workEstimate?->order;
        $rating = $project?->rating;

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

    <div class="mx-auto max-w-5xl space-y-6">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a
                    href="{{ route('invoices.index') }}"
                    class="text-sm font-medium text-blue-600 hover:text-blue-700"
                >
                    ← Kembali ke Invoice
                </a>

                <h1 class="mt-3 text-2xl font-bold text-slate-900">
                    {{ $invoice->invoice_number }}
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Detail Invoice Project
                </p>
            </div>

            <span class="inline-flex w-fit rounded-full px-4 py-2 text-sm font-semibold {{ $statusClass }}">
                {{ $statusLabel }}
            </span>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                <p class="font-semibold text-red-700">
                    Terjadi kesalahan.
                </p>

                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-600">
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
                        <h2 class="font-semibold text-green-800">
                            Invoice Sudah Lunas
                        </h2>

                        <p class="mt-1 text-sm text-green-700">
                            Pembayaran Invoice ini telah dikonfirmasi.
                        </p>

                        @if ($invoice->paid_at)
                            <p class="mt-2 text-sm text-green-700">
                                Dibayar pada {{ $invoice->paid_at->format('d/m/Y H:i') }}
                            </p>
                        @endif
                    </div>

                    @if ($role === 'client')
                        @if ($rating)
                            <a
                                href="{{ route('ratings.show', $rating) }}"
                                class="inline-flex items-center justify-center rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-green-700 ring-1 ring-inset ring-green-300 hover:bg-green-100"
                            >
                                Lihat Rating
                            </a>
                        @else
                            <a
                                href="{{ route('client.ratings.create', $project) }}"
                                class="inline-flex items-center justify-center rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-700"
                            >
                                Beri Rating
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        @elseif ($invoice->status === 'cancelled')
            <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                <h2 class="font-semibold text-red-800">
                    Invoice Dibatalkan
                </h2>

                <p class="mt-1 text-sm text-red-700">
                    Invoice ini telah dibatalkan dan tidak dapat diproses kembali.
                </p>
            </div>
        @elseif ($invoice->status === 'draft')
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                <h2 class="font-semibold text-amber-800">
                    Invoice Masih Draft
                </h2>

                <p class="mt-1 text-sm text-amber-700">
                    Invoice belum dapat dilihat oleh Client sampai Admin menerbitkannya.
                </p>
            </div>
        @elseif ($invoice->status === 'issued')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <h2 class="font-semibold text-blue-800">
                    Invoice Telah Diterbitkan
                </h2>

                <p class="mt-1 text-sm text-blue-700">
                    Client sudah dapat melihat Invoice ini.
                </p>

                @if ($invoice->issued_at)
                    <p class="mt-2 text-sm text-blue-700">
                        Diterbitkan pada {{ $invoice->issued_at->format('d/m/Y H:i') }}
                    </p>
                @endif
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-2">

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">
                    Informasi Invoice
                </h2>

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

                        <span class="text-right text-sm font-medium text-slate-900">
                            {{ $invoice->due_date?->format('d/m/Y') ?? '-' }}
                        </span>
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

                            <span class="text-right text-sm font-medium text-green-700">
                                {{ $invoice->paid_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    @endif

                    <div class="flex justify-between gap-4">
                        <span class="text-sm text-slate-500">
                            Dibuat Oleh
                        </span>

                        <span class="text-right text-sm font-medium text-slate-900">
                            {{ $invoice->creator?->name ?? '-' }}
                        </span>
                    </div>

                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">
                    Informasi Project
                </h2>

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

                        <span class="text-right text-sm font-medium text-slate-900">
                            {{ $project?->title ?? '-' }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                        <span class="text-sm text-slate-500">
                            Perusahaan
                        </span>

                        <span class="text-right text-sm font-medium text-slate-900">
                            {{ $order?->company_name ?? '-' }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                        <span class="text-sm text-slate-500">
                            Event
                        </span>

                        <span class="text-right text-sm font-medium text-slate-900">
                            {{ $order?->event_name ?? '-' }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-sm text-slate-500">
                            Status Project
                        </span>

                        @if ($project?->status === 'completed')
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                Selesai
                            </span>
                        @elseif ($project?->status === 'ongoing')
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                Dalam Pengerjaan
                            </span>
                        @elseif ($project?->status === 'planning')
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                Planning
                            </span>
                        @elseif ($project?->status === 'cancelled')
                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                Dibatalkan
                            </span>
                        @else
                            <span class="text-sm text-slate-500">
                                -
                            </span>
                        @endif
                    </div>

                </div>
            </div>

        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-lg font-bold text-slate-900">
                    Rincian Pembayaran
                </h2>
            </div>

            <div class="p-6">
                <div class="ml-auto max-w-md space-y-4">

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500">
                            Subtotal
                        </span>

                        <span class="font-medium text-slate-900">
                            Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500">
                            Pajak
                        </span>

                        <span class="font-medium text-slate-900">
                            Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="border-t border-slate-200 pt-4">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900">
                                Total
                            </span>

                            <span class="text-xl font-bold text-slate-900">
                                Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        @if ($invoice->notes)
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">
                    Catatan
                </h2>

                <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-600">
                    {{ $invoice->notes }}
                </p>
            </div>
        @endif

        @if ($role === 'admin' && $invoice->status === 'draft')
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">
                    Kelola Invoice
                </h2>

                <p class="mt-1 text-sm text-slate-500">
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
                <h2 class="text-lg font-bold text-slate-900">
                    Konfirmasi Pembayaran
                </h2>

                <p class="mt-1 text-sm text-slate-500">
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
                        <h2 class="font-bold text-green-700">
                            Pembayaran Selesai
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Invoice sudah lunas dan tidak dapat diubah kembali. Client sudah dapat memberikan Rating dan ulasan.
                        </p>
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
                        <h2 class="font-bold text-red-700">
                            Invoice Dibatalkan
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Invoice telah dibatalkan dan statusnya tidak dapat diubah kembali.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if ($role === 'client' && $invoice->status === 'issued')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <h2 class="font-semibold text-blue-800">
                    Menunggu Pembayaran
                </h2>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Invoice telah diterbitkan. Setelah pembayaran dikonfirmasi oleh Admin, status Invoice akan berubah menjadi Lunas dan Rating dapat diberikan.
                </p>
            </div>
        @endif

    </div>
@endsection