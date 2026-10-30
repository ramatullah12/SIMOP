@extends('layouts.app')

@section('title', 'Tambah Survey | SIMOP')
@section('page-title', 'Tambah Survey')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <a
                href="{{ route('admin.surveys.index') }}"
                class="text-sm font-medium text-blue-600 hover:text-blue-800"
            >
                &larr; Kembali ke Daftar Survey
            </a>

            <h2 class="mt-3 text-2xl font-bold text-slate-900">
                Buat Jadwal Survey
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Tentukan Order & RFQ, lokasi, jadwal, dan Petugas Lapangan yang akan melaksanakan Survey.
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold">
                    Terdapat data yang perlu diperbaiki:
                </p>

                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ route('admin.surveys.store') }}"
            method="POST"
            class="space-y-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
        >
            @csrf

            <div>
                <h3 class="text-lg font-bold text-slate-900">
                    Informasi Survey
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Lengkapi informasi awal untuk menjadwalkan Survey lapangan.
                </p>
            </div>

            <div>
                <label
                    for="order_id"
                    class="mb-2 block text-sm font-semibold text-slate-700"
                >
                    Order & RFQ <span class="text-red-500">*</span>
                </label>

                <select
                    id="order_id"
                    name="order_id"
                    required
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                >
                    <option value="">
                        -- Pilih Order --
                    </option>

                    @foreach ($orders as $order)
                        <option
                            value="{{ $order->id }}"
                            @selected(old('order_id') == $order->id)
                        >
                            {{ $order->order_number }} — {{ $order->company_name }}
                        </option>
                    @endforeach
                </select>

                @if ($orders->isEmpty())
                    <p class="mt-2 text-sm font-medium text-amber-600">
                        Belum ada Order yang tersedia untuk dibuatkan Survey.
                    </p>
                @endif
            </div>

            <div>
                <label
                    for="location"
                    class="mb-2 block text-sm font-semibold text-slate-700"
                >
                    Lokasi Survey <span class="text-red-500">*</span>
                </label>

                <input
                    id="location"
                    name="location"
                    type="text"
                    required
                    value="{{ old('location') }}"
                    placeholder="Contoh: Jl. Jenderal Sudirman, Palembang"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                >
            </div>

            <div>
                <label
                    for="assigned_to"
                    class="mb-2 block text-sm font-semibold text-slate-700"
                >
                    Petugas Lapangan
                </label>

                <select
                    id="assigned_to"
                    name="assigned_to"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                >
                    <option value="">
                        -- Belum Ditugaskan --
                    </option>

                    @foreach ($officers as $officer)
                        <option
                            value="{{ $officer->id }}"
                            @selected(old('assigned_to') == $officer->id)
                        >
                            {{ $officer->name }}
                        </option>
                    @endforeach
                </select>

                @if ($officers->isEmpty())
                    <p class="mt-2 text-sm font-medium text-amber-600">
                        Belum ada akun Petugas Lapangan yang tersedia.
                    </p>
                @endif
            </div>

            <div>
                <label
                    for="scheduled_at"
                    class="mb-2 block text-sm font-semibold text-slate-700"
                >
                    Tanggal dan Waktu Survey
                </label>

                <input
                    id="scheduled_at"
                    name="scheduled_at"
                    type="datetime-local"
                    value="{{ old('scheduled_at') }}"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                >

                <p class="mt-2 text-xs text-slate-500">
                    Tentukan waktu pelaksanaan Survey jika jadwal sudah tersedia.
                </p>
            </div>

            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                <div class="flex gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-100 font-bold text-amber-700">
                        !
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-amber-800">
                            Status Awal Survey
                        </p>

                        <p class="mt-1 text-sm leading-6 text-amber-700">
                            Survey baru akan dibuat dengan status Dijadwalkan. Status dapat diperbarui setelah Survey dibuat.
                        </p>
                    </div>
                </div>
            </div>

            <input
                type="hidden"
                name="status"
                value="dijadwalkan"
            >

            <div>
                <label
                    for="notes"
                    class="mb-2 block text-sm font-semibold text-slate-700"
                >
                    Catatan Tambahan
                </label>

                <textarea
                    id="notes"
                    name="notes"
                    rows="4"
                    placeholder="Masukkan catatan atau informasi tambahan untuk Petugas Lapangan"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                >{{ old('notes') }}</textarea>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
                <a
                    href="{{ route('admin.surveys.index') }}"
                    class="rounded-lg border border-slate-300 px-5 py-3 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    @disabled($orders->isEmpty())
                    class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-slate-300"
                >
                    Simpan Survey
                </button>
            </div>
        </form>
    </div>
@endsection