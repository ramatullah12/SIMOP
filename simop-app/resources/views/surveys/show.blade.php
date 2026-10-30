@extends('layouts.app')

@section('title', 'Detail Survey | SIMOP')
@section('page-title', 'Detail Survey')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;

        $statusLabel = match ($survey->status) {
            'dijadwalkan' => 'Dijadwalkan',
            'berlangsung' => 'Berlangsung',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => ucfirst($survey->status),
        };

        $statusClass = match ($survey->status) {
            'dijadwalkan' => 'bg-amber-100 text-amber-700',
            'berlangsung' => 'bg-blue-100 text-blue-700',
            'selesai' => 'bg-green-100 text-green-700',
            'dibatalkan' => 'bg-red-100 text-red-700',
            default => 'bg-slate-100 text-slate-700',
        };

        $canAdminUpdate = $role === 'admin';

        $canOfficerUpdate = $role === 'petugas-lapangan'
            && $survey->assigned_to === auth()->id()
            && ! in_array(
                $survey->status,
                ['selesai', 'dibatalkan'],
                true
            );
    @endphp

    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <a
                    href="{{ route('admin.surveys.index') }}"
                    class="text-sm font-medium text-blue-600 hover:text-blue-800"
                >
                    &larr; Kembali ke Daftar Survey
                </a>

                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <h2 class="text-2xl font-bold text-slate-900">
                        Detail Survey
                    </h2>

                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                </div>

                <p class="mt-1 text-sm text-slate-500">
                    Informasi Survey untuk Order
                    {{ $survey->order?->order_number ?? '#'.$survey->order_id }}
                </p>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-700">
                {{ session('success') }}
            </div>
        @endif

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

        @if ($survey->status === 'selesai')
            <div class="rounded-xl border border-green-200 bg-green-50 p-5">
                <div class="flex gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-100 font-bold text-green-700">
                        ✓
                    </div>

                    <div>
                        <p class="font-semibold text-green-800">
                            Survey Telah Selesai
                        </p>

                        <p class="mt-1 text-sm leading-6 text-green-700">
                            Hasil Survey telah diselesaikan dan dapat digunakan untuk proses Work Estimate.
                        </p>
                    </div>
                </div>
            </div>
        @elseif ($survey->status === 'dibatalkan')
            <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                <div class="flex gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 font-bold text-red-700">
                        ×
                    </div>

                    <div>
                        <p class="font-semibold text-red-800">
                            Survey Dibatalkan
                        </p>

                        <p class="mt-1 text-sm leading-6 text-red-700">
                            Survey ini telah dibatalkan.
                        </p>
                    </div>
                </div>
            </div>
        @elseif ($survey->status === 'berlangsung')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <div class="flex gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-700">
                        •
                    </div>

                    <div>
                        <p class="font-semibold text-blue-800">
                            Survey Sedang Berlangsung
                        </p>

                        <p class="mt-1 text-sm leading-6 text-blue-700">
                            Petugas Lapangan sedang menjalankan proses Survey.
                        </p>
                    </div>
                </div>
            </div>
        @elseif ($survey->status === 'dijadwalkan')
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                <div class="flex gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 font-bold text-amber-700">
                        !
                    </div>

                    <div>
                        <p class="font-semibold text-amber-800">
                            Survey Dijadwalkan
                        </p>

                        <p class="mt-1 text-sm leading-6 text-amber-700">
                            Survey telah dijadwalkan dan menunggu pelaksanaan oleh Petugas Lapangan.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-100 pb-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">
                        Informasi Survey
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Informasi Order, jadwal, lokasi, dan Petugas Lapangan.
                    </p>
                </div>

                <span class="w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                    {{ $statusLabel }}
                </span>
            </div>

            <dl class="mt-5 grid gap-5 sm:grid-cols-2">
                <div>
                    <dt class="text-sm text-slate-500">
                        Nomor Order
                    </dt>

                    <dd class="mt-1 font-semibold text-slate-900">
                        {{ $survey->order?->order_number ?? '-' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm text-slate-500">
                        Perusahaan
                    </dt>

                    <dd class="mt-1 font-semibold text-slate-900">
                        {{ $survey->order?->company_name ?? '-' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm text-slate-500">
                        Nama Event
                    </dt>

                    <dd class="mt-1 font-semibold text-slate-900">
                        {{ $survey->order?->event_name ?? '-' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm text-slate-500">
                        Tanggal Event
                    </dt>

                    <dd class="mt-1 font-semibold text-slate-900">
                        {{ $survey->order?->event_date?->format('d/m/Y') ?? '-' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm text-slate-500">
                        Lokasi Survey
                    </dt>

                    <dd class="mt-1 font-semibold text-slate-900">
                        {{ $survey->location }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm text-slate-500">
                        Petugas Lapangan
                    </dt>

                    <dd class="mt-1 font-semibold text-slate-900">
                        {{ $survey->assignedUser?->name ?? 'Belum ditugaskan' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm text-slate-500">
                        Jadwal Survey
                    </dt>

                    <dd class="mt-1 font-semibold text-slate-900">
                        {{ $survey->scheduled_at?->format('d/m/Y H:i') ?? 'Belum dijadwalkan' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm text-slate-500">
                        Status
                    </dt>

                    <dd class="mt-1">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900">
                Hasil Survey
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Hasil dan catatan dari pelaksanaan Survey lapangan.
            </p>

            <div class="mt-5 space-y-5">
                <div>
                    <p class="text-sm font-semibold text-slate-700">
                        Hasil atau Temuan Survey
                    </p>

                    <div class="mt-2 rounded-lg bg-slate-50 p-4 text-sm leading-6 text-slate-700">
                        @if ($survey->findings)
                            {!! nl2br(e($survey->findings)) !!}
                        @else
                            <span class="text-slate-400">
                                Belum ada hasil atau temuan Survey.
                            </span>
                        @endif
                    </div>
                </div>

                <div>
                    <p class="text-sm font-semibold text-slate-700">
                        Catatan Tambahan
                    </p>

                    <div class="mt-2 rounded-lg bg-slate-50 p-4 text-sm leading-6 text-slate-700">
                        @if ($survey->notes)
                            {!! nl2br(e($survey->notes)) !!}
                        @else
                            <span class="text-slate-400">
                                Tidak ada catatan tambahan.
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if ($canAdminUpdate)
            <form
                action="{{ route('admin.surveys.update', $survey) }}"
                method="POST"
                class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
            >
                @csrf
                @method('PUT')

                <div>
                    <h3 class="text-lg font-bold text-slate-900">
                        Perbarui Survey
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Atur Petugas Lapangan, jadwal, lokasi, status, dan hasil Survey.
                    </p>
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
                                @selected(old('assigned_to', $survey->assigned_to) == $officer->id)
                            >
                                {{ $officer->name }}
                            </option>
                        @endforeach
                    </select>
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
                        value="{{ old('scheduled_at', $survey->scheduled_at?->format('Y-m-d\TH:i')) }}"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                </div>

                <div>
                    <label
                        for="location"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Lokasi Survey
                    </label>

                    <input
                        id="location"
                        name="location"
                        type="text"
                        required
                        value="{{ old('location', $survey->location) }}"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                </div>

                <div>
                    <label
                        for="status"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Status Survey
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                        @foreach ([
                            'dijadwalkan' => 'Dijadwalkan',
                            'berlangsung' => 'Berlangsung',
                            'selesai' => 'Selesai',
                            'dibatalkan' => 'Dibatalkan',
                        ] as $value => $label)
                            <option
                                value="{{ $value }}"
                                @selected(old('status', $survey->status) === $value)
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label
                        for="findings"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Hasil atau Temuan Survey
                    </label>

                    <textarea
                        id="findings"
                        name="findings"
                        rows="5"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        placeholder="Masukkan hasil atau temuan Survey"
                    >{{ old('findings', $survey->findings) }}</textarea>

                    <p class="mt-2 text-xs text-slate-500">
                        Hasil atau temuan wajib diisi ketika Survey diselesaikan.
                    </p>
                </div>

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
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        placeholder="Masukkan catatan tambahan jika diperlukan"
                    >{{ old('notes', $survey->notes) }}</textarea>
                </div>

                <div class="flex justify-end border-t border-slate-200 pt-5">
                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        @elseif ($canOfficerUpdate)
            <form
                action="{{ route('admin.surveys.update', $survey) }}"
                method="POST"
                class="space-y-5 rounded-xl border border-blue-200 bg-white p-6 shadow-sm"
            >
                @csrf
                @method('PUT')

                <div>
                    <h3 class="text-lg font-bold text-slate-900">
                        Update Hasil Survey
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Perbarui status pelaksanaan dan masukkan hasil Survey lapangan.
                    </p>
                </div>

                <div>
                    <label
                        for="status"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Status Survey
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                        @if ($survey->status === 'dijadwalkan')
                            <option
                                value="berlangsung"
                                @selected(old('status') === 'berlangsung')
                            >
                                Mulai Survey
                            </option>

                            <option
                                value="selesai"
                                @selected(old('status') === 'selesai')
                            >
                                Selesaikan Survey
                            </option>
                        @elseif ($survey->status === 'berlangsung')
                            <option
                                value="berlangsung"
                                @selected(old('status', $survey->status) === 'berlangsung')
                            >
                                Berlangsung
                            </option>

                            <option
                                value="selesai"
                                @selected(old('status') === 'selesai')
                            >
                                Selesaikan Survey
                            </option>
                        @endif
                    </select>
                </div>

                <div>
                    <label
                        for="findings"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Hasil atau Temuan Survey
                    </label>

                    <textarea
                        id="findings"
                        name="findings"
                        rows="5"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        placeholder="Masukkan hasil atau temuan Survey di lapangan"
                    >{{ old('findings', $survey->findings) }}</textarea>

                    <p class="mt-2 text-xs text-slate-500">
                        Hasil atau temuan wajib diisi sebelum Survey diselesaikan.
                    </p>
                </div>

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
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        placeholder="Masukkan catatan tambahan jika diperlukan"
                    >{{ old('notes', $survey->notes) }}</textarea>
                </div>

                <div class="flex justify-end border-t border-slate-200 pt-5">
                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        Simpan Hasil Survey
                    </button>
                </div>
            </form>
        @elseif ($role === 'petugas-lapangan' && $survey->status === 'selesai')
            <div class="rounded-xl border border-green-200 bg-green-50 p-5">
                <p class="font-semibold text-green-800">
                    Survey Sudah Selesai
                </p>

                <p class="mt-1 text-sm leading-6 text-green-700">
                    Hasil Survey sudah disimpan dan tidak dapat diubah kembali oleh Petugas Lapangan.
                </p>
            </div>
        @elseif ($role === 'petugas-lapangan' && $survey->status === 'dibatalkan')
            <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                <p class="font-semibold text-red-800">
                    Survey Tidak Dapat Diperbarui
                </p>

                <p class="mt-1 text-sm leading-6 text-red-700">
                    Survey telah dibatalkan oleh Admin.
                </p>
            </div>
        @elseif ($role === 'direktur')
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                <p class="font-semibold text-slate-800">
                    Mode Monitoring
                </p>

                <p class="mt-1 text-sm leading-6 text-slate-600">
                    Direktur dapat melihat informasi dan hasil Survey, tetapi tidak dapat mengubah data Survey.
                </p>
            </div>
        @endif
    </div>
@endsection