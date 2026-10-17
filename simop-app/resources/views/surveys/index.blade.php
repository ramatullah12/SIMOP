
@extends('layouts.app')

@section('title', 'Survey | SIMOP')
@section('page-title', 'Manajemen Survey')

@section('content')
<div class="space-y-6">

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

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Daftar Survey</h2>
            <p class="mt-1 text-sm text-slate-500">
                Kelola jadwal dan pelaksanaan survey Order & RFQ.
            </p>
        </div>

        <a href="{{ route('admin.surveys.create') }}"
           class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">
            + Tambah Survey
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">No.</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">Order</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">Lokasi</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">Jadwal</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">Petugas</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">Status</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($surveys as $survey)
                        <tr class="hover:bg-slate-50">
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                {{ $surveys->firstItem() + $loop->index }}
                            </td>

                            <td class="px-5 py-4 text-sm font-medium text-slate-900">
                                {{ $survey->order->order_number ?? 'Order #'.$survey->order_id }}
                            </td>

                            <td class="px-5 py-4 text-sm text-slate-600">
                                {{ $survey->location }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                {{ $survey->scheduled_at?->format('d-m-Y H:i') ?? 'Belum dijadwalkan' }}
                            </td>

                            <td class="px-5 py-4 text-sm text-slate-600">
                                {{ $survey->assignedUser->name ?? 'Belum ditugaskan' }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm">
                                @php
                                    $statusColors = [
                                        'dijadwalkan' => 'bg-blue-100 text-blue-700',
                                        'berlangsung' => 'bg-amber-100 text-amber-700',
                                        'selesai' => 'bg-green-100 text-green-700',
                                        'dibatalkan' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp

                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusColors[$survey->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ ucfirst($survey->status) }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm">
                                <a href="{{ route('admin.surveys.show', $survey) }}"
                                   class="font-semibold text-blue-600 hover:text-blue-800">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <p class="font-semibold text-slate-700">Belum ada data Survey</p>
                                <p class="mt-1 text-sm text-slate-500">
                                    Klik tombol Tambah Survey untuk membuat jadwal pertama.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($surveys->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">
                {{ $surveys->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
