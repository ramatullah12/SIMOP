@extends('layouts.app')

@section('title', 'Detail Work Estimate - SIMOP')
@section('page-title', 'Detail Work Estimate')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                Detail Work Estimate
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Nomor estimasi:
                <span class="font-semibold text-slate-700">
                    {{ $estimate->estimate_number }}
                </span>
            </p>
        </div>

        <a href="{{ route('admin.work-estimates.index') }}"
           class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Kembali ke Daftar
        </a>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <p class="mb-2 font-semibold">
                Ada kesalahan:
            </p>

            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="mb-5 text-lg font-bold text-slate-900">
                Informasi Estimasi
            </h2>

            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <dt class="text-sm text-slate-500">
                        Nomor Estimasi
                    </dt>

                    <dd class="mt-1 font-semibold text-slate-900">
                        {{ $estimate->estimate_number }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm text-slate-500">
                        Judul
                    </dt>

                    <dd class="mt-1 font-semibold text-slate-900">
                        {{ $estimate->title }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm text-slate-500">
                        Nomor Order
                    </dt>

                    <dd class="mt-1 font-medium text-slate-900">
                        {{ $estimate->order->order_number ?? '-' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm text-slate-500">
                        Client / Perusahaan
                    </dt>

                    <dd class="mt-1 font-medium text-slate-900">
                        {{ $estimate->order->company_name ?? '-' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm text-slate-500">
                        Survey
                    </dt>

                    <dd class="mt-1 font-medium text-slate-900">
                        {{ $estimate->survey->location ?? 'Belum dihubungkan' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm text-slate-500">
                        Dibuat oleh
                    </dt>

                    <dd class="mt-1 font-medium text-slate-900">
                        {{ $estimate->creator->name ?? '-' }}
                    </dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="text-sm text-slate-500">
                        Deskripsi
                    </dt>

                    <dd class="mt-1 whitespace-pre-line text-slate-700">
                        {{ $estimate->description ?: 'Tidak ada deskripsi.' }}
                    </dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="text-sm text-slate-500">
                        Catatan Estimasi
                    </dt>

                    <dd class="mt-1 whitespace-pre-line text-slate-700">
                        {{ $estimate->notes ?: 'Tidak ada catatan.' }}
                    </dd>
                </div>
            </dl>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-bold text-slate-900">
                Status Estimasi
            </h2>

            @php
                $statusStyles = [
                    'draft' => 'bg-slate-100 text-slate-700',
                    'pending' => 'bg-amber-100 text-amber-800',
                    'approved' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                ];

                $statusLabels = [
                    'draft' => 'Draft',
                    'pending' => 'Menunggu Persetujuan',
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak',
                ];

                $status = $estimate->status;
            @endphp

            <span class="inline-flex rounded-full px-3 py-1.5 text-sm font-semibold {{ $statusStyles[$status] ?? 'bg-slate-100 text-slate-700' }}">
                {{ $statusLabels[$status] ?? ucfirst($status) }}
            </span>

            <p class="mt-4 text-sm leading-6 text-slate-500">
                @if ($status === 'draft')
                    Estimasi belum diajukan untuk persetujuan.
                @elseif ($status === 'pending')
                    Estimasi sedang menunggu keputusan Direktur.
                @elseif ($status === 'approved')
                    Estimasi telah mendapatkan persetujuan final dari Direktur.
                @elseif ($status === 'rejected')
                    Estimasi ditolak oleh Direktur dan perlu ditinjau kembali.
                @endif
            </p>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-5">
            <h2 class="text-lg font-bold text-slate-900">
                Rincian Biaya
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Daftar item yang membentuk nilai estimasi.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-5 py-4">
                            No.
                        </th>

                        <th class="px-5 py-4">
                            Nama Item
                        </th>

                        <th class="px-5 py-4">
                            Deskripsi
                        </th>

                        <th class="px-5 py-4 text-right">
                            Kuantitas
                        </th>

                        <th class="px-5 py-4">
                            Satuan
                        </th>

                        <th class="px-5 py-4 text-right">
                            Harga Satuan
                        </th>

                        <th class="px-5 py-4 text-right">
                            Total
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($estimate->items as $item)
                        <tr>
                            <td class="px-5 py-4">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-5 py-4 font-medium text-slate-900">
                                {{ $item->item_name }}
                            </td>

                            <td class="px-5 py-4 text-slate-600">
                                {{ $item->description ?: '-' }}
                            </td>

                            <td class="px-5 py-4 text-right">
                                {{ number_format(
                                    (float) $item->quantity,
                                    2,
                                    ',',
                                    '.'
                                ) }}
                            </td>

                            <td class="px-5 py-4">
                                {{ $item->unit }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                Rp {{ number_format(
                                    (float) $item->unit_price,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-right font-medium">
                                Rp {{ number_format(
                                    (float) $item->total_price,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7"
                                class="px-5 py-8 text-center text-slate-500">
                                Belum ada rincian item biaya.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex justify-end border-t border-slate-200 bg-slate-50 px-6 py-5">
            <dl class="w-full space-y-3 sm:max-w-sm">

                <div class="flex justify-between gap-4">
                    <dt class="text-slate-600">
                        Subtotal
                    </dt>

                    <dd class="font-medium text-slate-900">
                        Rp {{ number_format(
                            (float) $estimate->subtotal,
                            0,
                            ',',
                            '.'
                        ) }}
                    </dd>
                </div>

                <div class="flex justify-between gap-4">
                    <dt class="text-slate-600">
                        Pajak
                    </dt>

                    <dd class="font-medium text-slate-900">
                        Rp {{ number_format(
                            (float) $estimate->tax_amount,
                            0,
                            ',',
                            '.'
                        ) }}
                    </dd>
                </div>

                <div class="flex justify-between gap-4 border-t border-slate-200 pt-3">
                    <dt class="font-bold text-slate-900">
                        Total Estimasi
                    </dt>

                    <dd class="text-lg font-bold text-blue-700">
                        Rp {{ number_format(
                            (float) $estimate->total_amount,
                            0,
                            ',',
                            '.'
                        ) }}
                    </dd>
                </div>

            </dl>
        </div>
    </div>

    @if (
        auth()->user()?->role?->slug === 'admin'
        && $status === 'draft'
    )
        <div class="rounded-xl border border-blue-200 bg-blue-50 p-6">
            <h2 class="text-lg font-bold text-slate-900">
                Ajukan Persetujuan
            </h2>

            <p class="mt-1 text-sm leading-6 text-slate-600">
                Kirim estimasi ini untuk diperiksa dan mendapatkan persetujuan final dari Direktur.
                Pastikan seluruh rincian biaya sudah benar sebelum mengajukan.
            </p>

            <form method="POST"
                  action="{{ route('approvals.store', $estimate) }}"
                  class="mt-5 space-y-4">

                @csrf

                <div>
                    <label for="approval_notes"
                           class="mb-1 block text-sm font-medium text-slate-700">
                        Catatan Pengajuan (Opsional)
                    </label>

                    <textarea
                        id="approval_notes"
                        name="notes"
                        rows="3"
                        class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Tambahkan informasi untuk Direktur..."
                    >{{ old('notes') }}</textarea>
                </div>

                <button type="submit"
                        onclick="return confirm('Ajukan estimasi ini kepada Direktur untuk persetujuan?')"
                        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                    Ajukan Persetujuan
                </button>

            </form>
        </div>
    @endif

    @if (in_array($status, ['pending', 'approved', 'rejected'], true))
        @php
            $approval = $estimate->approvals
                ->where('stage', 'direktur')
                ->sortByDesc('created_at')
                ->first();
        @endphp

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">
                Informasi Persetujuan
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Persetujuan Work Estimate diproses oleh Direktur sebagai persetujuan final.
            </p>

            @if ($approval)
                <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">

                    <div>
                        <dt class="text-sm text-slate-500">
                            Tahap Persetujuan
                        </dt>

                        <dd class="mt-1 font-semibold text-slate-900">
                            Direktur
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Status Approval
                        </dt>

                        <dd class="mt-1 font-semibold text-slate-900">
                            @if ($approval->status === 'pending')
                                Menunggu Persetujuan
                            @elseif ($approval->status === 'approved')
                                Disetujui
                            @elseif ($approval->status === 'rejected')
                                Ditolak
                            @else
                                {{ ucfirst($approval->status) }}
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Pemberi Persetujuan
                        </dt>

                        <dd class="mt-1 font-semibold text-slate-900">
                            {{ $approval->approver->name ?? 'Belum diproses' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Waktu Keputusan
                        </dt>

                        <dd class="mt-1 text-slate-700">
                            {{ $approval->approved_at?->format('d-m-Y H:i') ?? '-' }}
                        </dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-sm text-slate-500">
                            Catatan Approval
                        </dt>

                        <dd class="mt-1 whitespace-pre-line text-slate-700">
                            {{ $approval->notes ?: 'Tidak ada catatan.' }}
                        </dd>
                    </div>

                </dl>
            @else
                <p class="mt-4 text-sm text-slate-500">
                    Data persetujuan Direktur belum tersedia.
                </p>
            @endif
        </div>
    @endif

</div>
@endsection