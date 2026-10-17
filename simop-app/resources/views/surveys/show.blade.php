
@extends('layouts.app')

@section('title', 'Detail Survey | SIMOP')
@section('page-title', 'Detail Survey')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('admin.surveys.index') }}"
               class="text-sm font-medium text-blue-600 hover:text-blue-800">
                &larr; Kembali ke Daftar Survey
            </a>
            <h2 class="mt-3 text-2xl font-bold text-slate-900">Detail Survey</h2>
            <p class="mt-1 text-sm text-slate-500">
                Informasi survey untuk Order {{ $survey->order->order_number ?? '#'.$survey->order_id }}
            </p>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-bold text-slate-900">Informasi Order</h3>

        <dl class="mt-4 grid gap-5 sm:grid-cols-2">
            <div>
                <dt class="text-sm text-slate-500">Nomor Order</dt>
                <dd class="mt-1 font-semibold text-slate-900">
                    {{ $survey->order->order_number ?? '-' }}
                </dd>
            </div>

            <div>
                <dt class="text-sm text-slate-500">Perusahaan</dt>
                <dd class="mt-1 font-semibold text-slate-900">
                    {{ $survey->order->company_name ?? '-' }}
                </dd>
            </div>

            <div>
                <dt class="text-sm text-slate-500">Lokasi Survey</dt>
                <dd class="mt-1 font-semibold text-slate-900">{{ $survey->location }}</dd>
            </div>

            <div>
                <dt class="text-sm text-slate-500">Petugas Lapangan</dt>
                <dd class="mt-1 font-semibold text-slate-900">
                    {{ $survey->assignedUser->name ?? 'Belum ditugaskan' }}
                </dd>
            </div>

            <div>
                <dt class="text-sm text-slate-500">Jadwal Survey</dt>
                <dd class="mt-1 font-semibold text-slate-900">
                    {{ $survey->scheduled_at?->format('d-m-Y H:i') ?? 'Belum dijadwalkan' }}
                </dd>
            </div>

            <div>
                <dt class="text-sm text-slate-500">Status Saat Ini</dt>
                <dd class="mt-1 font-semibold text-slate-900">
                    {{ ucfirst($survey->status) }}
                </dd>
            </div>
        </dl>
    </div>

    <form action="{{ route('admin.surveys.update', $survey) }}"
          method="POST"
          class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')

        <h3 class="text-lg font-bold text-slate-900">Perbarui Survey</h3>

        <div>
            <label for="assigned_to" class="mb-2 block text-sm font-semibold text-slate-700">
                Petugas Lapangan
            </label>
            <select id="assigned_to" name="assigned_to"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3">
                <option value="">-- Belum Ditugaskan --</option>
                @foreach ($officers as $officer)
                    <option value="{{ $officer->id }}"
                        @selected(old('assigned_to', $survey->assigned_to) == $officer->id)>
                        {{ $officer->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="scheduled_at" class="mb-2 block text-sm font-semibold text-slate-700">
                Tanggal dan Waktu Survey
            </label>
            <input id="scheduled_at" name="scheduled_at" type="datetime-local"
                   value="{{ old('scheduled_at', $survey->scheduled_at?->format('Y-m-d\TH:i')) }}"
                   class="w-full rounded-lg border border-slate-300 px-4 py-3">
        </div>

        <div>
            <label for="location" class="mb-2 block text-sm font-semibold text-slate-700">
                Lokasi Survey
            </label>
            <input id="location" name="location" type="text" required
                   value="{{ old('location', $survey->location) }}"
                   class="w-full rounded-lg border border-slate-300 px-4 py-3">
        </div>

        <div>
            <label for="status" class="mb-2 block text-sm font-semibold text-slate-700">
                Status Survey
            </label>
            <select id="status" name="status" required
                    class="w-full rounded-lg border border-slate-300 px-4 py-3">
                @foreach ([
                    'dijadwalkan' => 'Dijadwalkan',
                    'berlangsung' => 'Berlangsung',
                    'selesai' => 'Selesai',
                    'dibatalkan' => 'Dibatalkan',
                ] as $value => $label)
                    <option value="{{ $value }}"
                        @selected(old('status', $survey->status) === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="findings" class="mb-2 block text-sm font-semibold text-slate-700">
                Hasil atau Temuan Survey
            </label>
            <textarea id="findings" name="findings" rows="4"
                      class="w-full rounded-lg border border-slate-300 px-4 py-3">{{ old('findings', $survey->findings) }}</textarea>
        </div>

        <div>
            <label for="notes" class="mb-2 block text-sm font-semibold text-slate-700">
                Catatan Tambahan
            </label>
            <textarea id="notes" name="notes" rows="3"
                      class="w-full rounded-lg border border-slate-300 px-4 py-3">{{ old('notes', $survey->notes) }}</textarea>
        </div>

        <div class="flex justify-end border-t border-slate-200 pt-5">
            <button type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
