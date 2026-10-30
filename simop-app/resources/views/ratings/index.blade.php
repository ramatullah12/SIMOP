@extends('layouts.app')

@section('title', 'Rating | SIMOP')
@section('page-title', 'Rating')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;

        $ratingLabels = [
            1 => 'Sangat Kurang',
            2 => 'Kurang',
            3 => 'Cukup',
            4 => 'Baik',
            5 => 'Sangat Baik',
        ];
    @endphp

    <div class="space-y-6">
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

        <div>
            <h2 class="text-2xl font-bold text-slate-900">
                Rating & Ulasan
            </h2>

            @if ($role === 'admin')
                <p class="mt-1 text-sm leading-6 text-slate-500">
                    Lihat penilaian dan ulasan yang diberikan Client terhadap Project yang telah selesai.
                </p>
            @else
                <p class="mt-1 text-sm leading-6 text-slate-500">
                    Berikan penilaian untuk Project yang telah selesai dan Invoice-nya sudah lunas.
                </p>
            @endif
        </div>

        @if ($role === 'admin')
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    No.
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Project
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Client
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Rating
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Ulasan
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Tanggal
                                </th>

                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($ratings as $rating)
                                @php
                                    $project = $rating->project;
                                    $order = $project?->workEstimate?->order;

                                    $ratingLabel = $ratingLabels[$rating->rating]
                                        ?? '-';
                                @endphp

                                <tr class="hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                        {{ $ratings->firstItem() + $loop->index }}
                                    </td>

                                    <td class="min-w-56 px-6 py-4">
                                        <p class="text-sm font-semibold text-slate-900">
                                            {{ $project?->title ?? '-' }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $project?->project_number ?? '-' }}
                                        </p>

                                        @if ($order?->order_number)
                                            <p class="mt-1 text-xs text-slate-400">
                                                Order: {{ $order->order_number }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ $rating->client?->name ?? '-' }}
                                        </p>

                                        @if ($rating->client?->email)
                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ $rating->client->email }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-1">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <span class="text-lg {{ $i <= $rating->rating ? 'text-amber-400' : 'text-slate-300' }}">
                                                    ★
                                                </span>
                                            @endfor
                                        </div>

                                        <p class="mt-1 text-xs font-semibold text-slate-600">
                                            {{ $rating->rating }}/5
                                            ·
                                            {{ $ratingLabel }}
                                        </p>
                                    </td>

                                    <td class="max-w-sm px-6 py-4">
                                        @if ($rating->review)
                                            <p class="text-sm leading-6 text-slate-600">
                                                {{ \Illuminate\Support\Str::limit($rating->review, 100) }}
                                            </p>
                                        @else
                                            <span class="text-sm italic text-slate-400">
                                                Tidak ada ulasan.
                                            </span>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <p class="text-sm text-slate-700">
                                            {{ $rating->created_at?->format('d/m/Y') ?? '-' }}
                                        </p>

                                        @if ($rating->created_at)
                                            <p class="mt-1 text-xs text-slate-400">
                                                {{ $rating->created_at->format('H:i') }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <a
                                            href="{{ route('ratings.show', $rating) }}"
                                            class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100"
                                        >
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="7"
                                        class="px-6 py-12 text-center"
                                    >
                                        <div class="mx-auto max-w-md">
                                            <div class="text-4xl text-slate-300">
                                                ☆
                                            </div>

                                            <h3 class="mt-4 font-bold text-slate-800">
                                                Belum Ada Rating
                                            </h3>

                                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                                Belum ada penilaian atau ulasan yang diberikan oleh Client.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($ratings->hasPages())
                    <div class="border-t border-slate-200 px-6 py-4">
                        {{ $ratings->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <h3 class="text-sm font-semibold text-blue-800">
                    Penilaian Project
                </h3>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Project akan tersedia untuk dinilai setelah Project selesai dan Invoice telah dikonfirmasi Lunas. Setiap Project hanya dapat diberikan satu Rating.
                </p>
            </div>

            <div class="grid gap-5">
                @forelse ($projects as $project)
                    @php
                        $order = $project->workEstimate?->order;
                        $invoice = $project->invoice;
                        $rating = $project->rating;

                        $ratingLabel = $rating
                            ? ($ratingLabels[$rating->rating] ?? '-')
                            : null;
                    @endphp

                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-lg font-bold text-slate-900">
                                        {{ $project->title }}
                                    </h3>

                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Project Selesai
                                    </span>

                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Invoice Lunas
                                    </span>
                                </div>

                                <p class="mt-2 text-sm font-medium text-slate-500">
                                    {{ $project->project_number }}
                                </p>

                                <div class="mt-5 grid grid-cols-1 gap-4 border-t border-slate-100 pt-4 sm:grid-cols-2 lg:grid-cols-3">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            Order
                                        </p>

                                        <p class="mt-1 text-sm font-medium text-slate-700">
                                            {{ $order?->order_number ?? '-' }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            Invoice
                                        </p>

                                        <p class="mt-1 text-sm font-medium text-slate-700">
                                            {{ $invoice?->invoice_number ?? '-' }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            Total Invoice
                                        </p>

                                        <p class="mt-1 text-sm font-semibold text-slate-800">
                                            Rp {{ number_format((float) ($invoice?->total_amount ?? 0), 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="shrink-0 lg:w-64">
                                @if ($rating)
                                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">
                                            Rating Anda
                                        </p>

                                        <div class="mt-2 flex items-center gap-1">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <span class="text-xl {{ $i <= $rating->rating ? 'text-amber-400' : 'text-slate-300' }}">
                                                    ★
                                                </span>
                                            @endfor
                                        </div>

                                        <p class="mt-2 text-sm font-semibold text-amber-800">
                                            {{ $rating->rating }}/5 · {{ $ratingLabel }}
                                        </p>

                                        <a
                                            href="{{ route('ratings.show', $rating) }}"
                                            class="mt-4 inline-flex text-sm font-semibold text-blue-600 hover:text-blue-800"
                                        >
                                            Lihat Rating & Ulasan →
                                        </a>
                                    </div>
                                @else
                                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                                        <p class="text-sm font-semibold text-blue-800">
                                            Belum Dinilai
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-blue-700">
                                            Bagikan pengalaman Anda terhadap Project ini.
                                        </p>

                                        <a
                                            href="{{ route('client.ratings.create', $project) }}"
                                            class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                                        >
                                            Beri Rating
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-slate-200 bg-white px-6 py-12 text-center shadow-sm">
                        <div class="mx-auto max-w-md">
                            <div class="text-4xl text-slate-300">
                                ☆
                            </div>

                            <h3 class="mt-4 font-bold text-slate-800">
                                Belum Ada Project yang Dapat Dinilai
                            </h3>

                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Rating dapat diberikan setelah Project selesai dan Invoice telah berstatus Lunas.
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($projects->hasPages())
                <div class="rounded-xl border border-slate-200 bg-white px-6 py-4 shadow-sm">
                    {{ $projects->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection