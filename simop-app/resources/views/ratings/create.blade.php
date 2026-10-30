@extends('layouts.app')

@section('title', 'Beri Rating - SIMOP')
@section('page-title', 'Beri Rating')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <a href="{{ route('client.ratings.index') }}"
               class="text-sm font-medium text-blue-600 hover:text-blue-700">
                ← Kembali ke Rating
            </a>

            <h1 class="mt-3 text-2xl font-bold text-slate-900">
                Beri Rating & Ulasan
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Berikan penilaian Anda terhadap Project yang telah selesai.
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                <p class="font-semibold text-red-700">
                    Data belum dapat disimpan.
                </p>

                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">
                        Project
                    </p>

                    <h2 class="mt-2 text-xl font-bold text-slate-900">
                        {{ $project->title }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ $project->project_number }}
                    </p>
                </div>

                <span class="inline-flex w-fit rounded-full bg-green-100 px-3 py-1.5 text-xs font-semibold text-green-700">
                    Invoice Lunas
                </span>
            </div>

            <div class="mt-6 grid gap-4 border-t border-slate-100 pt-5 sm:grid-cols-2">
                <div>
                    <p class="text-xs text-slate-500">
                        Event
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $project->workEstimate?->order?->event_name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-slate-500">
                        Lokasi
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $project->workEstimate?->order?->event_location ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-slate-500">
                        Perusahaan
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $project->workEstimate?->order?->company_name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-slate-500">
                        Total Invoice
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        Rp {{ number_format($project->invoice?->total_amount ?? 0, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <form action="{{ route('client.ratings.store', $project) }}"
              method="POST"
              class="rounded-xl border border-slate-200 bg-white shadow-sm">

            @csrf

            <div class="space-y-7 p-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-800">
                        Rating
                    </label>

                    <p class="mt-1 text-sm text-slate-500">
                        Pilih nilai dari 1 sampai 5 bintang.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-3">
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

                                <span class="flex h-14 min-w-14 items-center justify-center rounded-xl border-2 border-slate-200 bg-white px-4 text-xl text-slate-400 transition hover:border-amber-300 hover:bg-amber-50 peer-checked:border-amber-400 peer-checked:bg-amber-50 peer-checked:text-amber-500">
                                    ★ {{ $i }}
                                </span>
                            </label>
                        @endfor
                    </div>

                    @error('rating')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="review"
                           class="block text-sm font-semibold text-slate-800">
                        Ulasan
                    </label>

                    <p class="mt-1 text-sm text-slate-500">
                        Ceritakan pengalaman Anda selama menggunakan layanan kami.
                    </p>

                    <textarea
                        name="review"
                        id="review"
                        rows="6"
                        maxlength="5000"
                        class="mt-3 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Tuliskan ulasan Anda di sini..."
                    >{{ old('review') }}</textarea>

                    @error('review')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                    <p class="text-sm font-semibold text-blue-800">
                        Penilaian Project
                    </p>

                    <p class="mt-1 text-sm leading-6 text-blue-700">
                        Satu Project hanya dapat diberikan satu Rating. Pastikan nilai dan ulasan sudah sesuai sebelum dikirim.
                    </p>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 sm:flex-row sm:justify-end">
                <a href="{{ route('client.ratings.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                    Batal
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                    Kirim Rating
                </button>
            </div>
        </form>
    </div>
@endsection