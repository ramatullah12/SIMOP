
@extends('layouts.app')

@section('title', 'Ajukan Order | SIMOP')
@section('page-title', 'Ajukan Order')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Form Order & RFQ</h1>
        <p class="mt-2 text-sm text-slate-500">
            Isi informasi pekerjaan yang ingin diajukan.
        </p>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('client.orders.store') }}"
          class="space-y-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        @csrf

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium">Nama Perusahaan</label>
                <input name="company_name" value="{{ old('company_name') }}" required
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium">Nama Contact Person</label>
                <input name="contact_person" value="{{ old('contact_person') }}" required
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium">Nomor Telepon</label>
                <input name="phone" value="{{ old('phone') }}" required
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium">Email Kontak</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium">Nama Event / Pekerjaan</label>
                <input name="event_name" value="{{ old('event_name') }}" required
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium">Tanggal Event</label>
                <input type="date" name="event_date" min="{{ now()->toDateString() }}"
                       value="{{ old('event_date') }}" required
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium">Lokasi Event</label>
                <input name="event_location" value="{{ old('event_location') }}" required
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium">Deskripsi Kebutuhan</label>
                <textarea name="description" rows="4" required
                          class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
            </div>

            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium">Estimasi Anggaran (Rp)</label>
                <input type="number" name="estimated_budget" min="0" step="1000"
                       value="{{ old('estimated_budget') }}"
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div class="flex flex-col-reverse justify-end gap-3 border-t border-slate-100 pt-5 sm:flex-row">
            <a href="{{ route('client.orders.index') }}"
               class="rounded-lg border border-slate-300 px-5 py-3 text-center text-sm font-semibold hover:bg-slate-50">
                Batal
            </a>
            <button type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                Kirim Pengajuan
            </button>
        </div>
    </form>
</div>
@endsection
