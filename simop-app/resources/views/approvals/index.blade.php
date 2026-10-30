@extends('layouts.app')

@section('title', 'Approval | SIMOP')
@section('page-title', 'Approval Work Estimate')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;

        $statusLabels = [
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];

        $statusColors = [
            'pending' => 'bg-amber-100 text-amber-700',
            'approved' => 'bg-green-100 text-green-700',
            'rejected' => 'bg-red-100 text-red-700',
        ];
    @endphp

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">
                {{ session('error') }}
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

        <div>
            <h2 class="text-xl font-bold text-slate-900">
                Daftar Persetujuan Work Estimate
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                @if ($role === 'direktur')
                    Periksa Work Estimate yang diajukan Admin dan berikan keputusan persetujuan final.
                @else
                    Pantau proses persetujuan Work Estimate yang telah diajukan kepada Direktur.
                @endif
            </p>
        </div>

        @if ($role === 'direktur')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                <p class="text-sm font-semibold text-blue-800">
                    Persetujuan Final Direktur
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Periksa rincian Work Estimate terlebih dahulu sebelum memberikan keputusan.
                    Work Estimate yang disetujui dapat dilanjutkan ke tahap Project.
                </p>
            </div>
        @elseif ($role === 'admin')
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-sm font-semibold text-slate-800">
                    Monitoring Approval
                </p>

                <p class="mt-1 text-sm leading-6 text-slate-600">
                    Admin dapat memantau status pengajuan Work Estimate.
                    Keputusan persetujuan hanya dapat dilakukan oleh Direktur.
                </p>
            </div>
        @endif

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                No.
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Work Estimate
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Order
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Total
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Tahap
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Status
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($approvals as $approval)
                            @php
                                $estimate = $approval->workEstimate;

                                $canDecide =
                                    $role === 'direktur'
                                    && $approval->stage === 'direktur'
                                    && $approval->status === 'pending'
                                    && $estimate?->status === 'pending';

                                $statusLabel = $statusLabels[$approval->status]
                                    ?? ucfirst(str_replace('_', ' ', $approval->status));

                                $statusClass = $statusColors[$approval->status]
                                    ?? 'bg-slate-100 text-slate-700';
                            @endphp

                            <tr class="align-top transition hover:bg-slate-50">
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                    {{ $approvals->firstItem() + $loop->index }}
                                </td>

                                <td class="min-w-56 px-5 py-4">
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ $estimate?->estimate_number ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-sm text-slate-600">
                                        {{ $estimate?->title ?? '-' }}
                                    </p>

                                    @if ($estimate)
                                        <a
                                            href="{{ route('admin.work-estimates.show', $estimate) }}"
                                            class="mt-2 inline-flex text-xs font-semibold text-blue-600 hover:text-blue-800"
                                        >
                                            Lihat Detail Work Estimate
                                        </a>
                                    @endif
                                </td>

                                <td class="min-w-48 px-5 py-4">
                                    <p class="text-sm font-medium text-slate-800">
                                        {{ $estimate?->order?->order_number ?? '-' }}
                                    </p>

                                    @if ($estimate?->order?->company_name)
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $estimate->order->company_name }}
                                        </p>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <p class="text-sm font-bold text-slate-900">
                                        Rp{{ number_format(
                                            (float) ($estimate?->total_amount ?? 0),
                                            0,
                                            ',',
                                            '.'
                                        ) }}
                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                        Direktur
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>

                                    @if (
                                        $approval->status !== 'pending'
                                        && $approval->approved_at
                                    )
                                        <p class="mt-2 text-xs text-slate-500">
                                            {{ $approval->approved_at->format('d/m/Y H:i') }}
                                        </p>
                                    @endif
                                </td>

                                <td class="min-w-72 px-5 py-4">
                                    @if ($canDecide)
                                        <form
                                            method="POST"
                                            action="{{ route('approvals.decide', $approval) }}"
                                            class="space-y-3"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <div>
                                                <label
                                                    for="notes-{{ $approval->id }}"
                                                    class="mb-1 block text-xs font-semibold text-slate-600"
                                                >
                                                    Catatan Keputusan
                                                </label>

                                                <textarea
                                                    id="notes-{{ $approval->id }}"
                                                    name="notes"
                                                    rows="3"
                                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                                    placeholder="Tambahkan catatan jika diperlukan..."
                                                >{{ old('notes') }}</textarea>
                                            </div>

                                            <div class="flex flex-wrap gap-2">
                                                <button
                                                    type="submit"
                                                    name="status"
                                                    value="approved"
                                                    onclick="return confirm('Setujui Work Estimate ini? Setelah disetujui, Work Estimate dapat dilanjutkan ke tahap Project.')"
                                                    class="rounded-lg bg-green-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-green-700"
                                                >
                                                    Setujui
                                                </button>

                                                <button
                                                    type="submit"
                                                    name="status"
                                                    value="rejected"
                                                    onclick="return confirm('Tolak Work Estimate ini?')"
                                                    class="rounded-lg bg-red-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-red-700"
                                                >
                                                    Tolak
                                                </button>
                                            </div>
                                        </form>
                                    @elseif ($approval->status === 'pending')
                                        <div class="rounded-lg bg-amber-50 p-3">
                                            <p class="text-sm font-medium text-amber-700">
                                                Menunggu keputusan Direktur
                                            </p>
                                        </div>
                                    @else
                                        <div class="space-y-2">
                                            <div>
                                                <p class="text-xs text-slate-500">
                                                    Diproses oleh
                                                </p>

                                                <p class="mt-1 text-sm font-semibold text-slate-800">
                                                    {{ $approval->approver?->name ?? '-' }}
                                                </p>
                                            </div>

                                            @if ($approval->notes)
                                                <div>
                                                    <p class="text-xs text-slate-500">
                                                        Catatan
                                                    </p>

                                                    <p class="mt-1 whitespace-pre-line text-sm text-slate-700">
                                                        {{ $approval->notes }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="7"
                                    class="px-5 py-14 text-center"
                                >
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-500">
                                        ✓
                                    </div>

                                    <p class="mt-4 font-semibold text-slate-700">
                                        Belum Ada Pengajuan Approval
                                    </p>

                                    <p class="mx-auto mt-1 max-w-md text-sm leading-6 text-slate-500">
                                        @if ($role === 'admin')
                                            Work Estimate berstatus Draft dapat diajukan melalui menu Work Estimate.
                                        @else
                                            Belum ada Work Estimate yang menunggu atau telah mendapatkan keputusan Direktur.
                                        @endif
                                    </p>

                                    @if ($role === 'admin')
                                        <a
                                            href="{{ route('admin.work-estimates.index') }}"
                                            class="mt-5 inline-flex rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                                        >
                                            Buka Work Estimate
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($approvals->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $approvals->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection