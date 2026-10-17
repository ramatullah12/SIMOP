
@extends('layouts.app')

@section('title', 'Tambah Survey | SIMOP')
@section('page-title', 'Tambah Survey')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    <div>
        <a href="{{ route('admin.surveys.index') }}"
           class="text-sm font-medium text-blue-600 hover:text-blue-800">
            &larr; Kembali ke Daftar Survey
        </a>
        <h2 class="mt-3 text-2xl font-bold text-slate-900">Buat Jadwal Survey</h2>
        <p class="mt-1 text-sm text-slate-500">
            Isi informasi survey untuk Order & RFQ yang dipilih.
        </p>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.surveys.store') }}" method="POST"
          class="space-y-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf

        <div>
            <label for="order_id" class="mb-2 block text-sm font-semibold text-slate-700">
                Order & RFQ <span class="text-red-500">*</span>
            </label>
            <select id="order_id" name="order_id" required
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                <option value="">-- Pilih Order --</option>
                @foreach ($orders as $order)
                    <option value="{{ $order->id }}" @selected(old('order_id') == $order->id)>
                        {{ $order->order_number }} — {{ $order->company_name }}
                    </option>
                @endforeach
            </select>
            @if ($orders->isEmpty())
                <p class="mt-2 text-sm text-amber-600">
                    Belum ada Order. Buat Order terlebih dahulu sebelum menjadwalkan survey.
                </p>
            @endif
        </div>

        <div>
            <label for="location" class="mb-2 block text-sm font-semibold text-slate-700">
                Lokasi Survey <span class="text-red-500">*</span>
            </label>
            <input id="location" name="location" type="text" required
                   value="{{ old('location') }}"
                   placeholder="Contoh: Jl. Jenderal Sudirman, Palembang"
                   class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
        </div>

        <div>
            <label for="assigned_to" class="mb-2 block text-sm font-semibold text-slate-700">
                Petugas Lapangan
            </label>
            <select id="assigned_to" name="assigned_to"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                <option value="">-- Belum Ditugaskan --</option>
                @foreach ($officers as $officer)
                    <option value="{{ $officer->id }}" @selected(old('assigned_to') == $officer->id)>
                        {{ $officer->name }}
                    </option>
                @endforeach
            </select>
            @if ($officers->isEmpty())
                <p class="mt-2 text-sm text-amber-600">
                    Belum ada akun Petugas Lapangan. Kamu bisa membuat akun tersebut terlebih dahulu.
                </p>
            @endif
        </div>

        <div>
            <label for="scheduled_at" class="mb-2 block text-sm font-semibold text-slate-700">
                Tanggal dan Waktu Survey
            </label>
            <input id="scheduled_at" name="scheduled_at" type="datetime-local"
                   value="{{ old('scheduled_at') }}"
                   class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
        </div>

        <div>
            <label for="status" class="mb-2 block text-sm font-semibold text-slate-700">
                Status Survey
            </label>
            <select id="status" name="status" required
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                <option value="dijadwalkan" @selected(old('status', 'dijadwalkan') === 'dijadwalkan')>Dijadwalkan</option>
                <option value="berlangsung" @selected(old('status') === 'berlangsung')>Berlangsung</option>
                <option value="selesai" @selected(old('status') === 'selesai')>Selesai</option>
                <option value="dibatalkan" @selected(old('status') === 'dibatalkan')>Dibatalkan</option>
            </select>
        </div>

        <div>
            <label for="findings" class="mb-2 block text-sm font-semibold text-slate-700">
                Hasil atau Temuan Survey
            </label>
            <textarea id="findings" name="findings" rows="4"
                      placeholder="Isi hasil survey setelah kegiatan dilaksanakan..."
                      class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">{{ old('findings') }}</textarea>
        </div>

        <div>
            <label for="notes" class="mb-2 block text-sm font-semibold text-slate-700">
                Catatan Tambahan
            </label>
            <textarea id="notes" name="notes" rows="3"
                      placeholder="Catatan tambahan..."
                      class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">{{ old('notes') }}</textarea>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.surveys.index') }}"
               class="rounded-lg border border-slate-300 px-5 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Batal
            </a>
            <button type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                Simpan Survey
            </button>
        </div>
    </form>
</div>
@endsection
