
@extends('layouts.app')

@section('title', 'Order & RFQ | SIMOP')
@section('page-title', 'Order & RFQ')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Order & RFQ</h1>
            <p class="mt-2 text-sm text-slate-500">
                Kelola permintaan penawaran dan order pekerjaan.
            </p>
        </div>

        @if ($role === 'client')
            <a href="{{ route('client.orders.create') }}"
               class="rounded-lg bg-blue-600 px-5 py-3 text-center text-sm font-semibold text-white hover:bg-blue-700">
                + Ajukan Order
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-4">Nomor Order</th>
                        @if ($role === 'admin')
                            <th class="px-5 py-4">Client</th>
                        @endif
                        <th class="px-5 py-4">Nama Event</th>
                        <th class="px-5 py-4">Tanggal</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-slate-50">
                            <td class="whitespace-nowrap px-5 py-4 font-semibold text-blue-700">
                                {{ $order->order_number }}
                            </td>
                            @if ($role === 'admin')
                                <td class="px-5 py-4">
                                    {{ $order->client->name ?? '-' }}
                                </td>
                            @endif
                            <td class="px-5 py-4">{{ $order->event_name }}</td>
                            <td class="whitespace-nowrap px-5 py-4">
                                {{ $order->event_date->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    $statusColors = [
                                        'baru' => 'bg-blue-100 text-blue-700',
                                        'diperiksa' => 'bg-amber-100 text-amber-700',
                                        'perlu_revisi' => 'bg-orange-100 text-orange-700',
                                        'ditolak' => 'bg-red-100 text-red-700',
                                        'menunggu_survey' => 'bg-green-100 text-green-700',
                                    ];
                                @endphp
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusColors[$order->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ str($order->status)->replace('_', ' ')->title() }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <a href="{{ route('orders.show', $order) }}"
                                   class="font-semibold text-blue-600 hover:text-blue-800">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $role === 'admin' ? 6 : 5 }}"
                                class="px-5 py-12 text-center text-slate-500">
                                Belum ada order yang tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-5 py-4">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
