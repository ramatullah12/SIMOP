<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Dashboard Direktur</h2>
                <p class="mt-0.5 text-sm text-slate-500">
                    Persetujuan Surat Perintah Survey, monitoring stok barang proyek, dan pemantauan pekerjaan.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('surveys.index') }}" class="btn-primary">
                    Approval Survey ({{ $stats['pending_survey_approvals'] ?? 0 }})
                </a>
                <a href="{{ route('inventories.index') }}" class="btn-secondary">
                    Stok Barang
                </a>
                <a href="{{ route('tracking.index') }}" class="btn-secondary">
                    Status Pekerjaan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Menunggu Approval Survey</span>
                <div class="text-2xl font-bold text-blue-600 mt-2">{{ $stats['pending_survey_approvals'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Pengajuan Surat Perintah Survey</span>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Proyek Berjalan</span>
                <div class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['active_projects'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Sedang dalam pengerjaan</span>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Proyek Selesai</span>
                <div class="text-2xl font-bold text-green-600 mt-2">{{ $stats['completed_projects'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Barang telah kembali ke stok awal</span>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-900 text-base">Pengajuan Survey Menunggu Persetujuan Direktur</h3>
                    <p class="text-xs text-gray-500">
                        Setujui pengajuan survey agar Surat Perintah Survey dikirim ke petugas lapangan.
                    </p>
                </div>
                <a href="{{ route('surveys.index') }}" class="text-xs font-medium text-blue-600 hover:underline">
                    Lihat Semua
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">No. Order & Event</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Client</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Petugas Lapangan</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Jadwal & Lokasi</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($pendingSurveys as $survey)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-gray-900">{{ $survey->order?->order_number }}</p>
                                    <p class="text-xs text-gray-500">{{ $survey->order?->event_name }}</p>
                                </td>
                                <td class="px-4 py-3">{{ $survey->order?->company_name }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $survey->assignedUser?->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium">{{ $survey->scheduled_at?->format('d M Y H:i') ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $survey->location }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('surveys.approve-direktur', $survey) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-green-600 text-white rounded text-xs font-medium hover:bg-green-700">
                                                Setujui & Terbitkan SPS
                                            </button>
                                        </form>
                                        <a href="{{ route('surveys.show', $survey) }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded text-xs font-medium hover:bg-gray-200">
                                            Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                    Tidak ada pengajuan survey yang menunggu persetujuan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-900 text-base">Data Stok Barang & Pengembalian Proyek</h3>
                    <p class="text-xs text-gray-500">
                        Stok awal barang, jumlah yang sedang dipakai proyek, dan status pengembalian setelah event selesai.
                    </p>
                </div>
                <a href="{{ route('inventories.index') }}" class="text-xs font-medium text-blue-600 hover:underline">
                    Detail Stok Barang
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama Barang</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">Stok Awal</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">Dipakai Proyek</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">Sisa Stok</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Status Stok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($items as $item)
                            @php
                                $activeAllocs = $item->projectItems->where('status', 'allocated');
                            @endphp
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    {{ $item->name }} ({{ $item->item_code }})
                                </td>
                                <td class="px-4 py-3 text-center font-semibold">{{ $item->total_stock }} {{ $item->unit }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-yellow-700">
                                    {{ $item->used_stock }} {{ $item->unit }}
                                </td>
                                <td class="px-4 py-3 text-center font-semibold text-green-700">
                                    {{ $item->available_stock }} {{ $item->unit }}
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    @if ($activeAllocs->isNotEmpty())
                                        @foreach ($activeAllocs as $al)
                                            <div class="text-yellow-800">
                                                Dipakai pada {{ $al->project?->project_number }} ({{ $al->project?->workEstimate?->order?->event_name }}): {{ $al->quantity }} {{ $item->unit }}
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-green-100 text-green-800 font-medium">
                                            Stok lengkap di gudang ({{ $item->total_stock }} {{ $item->unit }})
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
