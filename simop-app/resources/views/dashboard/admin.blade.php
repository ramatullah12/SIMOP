<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Beranda Admin</h2>
                <p class="mt-0.5 text-sm text-slate-500">
                    Ringkasan data pesanan, pengajuan survei, stok barang, dan pelaksanaan proyek.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('surveys.create') }}" class="btn-primary">
                    + Buat Pengajuan Survei
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
        @if (($stats['users_unverified_count'] ?? 0) > 0)
            <div class="rounded-xl bg-amber-50 border border-amber-300 p-4 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-100 rounded-lg text-amber-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-amber-900 text-sm">Verifikasi Pengguna Baru</h4>
                        <p class="text-xs text-amber-700 mt-0.5">
                            Ada <strong class="underline">{{ $stats['users_unverified_count'] }} akun pendaftar</strong> yang menunggu verifikasi Anda agar dapat masuk ke sistem.
                        </p>
                    </div>
                </div>
                <a href="{{ route('admin.users.index', ['verification_status' => 'unverified']) }}"
                   class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-md text-xs font-semibold shadow-sm transition shrink-0 inline-flex items-center gap-1.5">
                    Verifikasi Sekarang &rarr;
                </a>
            </div>
        @endif

        @if (($stats['orders_masuk_count'] ?? 0) > 0)
            <div class="rounded-xl bg-blue-50 border border-blue-300 p-4 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 rounded-lg text-blue-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-blue-900 text-sm">Pesanan Baru Perlu Diverifikasi</h4>
                        <p class="text-xs text-blue-700 mt-0.5">
                            Terdapat <strong class="underline">{{ $stats['orders_masuk_count'] }} pesanan baru</strong> berstatus Masuk yang perlu diverifikasi oleh Admin.
                        </p>
                    </div>
                </div>
                <a href="{{ route('orders.index', ['status' => 'masuk']) }}"
                   class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold shadow-sm transition shrink-0 inline-flex items-center gap-1.5">
                    Tinjau Pesanan &rarr;
                </a>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Total Pesanan</span>
                <div class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['orders_total'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Data pesanan masuk</span>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Draf & Revisi Survei</span>
                <div class="text-2xl font-bold text-yellow-600 mt-2">{{ $stats['surveys_waiting_admin'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Perlu diproses oleh Admin</span>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Menunggu Persetujuan Direktur</span>
                <div class="text-2xl font-bold text-blue-600 mt-2">{{ $stats['surveys_waiting_direktur'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Pengajuan Surat Perintah Survei</span>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Proyek Aktif / Selesai</span>
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
                    <h3 class="font-semibold text-gray-900 text-base">Pengajuan Survei Terbaru</h3>
                    <a href="{{ route('surveys.index') }}" class="text-xs font-medium text-blue-600 hover:underline">
                        Lihat Semua
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Pesanan & Kegiatan</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Status Survei</th>
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
