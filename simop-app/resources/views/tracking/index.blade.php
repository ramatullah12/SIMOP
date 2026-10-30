@extends('layouts.app')

@section('title', 'Tracking Project - SIMOP')
@section('page-title', 'Tracking')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                Tracking Project
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Pantau perkembangan Order dan Project Anda dari awal hingga selesai.
            </p>
        </div>

        @if ($projects->count())
            <div class="grid gap-5">
                @foreach ($projects as $project)
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

                        $surveyDone = $survey?->status === 'selesai';
                        $surveyCancelled = $survey?->status === 'dibatalkan';
                        $surveyActive = $survey
                            && in_array(
                                $survey->status,
                                ['dijadwalkan', 'berlangsung'],
                                true
                            );

                        $workEstimateDone = $workEstimate?->status === 'approved';

                        $approvalDone = (bool) $approval
                            || $workEstimate?->status === 'approved';

                        $planningDone = $planning
                            && $planning->status === 'completed';

                        $planningActive = $planning
                            && in_array(
                                $planning->status,
                                ['draft', 'ready', 'in_progress'],
                                true
                            );

                        $planningCancelled = $planning?->status === 'cancelled';

                        $productionDone = $production
                            && $production->status === 'completed';

                        $productionActive = $production
                            && in_array(
                                $production->status,
                                ['pending', 'in_progress'],
                                true
                            );

                        $productionCancelled = $production?->status === 'cancelled';

                        $bappStarted = (bool) $bapp;

                        $bappDone = $bapp
                            && $bapp->status === 'approved';

                        $bappActive = $bapp
                            && in_array(
                                $bapp->status,
                                ['draft', 'submitted'],
                                true
                            );

                        $projectDone = $project->status === 'completed';
                        $projectCancelled = $project->status === 'cancelled';

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

                        $stageClass = function (
                            $done,
                            $active = false,
                            $cancelled = false
                        ) {
                            if ($cancelled) {
                                return 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-200';
                            }

                            if ($done) {
                                return 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-200';
                            }

                            if ($active) {
                                return 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-200';
                            }

                            return 'bg-slate-100 text-slate-400';
                        };
                    @endphp

                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                        <div class="p-6">
                            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <h2 class="text-lg font-bold text-slate-900">
                                            {{ $project->title }}
                                        </h2>

                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>

                                    <p class="mt-2 text-sm font-medium text-blue-600">
                                        {{ $project->project_number }}
                                    </p>

                                    <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                        <div>
                                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                                Order / RFQ
                                            </p>

                                            <p class="mt-1 text-sm font-semibold text-slate-700">
                                                {{ $order?->order_number ?? '-' }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                                Lokasi
                                            </p>

                                            <p class="mt-1 text-sm font-semibold text-slate-700">
                                                {{ $order?->event_location ?? '-' }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                                Tanggal Mulai
                                            </p>

                                            <p class="mt-1 text-sm font-semibold text-slate-700">
                                                {{ $project->start_date?->format('d/m/Y') ?? '-' }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                                Tanggal Event
                                            </p>

                                            <p class="mt-1 text-sm font-semibold text-slate-700">
                                                {{ $order?->event_date?->format('d/m/Y') ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="shrink-0">
                                    <a
                                        href="{{ route('client.tracking.show', $project) }}"
                                        class="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 lg:w-auto"
                                    >
                                        Lihat Tracking
                                    </a>
                                </div>
                            </div>

                            <div class="mt-6 border-t border-slate-100 pt-5">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-700">
                                            Progress Project
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Perkembangan pekerjaan saat ini
                                        </p>
                                    </div>

                                    <p class="text-lg font-bold
                                        {{ $projectDone
                                            ? 'text-green-600'
                                            : ($projectCancelled
                                                ? 'text-red-600'
                                                : 'text-blue-600')
                                        }}"
                                    >
                                        {{ $project->progress }}%
                                    </p>
                                </div>

                                <div class="mt-3 h-2.5 overflow-hidden rounded-full bg-slate-200">
                                    <div
                                        class="h-full rounded-full transition-all
                                            {{ $projectDone
                                                ? 'bg-green-500'
                                                : ($projectCancelled
                                                    ? 'bg-red-500'
                                                    : 'bg-blue-600')
                                            }}"
                                        style="width: {{ min(100, max(0, $project->progress)) }}%"
                                    ></div>
                                </div>

                                @if ($projectDone)
                                    <p class="mt-2 text-xs font-medium text-green-600">
                                        Project telah selesai.
                                    </p>
                                @elseif ($projectCancelled)
                                    <p class="mt-2 text-xs font-medium text-red-600">
                                        Project telah dibatalkan.
                                    </p>
                                @endif
                            </div>

                            <div class="mt-5 border-t border-slate-100 pt-5">
                                <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Tahapan Project
                                </p>

                                <div class="flex flex-wrap gap-2">
                                    <span
                                        class="rounded-lg px-3 py-1.5 text-xs font-medium
                                            {{ $stageClass($orderDone) }}"
                                    >
                                        Order
                                    </span>

                                    <span
                                        class="rounded-lg px-3 py-1.5 text-xs font-medium
                                            {{ $stageClass(
                                                $surveyDone,
                                                $surveyActive,
                                                $surveyCancelled
                                            ) }}"
                                    >
                                        Survey
                                    </span>

                                    <span
                                        class="rounded-lg px-3 py-1.5 text-xs font-medium
                                            {{ $stageClass(
                                                $workEstimateDone,
                                                $surveyDone && ! $workEstimateDone
                                            ) }}"
                                    >
                                        Work Estimate
                                    </span>

                                    <span
                                        class="rounded-lg px-3 py-1.5 text-xs font-medium
                                            {{ $stageClass(
                                                $approvalDone,
                                                $workEstimate
                                                    && ! $approvalDone
                                                    && ! $surveyCancelled
                                            ) }}"
                                    >
                                        Approval
                                    </span>

                                    <span
                                        class="rounded-lg px-3 py-1.5 text-xs font-medium
                                            {{ $stageClass(
                                                $planningDone,
                                                $planningActive,
                                                $planningCancelled
                                            ) }}"
                                    >
                                        Planning
                                    </span>

                                    <span
                                        class="rounded-lg px-3 py-1.5 text-xs font-medium
                                            {{ $stageClass(
                                                $productionDone,
                                                $productionActive,
                                                $productionCancelled
                                            ) }}"
                                    >
                                        Produksi
                                    </span>

                                    <span
                                        class="rounded-lg px-3 py-1.5 text-xs font-medium
                                            {{ $stageClass(
                                                $bappDone,
                                                $bappActive
                                            ) }}"
                                    >
                                        BAPP
                                    </span>

                                    <span
                                        class="rounded-lg px-3 py-1.5 text-xs font-medium
                                            {{ $stageClass(
                                                $projectDone,
                                                false,
                                                $projectCancelled
                                            ) }}"
                                    >
                                        Project Selesai
                                    </span>

                                    <span
                                        class="rounded-lg px-3 py-1.5 text-xs font-medium
                                            {{ $stageClass(
                                                $invoicePaid,
                                                $invoiceIssued && ! $invoicePaid
                                            ) }}"
                                    >
                                        Invoice
                                    </span>

                                    <span
                                        class="rounded-lg px-3 py-1.5 text-xs font-medium
                                            {{ $stageClass(
                                                $ratingDone,
                                                $invoicePaid && ! $ratingDone
                                            ) }}"
                                    >
                                        Rating
                                    </span>
                                </div>

                                <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-slate-500">
                                    <div class="flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-full bg-green-500"></span>
                                        <span>Selesai</span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                                        <span>Sedang diproses</span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-full bg-slate-300"></span>
                                        <span>Belum diproses</span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-full bg-red-500"></span>
                                        <span>Dibatalkan</span>
                                    </div>
                                </div>
                            </div>

                            @if ($survey)
                                <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                                Status Survey
                                            </p>

                                            <p class="mt-1 text-sm font-semibold text-slate-800">
                                                @if ($survey->status === 'dijadwalkan')
                                                    Dijadwalkan
                                                @elseif ($survey->status === 'berlangsung')
                                                    Sedang Berlangsung
                                                @elseif ($survey->status === 'selesai')
                                                    Selesai
                                                @elseif ($survey->status === 'dibatalkan')
                                                    Dibatalkan
                                                @else
                                                    {{ ucfirst($survey->status) }}
                                                @endif
                                            </p>
                                        </div>

                                        @if ($survey->scheduled_at)
                                            <div class="sm:text-right">
                                                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                                    Jadwal Survey
                                                </p>

                                                <p class="mt-1 text-sm font-semibold text-slate-700">
                                                    {{ $survey->scheduled_at->format('d/m/Y H:i') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if ($production && ! $productionDone && ! $productionCancelled)
                                <div class="mt-5 rounded-xl bg-slate-50 p-4">
                                    <div class="flex items-center justify-between gap-4">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-700">
                                                Progress Produksi & Instalasi
                                            </p>

                                            <p class="mt-1 text-xs text-slate-500">
                                                Tahap pekerjaan lapangan saat ini
                                            </p>
                                        </div>

                                        <span class="text-sm font-bold text-blue-600">
                                            {{ $production->progress }}%
                                        </span>
                                    </div>

                                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-200">
                                        <div
                                            class="h-full rounded-full bg-blue-600"
                                            style="width: {{ min(100, max(0, $production->progress)) }}%"
                                        ></div>
                                    </div>
                                </div>
                            @endif

                            @if ($invoice)
                                <div class="mt-5 flex flex-col gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            Invoice
                                        </p>

                                        <p class="mt-1 text-sm font-semibold text-slate-800">
                                            {{ $invoice->invoice_number }}
                                        </p>

                                        <p class="mt-1 text-sm font-bold text-slate-900">
                                            Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                        </p>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-3">
                                        @if ($invoicePaid)
                                            <span class="rounded-full bg-green-100 px-3 py-1.5 text-xs font-semibold text-green-700">
                                                Lunas
                                            </span>
                                        @else
                                            <span class="rounded-full bg-blue-100 px-3 py-1.5 text-xs font-semibold text-blue-700">
                                                Menunggu Pembayaran
                                            </span>
                                        @endif

                                        <a
                                            href="{{ route('invoices.show', $invoice) }}"
                                            class="text-sm font-semibold text-blue-600 hover:text-blue-700"
                                        >
                                            Lihat Invoice →
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if ($invoicePaid && ! $ratingDone)
                                <div class="mt-5 flex flex-col gap-4 rounded-xl border border-amber-200 bg-amber-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="font-semibold text-amber-800">
                                            Bagaimana pengalaman Anda?
                                        </p>

                                        <p class="mt-1 text-sm text-amber-700">
                                            Project telah selesai dan Invoice sudah lunas. Anda dapat memberikan Rating dan ulasan.
                                        </p>
                                    </div>

                                    <a
                                        href="{{ route('client.ratings.create', $project) }}"
                                        class="inline-flex shrink-0 items-center justify-center rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-600"
                                    >
                                        Beri Rating
                                    </a>
                                </div>
                            @elseif ($ratingDone)
                                <div class="mt-5 flex flex-col gap-4 rounded-xl border border-green-200 bg-green-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="font-semibold text-green-800">
                                            Rating Telah Diberikan
                                        </p>

                                        <div class="mt-2 flex items-center gap-1">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <span class="text-lg {{ $i <= $rating->rating ? 'text-amber-400' : 'text-slate-300' }}">
                                                    ★
                                                </span>
                                            @endfor

                                            <span class="ml-2 text-sm font-semibold text-green-700">
                                                {{ $rating->rating }}/5
                                            </span>
                                        </div>
                                    </div>

                                    <a
                                        href="{{ route('ratings.show', $rating) }}"
                                        class="text-sm font-semibold text-green-700 hover:text-green-800"
                                    >
                                        Lihat Ulasan →
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($projects->hasPages())
                <div>
                    {{ $projects->links() }}
                </div>
            @endif
        @else
            <div class="rounded-xl border border-slate-200 bg-white px-6 py-14 text-center shadow-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-blue-50 text-2xl text-blue-600">
                    ◎
                </div>

                <h2 class="mt-4 text-lg font-bold text-slate-800">
                    Belum Ada Project
                </h2>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                    Project akan muncul di halaman Tracking setelah Order Anda melewati proses Work Estimate dan telah disetujui.
                </p>

                <a
                    href="{{ route('client.orders.index') }}"
                    class="mt-5 inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    Lihat Order Saya
                </a>
            </div>
        @endif
    </div>
@endsection