@extends('layouts.app')

@section('title', 'Detail Rating | SIMOP')
@section('page-title', 'Detail Rating')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;

        $project = $rating->project;
        $workEstimate = $project?->workEstimate;
        $order = $workEstimate?->order;
        $client = $rating->client;
        $invoice = $project?->invoice;

        $ratingLabel = match ($rating->rating) {
            1 => 'Sangat Kurang',
            2 => 'Kurang',
            3 => 'Cukup',
            4 => 'Baik',
            5 => 'Sangat Baik',
            default => '-',
        };

        $backRoute = $role === 'admin'
            ? route('admin.ratings.index')
            : route('client.ratings.index');
    @endphp

    <div class="mx-auto max-w-5xl space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div>
            <a
                href="{{ $backRoute }}"
                class="text-sm font-semibold text-blue-600 hover:text-blue-800"
            >
                &larr; Kembali ke Rating
            </a>

            <h2 class="mt-3 text-2xl font-bold text-slate-900">
                Detail Rating & Ulasan
            </h2>

            <p class="mt-1 text-sm leading-6 text-slate-500">
                Penilaian Client terhadap Project yang telah selesai.
            </p>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                            Project
                        </p>

                        <h3 class="mt-2 text-xl font-bold text-slate-900">
                            {{ $project?->title ?? '-' }}
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $project?->project_number ?? '-' }}
                        </p>
                    </div>

                    <div class="w-fit rounded-xl border border-amber-200 bg-amber-50 px-5 py-4">
                        <div class="flex items-center gap-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="text-2xl {{ $i <= $rating->rating ? 'text-amber-400' : 'text-slate-300' }}">
                                    ★
                                </span>
                            @endfor
                        </div>

                        <p class="mt-2 text-center text-sm font-bold text-amber-700">
                            {{ $rating->rating }}/5 · {{ $ratingLabel }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 p-6 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Penilaian
                    </p>

                    <p class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $ratingLabel }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Tanggal Rating
                    </p>

                    <p class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $rating->created_at?->format('d/m/Y H:i') ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Client
                    </p>

                    <p class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $client?->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Order
                    </p>

                    <p class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $order?->order_number ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Work Estimate
                    </p>

                    <p class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $workEstimate?->estimate_number ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Status Project
                    </p>

                    @if ($project?->status === 'completed')
                        <span class="mt-2 inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                            Selesai
                        </span>
                    @elseif ($project?->status === 'cancelled')
                        <span class="mt-2 inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                            Dibatalkan
                        </span>
                    @elseif ($project?->status === 'ongoing')
                        <span class="mt-2 inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                            Dalam Pengerjaan
                        </span>
                    @else
                        <span class="mt-2 inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                            {{ ucfirst($project?->status ?? '-') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">
                        Informasi Invoice
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Invoice yang menjadi dasar Project dapat diberikan Rating.
                    </p>
                </div>

                @if ($invoice?->status === 'paid')
                    <span class="inline-flex w-fit rounded-full bg-green-100 px-3 py-1.5 text-xs font-semibold text-green-700">
                        Invoice Lunas
                    </span>
                @endif
            </div>

            <div class="mt-5 grid grid-cols-1 gap-5 border-t border-slate-100 pt-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Nomor Invoice
                    </p>

                    <p class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $invoice?->invoice_number ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Tanggal Invoice
                    </p>

                    <p class="mt-2 text-sm font-semibold text-slate-900">
                        {{ $invoice?->invoice_date?->format('d/m/Y') ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Total Invoice
                    </p>

                    <p class="mt-2 text-sm font-bold text-slate-900">
                        Rp {{ number_format((float) ($invoice?->total_amount ?? 0), 0, ',', '.') }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Tanggal Lunas
                    </p>

                    <p class="mt-2 text-sm font-semibold text-green-700">
                        {{ $invoice?->paid_at?->format('d/m/Y H:i') ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">
                        Ulasan Client
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Ulasan yang diberikan setelah Project selesai dan Invoice lunas.
                    </p>
                </div>

                <div class="hidden rounded-lg bg-amber-50 px-4 py-2 sm:block">
                    <span class="font-bold text-amber-600">
                        ★ {{ $rating->rating }}/5
                    </span>
                </div>
            </div>

            <div class="mt-5 rounded-xl border border-slate-100 bg-slate-50 p-5">
                @if ($rating->review)
                    <p class="whitespace-pre-line text-sm leading-7 text-slate-700">
                        {{ $rating->review }}
                    </p>
                @else
                    <p class="text-sm italic text-slate-500">
                        Client tidak memberikan ulasan tertulis.
                    </p>
                @endif
            </div>
        </div>

        @if ($role === 'admin')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <h3 class="font-semibold text-blue-800">
                    Penilaian Client
                </h3>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Rating dan ulasan ini merupakan penilaian final dari Client setelah Project selesai dan pembayaran Invoice telah dikonfirmasi Lunas.
                </p>
            </div>
        @elseif ($role === 'client')
            <div class="rounded-xl border border-green-200 bg-green-50 p-5">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-100 font-bold text-green-700">
                        ✓
                    </div>

                    <div>
                        <h3 class="font-semibold text-green-800">
                            Rating Telah Dikirim
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-green-700">
                            Terima kasih. Rating dan ulasan Anda telah tersimpan sebagai penilaian final terhadap Project dan layanan yang diberikan.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="flex justify-end">
            <a
                href="{{ $backRoute }}"
                class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100"
            >
                Kembali ke Rating
            </a>
        </div>
    </div>
@endsection