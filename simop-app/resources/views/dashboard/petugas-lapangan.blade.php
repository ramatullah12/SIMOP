<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Dashboard Petugas Lapangan</h2>
                <p class="mt-0.5 text-sm text-slate-500">
                    Daftar Surat Perintah Survey, pengerjaan produksi, dan dokumentasi lapangan.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('surveys.index') }}" class="btn-primary">
                    Surat Perintah Survey ({{ $stats['sps_active'] ?? 0 }})
                </a>
                <a href="{{ route('field-documentations.create') }}" class="btn-secondary">
                    Input Dokumentasi Lapangan
                </a>
                <a href="{{ route('productions.index') }}" class="btn-secondary">
                    Produksi
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">SPS Aktif</span>
                <div class="text-2xl font-bold text-blue-600 mt-2">{{ $stats['sps_active'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Surat Perintah Survey terjadwal</span>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Survey Selesai</span>
                <div class="text-2xl font-bold text-green-600 mt-2">{{ $stats['sps_completed'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Hasil survey telah diinput</span>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Produksi Aktif</span>
                <div class="text-2xl font-bold text-yellow-600 mt-2">{{ $stats['productions_active'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Tugas pengerjaan lapangan</span>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Dokumentasi Lapangan</span>
                <div class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['field_docs_count'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Total dokumentasi diinput</span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 text-base">Surat Perintah Survey</h3>
                    <a href="{{ route('surveys.index') }}" class="text-xs font-medium text-blue-600 hover:underline">
                        Lihat Semua
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600">No. SPS</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Event & Lokasi</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Status</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($activeSps as $survey)
                                <tr>
                                    <td class="px-4 py-2.5 font-semibold text-blue-700">{{ $survey->sps_number }}</td>
                                    <td class="px-4 py-2.5">
                                        <p class="font-medium text-gray-900">{{ $survey->order?->event_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $survey->location }}</p>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-xs font-medium">
                                            {{ strtoupper($survey->status) }}
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
                                    <td colspan="4" class="px-4 py-4 text-center text-gray-500">
                                        Belum ada Surat Perintah Survey.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 text-base">Dokumentasi Lapangan Terbaru</h3>
                    <a href="{{ route('field-documentations.create') }}" class="text-xs font-medium text-blue-600 hover:underline">
                        Input Baru
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600">No. Dok</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Judul</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Project</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($recentFieldDocs as $doc)
                                <tr>
                                    <td class="px-4 py-2.5 font-medium">{{ $doc->documentation_number }}</td>
                                    <td class="px-4 py-2.5">{{ $doc->title }}</td>
                                    <td class="px-4 py-2.5">{{ $doc->project?->project_number }}</td>
                                    <td class="px-4 py-2.5">
                                        <a href="{{ route('field-documentations.show', $doc) }}" class="text-blue-600 hover:underline text-xs font-medium">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-gray-500">
                                        Belum ada dokumentasi lapangan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
