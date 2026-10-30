@extends('layouts.app')

@section('title', 'Beri Rating | SIMOP')
@section('page-title', 'Beri Rating')

@section('content')
    @php
        $workEstimate = $project->workEstimate;
        $order = $workEstimate?->order;
        $client = $order?->client;
        $invoice = $project->invoice;

        $oldRating = (int) old('rating', 0);
    @endphp

    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <a
                href="{{ route('client.ratings.index') }}"
                class="text-sm font-semibold text-blue-600 hover:text-blue-800"
            >
                &larr; Kembali ke Rating
            </a>

            <h2 class="mt-3 text-2xl font-bold text-slate-900">
                Beri Rating & Ulasan
            </h2>

            <p class="mt-1 text-sm leading-6 text-slate-500">
                Berikan penilaian Anda terhadap Project yang telah selesai.
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                <p class="font-semibold text-red-700">
                    Data belum dapat disimpan.
                </p>

                <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                        Project
                    </p>

                    <h3 class="mt-2 text-xl font-bold text-slate-900">
                        {{ $project->title }}
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ $project->project_number }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex w-fit rounded-full bg-green-100 px-3 py-1.5 text-xs font-semibold text-green-700">
                        Project Selesai
                    </span>

                    <span class="inline-flex w-fit rounded-full bg-green-100 px-3 py-1.5 text-xs font-semibold text-green-700">
                        Invoice Lunas
                    </span>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-5 border-t border-slate-100 pt-5 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Order
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $order?->order_number ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Client
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $client?->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Work Estimate
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $workEstimate?->estimate_number ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Invoice
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $invoice?->invoice_number ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Total Invoice
                    </p>

                    <p class="mt-1 text-sm font-bold text-slate-900">
                        Rp {{ number_format((float) ($invoice?->total_amount ?? 0), 0, ',', '.') }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Status Pembayaran
                    </p>

                    <span class="mt-1 inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                        Lunas
                    </span>
                </div>
            </div>
        </div>

        <form
            action="{{ route('client.ratings.store', $project) }}"
            method="POST"
            class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
        >
            @csrf

            <div class="space-y-7 p-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-800">
                        Rating <span class="text-red-500">*</span>
                    </label>

                    <p class="mt-1 text-sm text-slate-500">
                        Pilih nilai sesuai dengan pengalaman Anda terhadap Project ini.
                    </p>

                    <div class="mt-5 grid grid-cols-5 gap-2 sm:flex sm:gap-3">
                        @for ($i = 1; $i <= 5; $i++)
                            <label class="cursor-pointer">
                                <input
                                    type="radio"
                                    name="rating"
                                    value="{{ $i }}"
                                    class="peer sr-only"
                                    @checked(old('rating') == $i)
                                    required
                                >

                                <span class="flex min-h-16 items-center justify-center rounded-xl border-2 border-slate-200 bg-white px-3 text-lg font-semibold text-slate-400 transition hover:border-amber-300 hover:bg-amber-50 hover:text-amber-500 peer-checked:border-amber-400 peer-checked:bg-amber-50 peer-checked:text-amber-500 sm:min-w-20">
                                    ★ {{ $i }}
                                </span>
                            </label>
                        @endfor
                    </div>

                    <div
                        id="rating-info"
                        class="{{ $oldRating ? '' : 'hidden' }} mt-4 rounded-lg border border-amber-200 bg-amber-50 p-4"
                    >
                        <div class="flex items-center gap-3">
                            <span
                                id="rating-stars"
                                class="text-xl tracking-wide text-amber-500"
                            >
                                -
                            </span>

                            <div>
                                <p
                                    id="rating-label"
                                    class="text-sm font-semibold text-amber-800"
                                >
                                    -
                                </p>

                                <p
                                    id="rating-value"
                                    class="mt-0.5 text-xs text-amber-700"
                                >
                                    -
                                </p>
                            </div>
                        </div>
                    </div>

                    @error('rating')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="review"
                        class="block text-sm font-semibold text-slate-800"
                    >
                        Ulasan
                    </label>

                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Ceritakan pengalaman Anda selama menggunakan layanan pada Project ini.
                    </p>

                    <textarea
                        name="review"
                        id="review"
                        rows="6"
                        maxlength="5000"
                        class="mt-3 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        placeholder="Tuliskan ulasan Anda di sini..."
                    >{{ old('review') }}</textarea>

                    <div class="mt-2 flex items-center justify-between gap-4">
                        <p class="text-xs text-slate-400">
                            Ulasan bersifat opsional.
                        </p>

                        <p
                            id="review-counter"
                            class="text-xs text-slate-400"
                        >
                            0 / 5000
                        </p>
                    </div>

                    @error('review')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                    <p class="text-sm font-semibold text-blue-800">
                        Sebelum Mengirim Penilaian
                    </p>

                    <p class="mt-1 text-sm leading-6 text-blue-700">
                        Satu Project hanya dapat diberikan satu Rating. Setelah dikirim, Rating dan ulasan menjadi penilaian final dan tidak dapat diubah kembali.
                    </p>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 sm:flex-row sm:justify-end">
                <a
                    href="{{ route('client.ratings.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    Kirim Rating & Ulasan
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ratingInputs = document.querySelectorAll(
                'input[name="rating"]'
            );

            const ratingInfo = document.getElementById('rating-info');
            const ratingStars = document.getElementById('rating-stars');
            const ratingLabel = document.getElementById('rating-label');
            const ratingValue = document.getElementById('rating-value');

            const review = document.getElementById('review');
            const reviewCounter = document.getElementById('review-counter');

            const labels = {
                1: 'Sangat Kurang',
                2: 'Kurang',
                3: 'Cukup',
                4: 'Baik',
                5: 'Sangat Baik'
            };

            function updateRating() {
                const selected = document.querySelector(
                    'input[name="rating"]:checked'
                );

                if (!selected) {
                    ratingInfo.classList.add('hidden');
                    return;
                }

                const value = Number(selected.value);

                ratingStars.textContent =
                    '★'.repeat(value) + '☆'.repeat(5 - value);

                ratingLabel.textContent =
                    labels[value] || '-';

                ratingValue.textContent =
                    value + ' dari 5 bintang';

                ratingInfo.classList.remove('hidden');
            }

            function updateReviewCounter() {
                reviewCounter.textContent =
                    review.value.length + ' / 5000';
            }

            ratingInputs.forEach(function (input) {
                input.addEventListener(
                    'change',
                    updateRating
                );
            });

            review.addEventListener(
                'input',
                updateReviewCounter
            );

            updateRating();
            updateReviewCounter();
        });
    </script>
@endsection