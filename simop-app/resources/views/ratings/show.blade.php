@extends('layouts.app')

@section('title', 'Detail Rating - SIMOP')
@section('page-title', 'Detail Rating')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;
        $project = $rating->project;
        $order = $project?->workEstimate?->order;

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

    <div class="mx-auto max-w-4xl space-y-6">

        <div>
            <a
                href="{{ $backRoute }}"
                class="text-sm font-medium text-blue-600 hover:text-blue-700"
            >
                ← Kembali ke Rating
            </a>

            <h1 class="mt-3 text-2xl font-bold text-slate-900">
                Detail Rating & Ulasan
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Penilaian terhadap Project yang telah selesai.
            </p>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">
                            Project
                        </p>

                        <h2 class="mt-1 text-xl font-bold text-slate-900">
                            {{ $project?->title ?? '-' }}
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $project?->project_number ?? '-' }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-amber-50 px-5 py-3">
                        <div class="flex items-center gap-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="text-2xl {{ $i <= $rating->rating ? 'text-amber-400' : 'text-slate-300' }}">
                                    ★
                                </span>
                            @endfor
                        </div>

                        <p class="mt-1 text-center text-sm font-semibold text-amber-700">
                            {{ $rating->rating }}/5
                        </p>
                    </div>

                </div>
            </div>

            <div class="grid gap-6 p-6 sm:grid-cols-2">

                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-400">
                        Penilaian
                    </p>

                    <p class="mt-2 font-semibold text-slate-900">
                        {{ $ratingLabel }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-400">
                        Tanggal Rating
                    </p>

                    <p class="mt-2 font-semibold text-slate-900">
                        {{ $rating->created_at?->format('d/m/Y H:i') ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-400">
                        Client
                    </p>

                    <p class="mt-2 font-semibold text-slate-900">
                        {{ $rating->client?->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-400">
                        Perusahaan
                    </p>

                    <p class="mt-2 font-semibold text-slate-900">
                        {{ $order?->company_name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-400">
                        Event
                    </p>

                    <p class="mt-2 font-semibold text-slate-900">
                        {{ $order?->event_name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-400">
                        Lokasi
                    </p>

                    <p class="mt-2 font-semibold text-slate-900">
                        {{ $order?->event_location ?? '-' }}
                    </p>
                </div>

            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        Ulasan Client
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Ulasan yang diberikan setelah Project selesai.
                    </p>
                </div>

                <div class="hidden rounded-lg bg-amber-50 px-3 py-2 sm:block">
                    <span class="font-bold text-amber-600">
                        ★ {{ $rating->rating }}
                    </span>
                </div>
            </div>

            <div class="mt-5 rounded-xl bg-slate-50 p-5">
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
                <h2 class="font-semibold text-blue-800">
                    Rating Client
                </h2>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Rating dan ulasan ini diberikan oleh Client setelah Project selesai dan Invoice dinyatakan lunas.
                </p>
            </div>
        @elseif ($role === 'client')
            <div class="rounded-xl border border-green-200 bg-green-50 p-5">
                <div class="flex items-start gap-3">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-100 font-bold text-green-700">
                        ✓
                    </div>

                    <div>
                        <h2 class="font-semibold text-green-800">
                            Rating Telah Dikirim
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-green-700">
                            Terima kasih. Rating dan ulasan Anda telah tersimpan sebagai penilaian terhadap Project dan layanan yang diberikan.
                        </p>
                    </div>

                </div>
            </div>
        @endif

    </div>
@endsection