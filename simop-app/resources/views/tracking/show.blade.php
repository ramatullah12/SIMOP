@extends('layouts.app')

@section('title', 'Detail Tracking - SIMOP')
@section('page-title', 'Detail Tracking')

@section('content')
    @php
        $workEstimate = $project->workEstimate;
        $order = $workEstimate?->order;
        $survey = $workEstimate?->survey;

        $approval = $workEstimate?->approvals
            ?->where('stage', 'direktur')
            ->where('status', 'approved')
            ->first();

        $planning = $project->planning;
        $production = $planning?->production;
        $bapp = $production?->bapp;
        $invoice = $project->invoice;
        $rating = $project->rating;

        $orderDone = (bool) $order;

        $surveyDone = (bool) $survey && (bool) $workEstimate;

        $workEstimateDone = $workEstimate?->status === 'approved';

        $approvalDone = (bool) $approval
            || $workEstimate?->status === 'approved';

        $planningStarted = $planning
            && in_array(
                $planning->status,
                ['ready', 'in_progress', 'completed'],
                true
            );

        $planningDone = $planning
            && $planning->status === 'completed';

        $productionStarted = $production
            && in_array(
                $production->status,
                ['in_progress', 'completed'],
                true
            );

        $productionDone = $production
            && $production->status === 'completed';

        $bappSubmitted = $bapp
            && in_array(
                $bapp->status,
                ['submitted', 'approved'],
                true
            );

        $bappDone = $bapp
            && $bapp->status === 'approved';

        $projectDone = $project->status === 'completed';

        $invoiceIssued = $invoice
            && in_array(
                $invoice->status,
                ['issued', 'paid'],
                true
            );

        $invoicePaid = $invoice
            && $invoice->status === 'paid';

        $ratingDone = (bool) $rating;

        $statusLabel = match ($project->status) {
            'planning' => 'Planning',
            'ongoing' => 'Dalam Pengerjaan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($project->status),
        };

        $statusClass = match ($project->status) {
            'planning' => 'bg-amber-100 text-amber-700',
            'ongoing' => 'bg-blue-100 text-blue-700',
            'completed' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-red-100 text-red-700',
            default => 'bg-slate-100 text-slate-700',
        };

        $steps = [
            [
                'key' => 'order',
                'title' => 'Order / RFQ Diajukan',
                'description' => 'Order telah diajukan dan tercatat di dalam sistem.',
                'done' => $orderDone,
                'active' => false,
                'date' => $order?->created_at,
            ],
            [
                'key' => 'survey',
                'title' => 'Survey',
                'description' => $surveyDone
                    ? 'Survey kebutuhan Client telah diselesaikan.'
                    : 'Menunggu proses Survey kebutuhan Client.',
                'done' => $surveyDone,
                'active' => $orderDone && ! $surveyDone,
                'date' => $surveyDone ? $survey?->updated_at : null,
            ],
            [
                'key' => 'work-estimate',
                'title' => 'Work Estimate',
                'description' => $workEstimateDone
                    ? 'Work Estimate telah selesai dan disetujui.'
                    : 'Menunggu penyelesaian Work Estimate.',
                'done' => $workEstimateDone,
                'active' => $surveyDone && ! $workEstimateDone,
                'date' => $workEstimateDone
                    ? $workEstimate?->updated_at
                    : null,
            ],
            [
                'key' => 'approval',
                'title' => 'Approval Direktur',
                'description' => $approvalDone
                    ? 'Work Estimate telah mendapatkan persetujuan Direktur.'
                    : 'Menunggu persetujuan Direktur.',
                'done' => $approvalDone,
                'active' => $workEstimate && ! $approvalDone,
                'date' => $approval?->approved_at,
            ],
            [
                'key' => 'planning',
                'title' => 'Planning',
                'description' => $planningDone
                    ? 'Perencanaan pelaksanaan Project telah selesai.'
                    : ($planningStarted
                        ? 'Perencanaan pelaksanaan Project sedang berjalan.'
                        : ($planning
                            ? 'Planning telah dibuat dan menunggu proses selanjutnya.'
                            : 'Menunggu proses Planning.')),
                'done' => $planningDone,
                'active' => (bool) $planning && ! $planningDone,
                'date' => $planning?->updated_at,
            ],
            [
                'key' => 'production',
                'title' => 'Produksi & Instalasi',
                'description' => $productionDone
                    ? 'Produksi dan instalasi telah selesai.'
                    : ($productionStarted
                        ? 'Produksi dan instalasi sedang dikerjakan.'
                        : ($production
                            ? 'Produksi dan instalasi telah disiapkan.'
                            : 'Menunggu proses produksi dan instalasi.')),
                'done' => $productionDone,
                'active' => (bool) $production && ! $productionDone,
                'date' => $production?->updated_at,
            ],
            [
                'key' => 'bapp',
                'title' => 'BAPP',
                'description' => $bappDone
                    ? 'BAPP telah disetujui.'
                    : ($bappSubmitted
                        ? 'BAPP telah diajukan dan menunggu persetujuan.'
                        : ($bapp
                            ? 'BAPP sedang dipersiapkan.'
                            : 'Menunggu penyelesaian BAPP.')),
                'done' => $bappDone,
                'active' => (bool) $bapp && ! $bappDone,
                'date' => $bapp?->updated_at,
            ],
            [
                'key' => 'project',
                'title' => 'Project Selesai',
                'description' => $projectDone
                    ? 'Project telah dinyatakan selesai setelah BAPP disetujui.'
                    : 'Project akan dinyatakan selesai setelah BAPP disetujui.',
                'done' => $projectDone,
                'active' => false,
                'date' => $projectDone
                    ? $project->updated_at
                    : null,
            ],
            [
                'key' => 'invoice',
                'title' => 'Invoice',
                'description' => $invoicePaid
                    ? 'Invoice telah dibayar dan berstatus lunas.'
                    : ($invoiceIssued
                        ? 'Invoice telah diterbitkan dan menunggu pembayaran.'
                        : 'Invoice belum diterbitkan.'),
                'done' => $invoicePaid,
                'active' => $invoiceIssued && ! $invoicePaid,
                'date' => $invoicePaid
                    ? $invoice?->paid_at
                    : $invoice?->issued_at,
            ],
            [
                'key' => 'rating',
                'title' => 'Rating & Ulasan',
                'description' => $ratingDone
                    ? 'Rating dan ulasan telah diberikan.'
                    : ($invoicePaid
                        ? 'Anda sudah dapat memberikan Rating dan ulasan.'
                        : 'Rating tersedia setelah Invoice lunas.'),
                'done' => $ratingDone,
                'active' => $invoicePaid && ! $ratingDone,
                'date' => $rating?->created_at,
            ],
        ];
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a
                    href="{{ route('client.tracking.index') }}"
                    class="text-sm font-medium text-blue-600 hover:text-blue-700"
                >
                    ← Kembali ke Tracking
                </a>

                <h1 class="mt-3 text-2xl font-bold text-slate-900">
                    {{ $project->title }}
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $project->project_number }}
                </p>
            </div>

            <span class="inline-flex w-fit rounded-full px-4 py-2 text-sm font-semibold {{ $statusClass }}">
                {{ $statusLabel }}
            </span>
        </div>

        @if ($project->status === 'cancelled')
            <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                <h2 class="font-semibold text-red-800">
                    Project Dibatalkan
                </h2>

                <p class="mt-1 text-sm leading-6 text-red-700">
                    Project ini telah dibatalkan. Proses operasional tidak dilanjutkan.
                </p>
            </div>
        @elseif ($projectDone)
            <div class="rounded-xl border border-green-200 bg-green-50 p-5">
                <h2 class="font-semibold text-green-800">
                    Project Selesai
                </h2>

                <p class="mt-1 text-sm leading-6 text-green-700">
                    Pekerjaan telah selesai dan BAPP telah disetujui.
                </p>
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Order / RFQ
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $order?->order_number ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Event
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $order?->event_name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Lokasi
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $order?->event_location ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Tanggal Event
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $order?->event_date?->format('d/m/Y') ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        Progress Project
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Perkembangan pekerjaan Project saat ini.
                    </p>
                </div>

                <p class="text-2xl font-bold text-blue-600">
                    {{ $project->progress }}%
                </p>
            </div>

            <div class="mt-5 h-3 overflow-hidden rounded-full bg-slate-200">
                <div
                    class="h-full rounded-full bg-blue-600 transition-all"
                    style="width: {{ min(100, max(0, $project->progress)) }}%"
                ></div>
            </div>

            @if ($projectDone)
                <p class="mt-3 text-sm font-medium text-green-600">
                    Project telah mencapai 100% dan dinyatakan selesai.
                </p>
            @endif
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <h2 class="text-lg font-bold text-slate-900">
                    Status Pengerjaan
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Pantau setiap tahapan Order dan Project Anda.
                </p>
            </div>

            <div class="mt-8">
                @foreach ($steps as $step)
                    @php
                        $done = $step['done'] ?? false;
                        $active = $step['active'] ?? false;
                        $isLast = $loop->last;
                    @endphp

                    <div class="relative flex gap-4">
                        <div class="flex flex-col items-center">
                            @if ($done)
                                <div class="z-10 flex h-10 w-10 items-center justify-center rounded-full bg-green-500 font-bold text-white">
                                    ✓
                                </div>
                            @elseif ($active)
                                <div class="z-10 flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white ring-4 ring-blue-100">
                                    ●
                                </div>
                            @else
                                <div class="z-10 flex h-10 w-10 items-center justify-center rounded-full border-2 border-slate-300 bg-white text-sm font-bold text-slate-400">
                                    ○
                                </div>
                            @endif

                            @unless ($isLast)
                                <div class="min-h-16 w-0.5 flex-1 {{ $done ? 'bg-green-400' : 'bg-slate-200' }}"></div>
                            @endunless
                        </div>

                        <div class="{{ $isLast ? 'pb-0' : 'pb-8' }} min-w-0 flex-1">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <h3 class="font-semibold {{ $done ? 'text-green-700' : ($active ? 'text-blue-700' : 'text-slate-500') }}">
                                        {{ $step['title'] }}
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-slate-500">
                                        {{ $step['description'] }}
                                    </p>
                                </div>

                                @if ($step['date'])
                                    <p class="shrink-0 text-xs text-slate-400">
                                        {{ $step['date']->format('d/m/Y H:i') }}
                                    </p>
                                @endif
                            </div>

                            @if ($step['key'] === 'production' && $production)
                                <div class="mt-4 max-w-xl">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-medium text-slate-500">
                                            Progress Produksi
                                        </span>

                                        <span class="font-semibold text-slate-700">
                                            {{ $production->progress }}%
                                        </span>
                                    </div>

                                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-200">
                                        <div
                                            class="h-full rounded-full bg-blue-600"
                                            style="width: {{ min(100, max(0, $production->progress)) }}%"
                                        ></div>
                                    </div>
                                </div>
                            @endif

                            @if ($step['key'] === 'invoice' && $invoice)
                                <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:inline-block sm:min-w-72">
                                    <p class="text-xs font-medium text-slate-500">
                                        Nomor Invoice
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-slate-800">
                                        {{ $invoice->invoice_number }}
                                    </p>

                                    <p class="mt-3 text-xs font-medium text-slate-500">
                                        Total
                                    </p>

                                    <p class="mt-1 text-lg font-bold text-slate-900">
                                        Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                    </p>

                                    <a
                                        href="{{ route('invoices.show', $invoice) }}"
                                        class="mt-3 inline-flex text-sm font-semibold text-blue-600 hover:text-blue-700"
                                    >
                                        Lihat Invoice →
                                    </a>
                                </div>
                            @endif

                            @if ($step['key'] === 'rating' && $invoicePaid && ! $ratingDone)
                                <div class="mt-4">
                                    <a
                                        href="{{ route('client.ratings.create', $project) }}"
                                        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                                    >
                                        Beri Rating
                                    </a>
                                </div>
                            @endif

                            @if ($step['key'] === 'rating' && $ratingDone)
                                <div class="mt-4">
                                    <div class="flex flex-wrap items-center gap-1">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span class="text-xl {{ $i <= $rating->rating ? 'text-amber-400' : 'text-slate-300' }}">
                                                ★
                                            </span>
                                        @endfor

                                        <span class="ml-2 text-sm font-semibold text-slate-700">
                                            {{ $rating->rating }}/5
                                        </span>
                                    </div>

                                    <a
                                        href="{{ route('ratings.show', $rating) }}"
                                        class="mt-2 inline-flex text-sm font-semibold text-blue-600 hover:text-blue-700"
                                    >
                                        Lihat Ulasan →
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection