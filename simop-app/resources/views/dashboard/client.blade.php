<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Beranda Klien</h2>
                <p class="mt-0.5 text-sm text-slate-500">
                    Pengajuan pesanan, permintaan survei, dan pemantauan status pekerjaan.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('orders.create') }}" class="btn-primary">
                    Buat Pesanan & Ajukan Survei
                </a>
                <a href="{{ route('tracking.index') }}" class="btn-secondary">
                    Status Pekerjaan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Total Pesanan</span>
                <div class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['orders_count'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Pesanan yang diajukan</span>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Pengajuan Survei</span>
                <div class="text-2xl font-bold text-blue-600 mt-2">{{ $stats['surveys_count'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Total survei lokasi</span>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Proyek Berjalan</span>
                <div class="text-2xl font-bold text-yellow-600 mt-2">{{ $stats['active_projects'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Sedang dikerjakan</span>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Pekerjaan Selesai</span>
                <div class="text-2xl font-bold text-green-600 mt-2">{{ $stats['completed_projects'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Proyek telah selesai</span>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900 text-base">Daftar Pesanan & Status Pekerjaan</h3>
                <a href="{{ route('tracking.index') }}" class="text-xs font-medium text-blue-600 hover:underline">
                    Lihat Semua
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">No. Pesanan</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Kegiatan & Lokasi</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Status Survei</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Status Proyek</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-gray-900">{{ $order->order_number }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">{{ $order->event_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->event_location }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($order->survey)
                                        <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-xs font-medium">
                                            {{ $order->survey->sps_number ?: strtoupper(str_replace('_', ' ', $order->survey->status)) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">Belum ada</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($order->project)
                                        <span class="px-2 py-0.5 rounded bg-green-100 text-green-800 text-xs font-medium">
                                            {{ strtoupper($order->project->status) }} ({{ $order->project->progress }}%)
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-500">Dalam Proses</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('tracking.show', $order) }}"
                                       class="px-3 py-1.5 bg-blue-600 text-white rounded text-xs font-medium hover:bg-blue-700">
                                        Lihat Status
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                    Belum ada data order.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
