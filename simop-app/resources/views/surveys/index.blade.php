<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    @if ($role === 'direktur')
                        Approval Survey & Penerbitan Surat Perintah Survey (SPS)
                    @elseif ($role === 'petugas-lapangan')
                        Surat Perintah Survey (SPS) Lapangan
                    @elseif ($role === 'client')
                        Pengajuan Survey Saya
                    @else
                        Manajemen Pengajuan Survey & Surat Perintah Survey (SPS)
                    @endif
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Daftar pengajuan survey lokasi dan penerbitan Surat Perintah Survey (SPS) untuk petugas lapangan.
                </p>
            </div>

            @if ($role === 'client')
                <a href="{{ route('surveys.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    + Ajukan Survey ke Admin
                </a>
            @elseif ($role === 'admin')
                <a href="{{ route('surveys.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    + Buat & Kirim Survey ke Direktur
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">No. SPS / Status Alur</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">No. Order & Event</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Client</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Petugas Lapangan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Jadwal & Lokasi</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Approval Direktur</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($surveys as $survey)
                                @php
                                    $statusBadge = match ($survey->status) {
                                        'diajukan_client' => ['bg-amber-100 text-amber-800', 'Diajukan Client (Di Admin)'],
                                        'menunggu_persetujuan_direktur' => ['bg-purple-100 text-purple-800', 'Menunggu Approval Direktur'],
                                        'ditolak_direktur' => ['bg-red-100 text-red-800', 'Ditolak / Revisi Direktur'],
                                        'dijadwalkan' => ['bg-blue-100 text-blue-800', 'SPS Terkirim ke Petugas'],
                                        'berlangsung' => ['bg-indigo-100 text-indigo-800', 'Survey Sedang Berlangsung'],
                                        'selesai' => ['bg-emerald-100 text-emerald-800', 'Survey Lapangan Selesai'],
                                        default => ['bg-gray-100 text-gray-700', strtoupper($survey->status)],
                                    };
                                @endphp
                                <tr>
                                    <td class="px-4 py-3">
                                        @if ($survey->sps_number)
                                            <p class="font-bold text-blue-700">{{ $survey->sps_number }}</p>
                                        @else
                                            <p class="text-xs text-gray-400 italic">SPS Belum Terbit</p>
                                        @endif
                                        <span class="mt-1 inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusBadge[0] }}">
                                            {{ $statusBadge[1] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-gray-900">{{ $survey->order?->order_number ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $survey->order?->event_name ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3">{{ $survey->order?->company_name ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        @if ($survey->assignedUser)
                                            <p class="font-medium text-gray-900">{{ $survey->assignedUser->name }}</p>
                                            @if (! $survey->approved_at)
                                                <p class="text-xs text-amber-600">Menunggu SPS Direktur</p>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-400">Belum dipilih Admin</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900">{{ $survey->scheduled_at?->format('d M Y H:i') ?? 'Belum dijadwalkan' }}</p>
                                        <p class="text-xs text-gray-500">{{ $survey->location }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($survey->approved_at)
                                            <span class="inline-flex px-2.5 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold">
                                                Disetujui Direktur
                                            </span>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $survey->approved_at->format('d M Y H:i') }}</p>
                                        @elseif ($survey->status === 'menunggu_persetujuan_direktur')
                                            <span class="inline-flex px-2.5 py-1 rounded-full bg-purple-100 text-purple-800 text-xs font-semibold">
                                                Menunggu Approval Direktur
                                            </span>
                                        @elseif ($survey->status === 'ditolak_direktur')
                                            <span class="inline-flex px-2.5 py-1 rounded-full bg-red-100 text-red-800 text-xs font-semibold">
                                                Revisi Direktur
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400">Menunggu Admin</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('surveys.show', $survey) }}"
                                               class="text-blue-600 hover:underline font-semibold">
                                                @if ($role === 'direktur' && $survey->status === 'menunggu_persetujuan_direktur')
                                                    Proses Approval SPS
                                                @elseif ($role === 'admin' && $survey->status === 'diajukan_client')
                                                    Teruskan ke Direktur
                                                @elseif ($role === 'petugas-lapangan')
                                                    Buka SPS & Input Hasil
                                                @else
                                                    Detail
                                                @endif
                                            </a>
                                            @if ($survey->order)
                                                <a href="{{ route('tracking.show', $survey->order) }}"
                                                   class="text-emerald-600 hover:underline text-xs font-semibold">
                                                    Lacak Alur
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                        Belum ada data pengajuan survey / Surat Perintah Survey.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $surveys->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
