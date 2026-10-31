<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Dokumentasi Lapangan Pengerjaan
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Fitur khusus dokumentasi pengerjaan lapangan yang diinput langsung oleh Petugas Lapangan.
                </p>
            </div>

            @if ($role === 'petugas-lapangan')
                <a href="{{ route('field-documentations.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md text-white text-sm font-medium hover:bg-blue-700">
                    + Input Dokumentasi Lapangan
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

            @if ($role !== 'petugas-lapangan')
                <div class="rounded-lg bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 text-sm">
                    Dokumentasi pada menu ini diinput oleh <strong>Petugas Lapangan</strong> sebagai bukti dan laporan aktivitas pengerjaan di lokasi event/proyek.
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">No. Dokumentasi</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Project & Event</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Judul Aktivitas</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600">Progress</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Petugas Lapangan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Lampiran</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($documentations as $doc)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $doc->documentation_number }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900">{{ $doc->project?->project_number }}</p>
                                        <p class="text-xs text-gray-500">{{ $doc->project?->workEstimate?->order?->event_name ?? $doc->project?->title }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900">{{ $doc->title }}</p>
                                        <p class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($doc->description, 60) }}</p>
                                    </td>
                                    <td class="px-4 py-3">{{ $doc->activity_date?->format('d M Y') ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($doc->progress_percentage !== null)
                                            <span class="inline-flex px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold">
                                                {{ $doc->progress_percentage }}%
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $doc->uploader?->name ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        @if ($doc->file_path)
                                            <a href="{{ route('field-documentations.download', $doc) }}"
                                               class="text-blue-600 hover:underline font-medium text-xs">
                                                Unduh File ({{ $doc->file_name }})
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400">Tanpa file</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('field-documentations.show', $doc) }}"
                                           class="text-blue-600 hover:underline font-medium">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                        Belum ada dokumentasi lapangan pengerjaan yang diinput.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $documentations->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

