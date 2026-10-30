@extends('layouts.app')

@section('title', 'Survey | SIMOP')
@section('page-title', 'Survey')

@section('content')
    @php
        $role = auth()->user()?->role?->slug;

        $statusLabels = [
            'dijadwalkan' => 'Dijadwalkan',
            'berlangsung' => 'Berlangsung',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];

        $statusColors = [
            'dijadwalkan' => 'bg-amber-100 text-amber-700',
            'berlangsung' => 'bg-blue-100 text-blue-700',
            'selesai' => 'bg-green-100 text-green-700',
            'dibatalkan' => 'bg-red-100 text-red-700',
        ];
    @endphp

    <div class="space-y-6">
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

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">
                    @if ($role === 'petugas-lapangan')
                        Survey Saya
                    @else
                        Daftar Survey
                    @endif
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    @if ($role === 'admin')
                        Kelola jadwal, penugasan, dan pelaksanaan Survey Order & RFQ.
                    @elseif ($role === 'direktur')
                        Pantau jadwal, pelaksanaan, dan hasil Survey Order & RFQ.
                    @elseif ($role === 'petugas-lapangan')
                        Lihat dan kerjakan Survey yang ditugaskan kepada Anda.
                    @endif
                </p>
            </div>

            @if ($role === 'admin')
                <a
                    href="{{ route('admin.surveys.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                    + Tambah Survey
                </a>
            @endif
        </div>

        @if ($role === 'direktur')
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                <p class="text-sm font-semibold text-blue-800">
                    Mode Monitoring
                </p>

                <p class="mt-1 text-sm text-blue-700">
                    Anda dapat melihat seluruh Survey dan hasilnya, tetapi tidak dapat mengubah data Survey.
                </p>
            </div>
        @elseif ($role === 'petugas-lapangan')
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm font-semibold text-slate-800">
                    Tugas Survey Lapangan
                </p>

                <p class="mt-1 text-sm text-slate-500">
                    Daftar ini hanya menampilkan Survey yang ditugaskan kepada Anda.
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
                                Order
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Lokasi
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Jadwal
                            </th>

                            @if ($role !== 'petugas-lapangan')
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Petugas
                                </th>
                            @endif

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Status
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($surveys as $survey)
                            @php
                                $statusLabel = $statusLabels[$survey->status]
                                    ?? ucfirst($survey->status);

                                $statusClass = $statusColors[$survey->status]
                                    ?? 'bg-slate-100 text-slate-700';
                            @endphp

                            <tr class="transition hover:bg-slate-50">
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                    {{ $surveys->firstItem() + $loop->index }}
                                </td>

                                <td class="px-5 py-4">
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ $survey->order?->order_number ?? 'Order #'.$survey->order_id }}
                                    </p>

                                    @if ($survey->order?->event_name)
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $survey->order->event_name }}
                                        </p>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-sm text-slate-600">
                                    {{ $survey->location }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    @if ($survey->scheduled_at)
                                        <p class="text-sm font-medium text-slate-700">
                                            {{ $survey->scheduled_at->format('d/m/Y') }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $survey->scheduled_at->format('H:i') }} WIB
                                        </p>
                                    @else
                                        <span class="text-sm text-slate-400">
                                            Belum dijadwalkan
                                        </span>
                                    @endif
                                </td>

                                @if ($role !== 'petugas-lapangan')
                                    <td class="px-5 py-4">
                                        @if ($survey->assignedUser)
                                            <p class="text-sm font-medium text-slate-700">
                                                {{ $survey->assignedUser->name }}
                                            </p>
                                        @else
                                            <span class="text-sm text-amber-600">
                                                Belum ditugaskan
                                            </span>
                                        @endif
                                    </td>
                                @endif

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <a
                                        href="{{ route('admin.surveys.show', $survey) }}"
                                        class="text-sm font-semibold text-blue-600 hover:text-blue-800"
                                    >
                                        @if (
                                            $role === 'petugas-lapangan'
                                            && ! in_array(
                                                $survey->status,
                                                ['selesai', 'dibatalkan'],
                                                true
                                            )
                                        )
                                            Kerjakan
                                        @else
                                            Detail
                                        @endif
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="{{ $role === 'petugas-lapangan' ? 6 : 7 }}"
                                    class="px-5 py-14 text-center"
                                >
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-500">
                                        ◎
                                    </div>

                                    <p class="mt-4 font-semibold text-slate-700">
                                        @if ($role === 'petugas-lapangan')
                                            Belum Ada Tugas Survey
                                        @else
                                            Belum Ada Data Survey
                                        @endif
                                    </p>

                                    <p class="mx-auto mt-1 max-w-md text-sm leading-6 text-slate-500">
                                        @if ($role === 'admin')
                                            Belum ada Survey yang dibuat. Tambahkan Survey untuk Order & RFQ yang membutuhkan proses Survey.
                                        @elseif ($role === 'direktur')
                                            Belum ada Survey yang tersedia untuk dipantau.
                                        @elseif ($role === 'petugas-lapangan')
                                            Belum ada Survey yang ditugaskan kepada Anda.
                                        @endif
                                    </p>

                                    @if ($role === 'admin')
                                        <a
                                            href="{{ route('admin.surveys.create') }}"
                                            class="mt-5 inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                                        >
                                            + Tambah Survey
                                        </a>
                                    @endif
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