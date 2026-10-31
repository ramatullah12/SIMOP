<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Dashboard Admin</h2>
                <p class="mt-0.5 text-sm text-slate-500">
                    Ringkasan data order, pengajuan survey, stok barang, dan pelaksanaan proyek.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('surveys.index') }}" class="btn-primary">
                    Pengajuan Survey ({{ $stats['surveys_waiting_admin'] ?? 0 }})
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
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Total Order</span>
                <div class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['orders_total'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Data order masuk</span>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Survey Baru (Client)</span>
                <div class="text-2xl font-bold text-yellow-600 mt-2">{{ $stats['surveys_waiting_admin'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Menunggu diteruskan ke Direktur</span>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Menunggu Approval Direktur</span>
                <div class="text-2xl font-bold text-blue-600 mt-2">{{ $stats['surveys_waiting_direktur'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Pengajuan Surat Perintah Survey</span>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Project Aktif / Selesai</span>
                <div class="text-2xl font-bold text-green-600 mt-2">{{ $stats['projects_active'] ?? 0 }} / {{ $stats['projects_completed'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Status pelaksanaan proyek</span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 text-base">Data Stok Barang</h3>
                    <a href="{{ route('inventories.index') }}" class="text-xs font-medium text-blue-600 hover:underline">
                        Lihat Semua
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Barang</th>
                                <th class="px-4 py-2.5 text-center font-semibold text-gray-600">Stok Awal</th>
                                <th class="px-4 py-2.5 text-center font-semibold text-gray-600">Dipakai</th>
                                <th class="px-4 py-2.5 text-center font-semibold text-gray-600">Sisa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($items as $item)
                                <tr>
                                    <td class="px-4 py-2.5 font-medium text-gray-900">{{ $item->name }}</td>
                                    <td class="px-4 py-2.5 text-center">{{ $item->total_stock }} {{ $item->unit }}</td>
                                    <td class="px-4 py-2.5 text-center text-yellow-700 font-semibold">{{ $item->used_stock }} {{ $item->unit }}</td>
                                    <td class="px-4 py-2.5 text-center text-green-700 font-semibold">{{ $item->available_stock }} {{ $item->unit }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-gray-500">Belum ada data barang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 text-base">Pengajuan Survey Terbaru</h3>
                    <a href="{{ route('surveys.index') }}" class="text-xs font-medium text-blue-600 hover:underline">
                        Lihat Semua
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Order & Event</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Status Survey</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($recentSurveys as $survey)
                                <tr>
                                    <td class="px-4 py-2.5">
                                        <p class="font-semibold text-gray-900">{{ $survey->order?->order_number }}</p>
                                        <p class="text-xs text-gray-500">{{ $survey->order?->event_name }}</p>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-xs font-medium">
                                            {{ $survey->sps_number ?: strtoupper(str_replace('_', ' ', $survey->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <a href="{{ route('surveys.show', $survey) }}" class="text-blue-600 hover:underline font-medium text-xs">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-center text-gray-500">Belum ada pengajuan survey.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
