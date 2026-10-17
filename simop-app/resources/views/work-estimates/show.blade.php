
@extends('layouts.app')

@section('title', 'Detail Work Estimate | SIMOP')
@section('page-title', 'Detail Estimasi Biaya')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">

    <div>
        <a href="{{ route('admin.work-estimates.index') }}"
           class="text-sm font-medium text-blue-600 hover:text-blue-800">
            &larr; Kembali ke Daftar Estimasi
        </a>

        <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">
                    {{ $estimate->title }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Nomor: {{ $estimate->estimate_number }}
                </p>
            </div>

            <span class="w-fit rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                {{ ucfirst(str_replace('_', ' ', $estimate->status)) }}
            </span>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-bold text-slate-900">Informasi Estimasi</h3>

        <dl class="mt-5 grid gap-5 sm:grid-cols-2">
            <div>
                <dt class="text-sm text-slate-500">Nomor Estimasi</dt>
                <dd class="mt-1 font-semibold text-slate-900">
                    {{ $estimate->estimate_number }}
                </dd>
            </div>

            <div>
                <dt class="text-sm text-slate-500">Nomor Order</dt>
                <dd class="mt-1 font-semibold text-slate-900">
                    {{ $estimate->order->order_number ?? 'Order #'.$estimate->order_id }}
                </dd>
            </div>

            <div>
                <dt class="text-sm text-slate-500">Perusahaan</dt>
                <dd class="mt-1 font-semibold text-slate-900">
                    {{ $estimate->order->company_name ?? '-' }}
                </dd>
            </div>

            <div>
                <dt class="text-sm text-slate-500">Survey Terkait</dt>
                <dd class="mt-1 font-semibold text-slate-900">
                    {{ $estimate->survey?->location ?? 'Tidak ada' }}
                </dd>
            </div>

            <div>
                <dt class="text-sm text-slate-500">Dibuat Oleh</dt>
                <dd class="mt-1 font-semibold text-slate-900">
                    {{ $estimate->creator?->name ?? '-' }}
                </dd>
            </div>

            <div>
                <dt class="text-sm text-slate-500">Tanggal Dibuat</dt>
                <dd class="mt-1 font-semibold text-slate-900">
                    {{ $estimate->created_at?->format('d-m-Y H:i') ?? '-' }}
                </dd>
            </div>

            <div class="sm:col-span-2">
                <dt class="text-sm text-slate-500">Deskripsi Pekerjaan</dt>
                <dd class="mt-1 whitespace-pre-line text-slate-800">
                    {{ $estimate->description ?: '-' }}
                </dd>
            </div>
        </dl>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-6">
            <h3 class="text-lg font-bold text-slate-900">Rincian Item Pekerjaan</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">No.</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">Item Pekerjaan</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase text-slate-500">Kuantitas</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase text-slate-500">Harga Satuan</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase text-slate-500">Total</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($estimate->items as $item)
                        <tr>
                            <td class="px-5 py-4 text-sm text-slate-600">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900">{{ $item->item_name }}</p>
                                @if ($item->description)
                                    <p class="mt-1 text-sm text-slate-500">{{ $item->description }}</p>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm text-slate-600">
                                {{ number_format((float) $item->quantity, 2, ',', '.') }}
                                {{ $item->unit }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm text-slate-600">
                                Rp{{ number_format((float) $item->unit_price, 0, ',', '.') }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-semibold text-slate-900">
                                Rp{{ number_format((float) $item->total_price, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-sm text-slate-500">
                                Belum ada rincian item.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 bg-slate-50 p-6">
            <div class="ml-auto max-w-sm space-y-3">
                <div class="flex justify-between gap-4 text-sm">
                    <span class="text-slate-600">Subtotal</span>
                    <span class="font-semibold text-slate-900">
                        Rp{{ number_format((float) $estimate->subtotal, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex justify-between gap-4 text-sm">
                    <span class="text-slate-600">Pajak</span>
                    <span class="font-semibold text-slate-900">
                        Rp{{ number_format((float) $estimate->tax_amount, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex justify-between gap-4 border-t border-slate-300 pt-4">
                    <span class="font-bold text-slate-900">Total Estimasi</span>
                    <span class="text-xl font-bold text-blue-700">
                        Rp{{ number_format((float) $estimate->total_amount, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-bold text-slate-900">Catatan Tambahan</h3>
        <p class="mt-3 whitespace-pre-line text-sm text-slate-600">
            {{ $estimate->notes ?: 'Tidak ada catatan tambahan.' }}
        </p>
    </div>

</div>
@endsection
