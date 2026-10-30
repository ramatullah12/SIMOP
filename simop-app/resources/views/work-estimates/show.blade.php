@extends('layouts.app')

@section('title', 'Detail Work Estimate | SIMOP')
@section('page-title', 'Detail Work Estimate')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;
        $status = $estimate->status;

        $statusStyles = [
            'draft' => 'bg-slate-100 text-slate-700',
            'pending' => 'bg-amber-100 text-amber-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
        ];

        $statusLabels = [
            'draft' => 'Draft',
            'pending' => 'Menunggu Approval',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];

        $approvalStyles = [
            'pending' => 'bg-amber-100 text-amber-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
        ];

        $approvalLabels = [
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];

        $approval = $estimate->approvals
            ->where('stage', 'direktur')
            ->sortByDesc('created_at')
            ->first();

        $survey = $estimate->survey;
        $order = $estimate->order;
        $project = $estimate->project;
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Detail Work Estimate
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Nomor WE:
                    <span class="font-semibold text-slate-700">
                        {{ $estimate->estimate_number }}
                    </span>
                </p>
            </div>

            <a
                href="{{ route('admin.work-estimates.index') }}"
                class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
            >
                &larr; Kembali ke Daftar
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
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

        @if ($status === 'draft')
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                <p class="font-semibold text-slate-800">
                    Work Estimate Masih Draft
                </p>

                <p class="mt-1 text-sm leading-6 text-slate-600">
                    Work Estimate belum diajukan kepada Direktur untuk proses pemeriksaan dan persetujuan.
                </p>
            </div>
        @elseif ($status === 'pending')
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                <p class="font-semibold text-amber-800">
                    Menunggu Persetujuan Direktur
                </p>

                <p class="mt-1 text-sm leading-6 text-amber-700">
                    Work Estimate telah diajukan dan sedang menunggu keputusan Direktur.
                </p>
            </div>
        @elseif ($status === 'approved')
            <div class="rounded-xl border border-green-200 bg-green-50 p-5">
                <p class="font-semibold text-green-800">
                    Work Estimate Disetujui
                </p>

                <p class="mt-1 text-sm leading-6 text-green-700">
                    Work Estimate telah mendapatkan persetujuan final dari Direktur dan dapat dilanjutkan ke tahap Project.
                </p>
            </div>
        @elseif ($status === 'rejected')
            <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                <p class="font-semibold text-red-800">
                    Work Estimate Ditolak
                </p>

                <p class="mt-1 text-sm leading-6 text-red-700">
                    Work Estimate ditolak oleh Direktur. Lihat catatan approval untuk mengetahui hasil evaluasi.
                </p>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
                <h2 class="mb-5 text-lg font-bold text-slate-900">
                    Informasi Work Estimate
                </h2>

                <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-slate-500">
                            Nomor Work Estimate
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
                            {{ $order?->order_number ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Client / Perusahaan
                        </dt>

                        <dd class="mt-1 font-medium text-slate-900">
                            {{ $order?->company_name ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Dibuat Oleh
                        </dt>

                        <dd class="mt-1 font-medium text-slate-900">
                            {{ $estimate->creator?->name ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Dibuat Pada
                        </dt>

                        <dd class="mt-1 font-medium text-slate-900">
                            {{ $estimate->created_at?->format('d/m/Y H:i') ?? '-' }}
                        </dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-sm text-slate-500">
                            Deskripsi Pekerjaan
                        </dt>

                        <dd class="mt-1 whitespace-pre-line text-slate-700">
                            {{ $estimate->description ?: 'Tidak ada deskripsi.' }}
                        </dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-sm text-slate-500">
                            Catatan Work Estimate
                        </dt>

                        <dd class="mt-1 whitespace-pre-line text-slate-700">
                            {{ $estimate->notes ?: 'Tidak ada catatan.' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-900">
                        Status
                    </h2>

                    <div class="mt-4">
                        <span class="inline-flex rounded-full px-3 py-1.5 text-sm font-semibold {{ $statusStyles[$status] ?? 'bg-slate-100 text-slate-700' }}">
                            {{ $statusLabels[$status] ?? ucfirst($status) }}
                        </span>
                    </div>

                    <p class="mt-4 text-sm leading-6 text-slate-500">
                        @if ($status === 'draft')
                            Belum diajukan untuk mendapatkan persetujuan Direktur.
                        @elseif ($status === 'pending')
                            Sedang menunggu keputusan Direktur.
                        @elseif ($status === 'approved')
                            Telah mendapatkan persetujuan final Direktur.
                        @elseif ($status === 'rejected')
                            Ditolak oleh Direktur.
                        @endif
                    </p>
                </div>

                @if ($project)
                    <div class="rounded-xl border border-green-200 bg-green-50 p-6">
                        <p class="text-sm font-semibold text-green-800">
                            Project Sudah Dibuat
                        </p>

                        <p class="mt-2 text-sm text-green-700">
                            {{ $project->project_number }}
                        </p>

                        <p class="mt-1 font-semibold text-green-900">
                            {{ $project->title }}
                        </p>

                        <a
                            href="{{ route('projects.show', $project) }}"
                            class="mt-4 inline-flex text-sm font-semibold text-green-800 underline hover:text-green-900"
                        >
                            Lihat Project
                        </a>
                    </div>
                @elseif ($status === 'approved')
                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-6">
                        <p class="text-sm font-semibold text-blue-800">
                            Siap Menjadi Project
                        </p>

                        <p class="mt-2 text-sm leading-6 text-blue-700">
                            Work Estimate telah disetujui dan belum memiliki Project.
                        </p>

                        @if ($role === 'admin')
                            <a
                                href="{{ route('projects.create') }}"
                                class="mt-4 inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                            >
                                Buat Project
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @if ($survey)
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">
                            Survey Terkait
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Survey yang menjadi dasar penyusunan Work Estimate.
                        </p>
                    </div>

                    <span class="inline-flex w-fit rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                        {{ ucfirst($survey->status) }}
                    </span>
                </div>

                <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <div>
                        <dt class="text-sm text-slate-500">
                            Lokasi
                        </dt>

                        <dd class="mt-1 font-medium text-slate-900">
                            {{ $survey->location }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Jadwal Survey
                        </dt>

                        <dd class="mt-1 font-medium text-slate-900">
                            {{ $survey->scheduled_at?->format('d/m/Y H:i') ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Petugas Lapangan
                        </dt>

                        <dd class="mt-1 font-medium text-slate-900">
                            {{ $survey->assignedUser?->name ?? '-' }}
                        </dd>
                    </div>

                    <div class="sm:col-span-3">
                        <dt class="text-sm text-slate-500">
                            Hasil / Temuan Survey
                        </dt>

                        <dd class="mt-1 whitespace-pre-line text-slate-700">
                            {{ $survey->findings ?: 'Tidak ada hasil Survey.' }}
                        </dd>
                    </div>

                    @if ($survey->notes)
                        <div class="sm:col-span-3">
                            <dt class="text-sm text-slate-500">
                                Catatan Survey
                            </dt>

                            <dd class="mt-1 whitespace-pre-line text-slate-700">
                                {{ $survey->notes }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>
        @endif

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <h2 class="text-lg font-bold text-slate-900">
                    Rincian Biaya
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Daftar item yang membentuk nilai Work Estimate.
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
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 text-slate-600">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-5 py-4 font-medium text-slate-900">
                                    {{ $item->item_name }}
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    {{ $item->description ?: '-' }}
                                </td>

                                <td class="px-5 py-4 text-right text-slate-700">
                                    {{ number_format(
                                        (float) $item->quantity,
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                                <td class="px-5 py-4 text-slate-700">
                                    {{ $item->unit }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right text-slate-700">
                                    Rp {{ number_format(
                                        (float) $item->unit_price,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-slate-900">
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
                                <td
                                    colspan="7"
                                    class="px-5 py-10 text-center text-slate-500"
                                >
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
                            Total Work Estimate
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

        @if ($role === 'admin' && $status === 'draft')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-6">
                <h2 class="text-lg font-bold text-slate-900">
                    Ajukan Approval Direktur
                </h2>

                <p class="mt-1 text-sm leading-6 text-slate-600">
                    Setelah diajukan, Work Estimate akan masuk ke daftar Approval Direktur. Pastikan seluruh informasi dan rincian biaya sudah benar.
                </p>

                <form
                    method="POST"
                    action="{{ route('approvals.store', $estimate) }}"
                    class="mt-5 space-y-4"
                >
                    @csrf

                    <div>
                        <label
                            for="approval_notes"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Catatan Pengajuan
                        </label>

                        <textarea
                            id="approval_notes"
                            name="notes"
                            rows="4"
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            placeholder="Tambahkan informasi untuk Direktur jika diperlukan..."
                        >{{ old('notes') }}</textarea>
                    </div>

                    <button
                        type="submit"
                        onclick="return confirm('Ajukan Work Estimate ini kepada Direktur?')"
                        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        Ajukan ke Direktur
                    </button>
                </form>
            </div>
        @endif

        @if ($role === 'direktur' && $status === 'draft')
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                <p class="text-sm font-semibold text-slate-800">
                    Belum Diajukan
                </p>

                <p class="mt-1 text-sm leading-6 text-slate-600">
                    Work Estimate ini masih Draft dan belum diajukan oleh Admin untuk proses approval.
                </p>
            </div>
        @endif

        @if ($role === 'direktur' && $status === 'pending')
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-6">
                <h2 class="text-lg font-bold text-amber-900">
                    Work Estimate Menunggu Keputusan
                </h2>

                <p class="mt-2 text-sm leading-6 text-amber-700">
                    Work Estimate ini sedang menunggu keputusan Direktur. Proses persetujuan atau penolakan dilakukan melalui modul Approval.
                </p>

                <a
                    href="{{ route('approvals.index') }}"
                    class="mt-4 inline-flex items-center justify-center rounded-lg bg-amber-600 px-5 py-3 text-sm font-semibold text-white hover:bg-amber-700"
                >
                    Buka Modul Approval
                </a>
            </div>
        @endif

        @if ($approval)
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">
                            Approval Direktur
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Riwayat keputusan final Work Estimate.
                        </p>
                    </div>

                    <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $approvalStyles[$approval->status] ?? 'bg-slate-100 text-slate-700' }}">
                        {{ $approvalLabels[$approval->status] ?? ucfirst($approval->status) }}
                    </span>
                </div>

                <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-slate-500">
                            Tahap Approval
                        </dt>

                        <dd class="mt-1 font-semibold text-slate-900">
                            Direktur
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Status
                        </dt>

                        <dd class="mt-1 font-semibold text-slate-900">
                            {{ $approvalLabels[$approval->status] ?? ucfirst($approval->status) }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Diproses Oleh
                        </dt>

                        <dd class="mt-1 font-semibold text-slate-900">
                            {{ $approval->approver?->name ?? 'Belum diproses' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Waktu Keputusan
                        </dt>

                        <dd class="mt-1 text-slate-700">
                            {{ $approval->approved_at?->format('d/m/Y H:i') ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Diajukan Pada
                        </dt>

                        <dd class="mt-1 text-slate-700">
                            {{ $approval->created_at?->format('d/m/Y H:i') ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">
                            Approver
                        </dt>

                        <dd class="mt-1 text-slate-700">
                            {{ $approval->approver?->name ?? '-' }}
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
            </div>
        @elseif ($status === 'pending')
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                <p class="text-sm font-semibold text-amber-800">
                    Data Approval Belum Ditemukan
                </p>

                <p class="mt-1 text-sm text-amber-700">
                    Work Estimate berstatus Pending, tetapi data Approval Direktur belum tersedia.
                </p>
            </div>
        @endif
    </div>
@endsection