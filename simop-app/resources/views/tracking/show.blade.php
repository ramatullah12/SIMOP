<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Status Pekerjaan - {{ $order->order_number }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Alur pengerjaan dari pengajuan survey awal sampai pekerjaan selesai.
                </p>
            </div>

            <div class="flex items-center gap-2">
                @if ($order->survey)
                    <a href="{{ route('surveys.show', $order->survey) }}"
                       class="px-3 py-2 bg-blue-600 rounded-md text-white text-xs font-medium hover:bg-blue-700">
                        Detail Survey
                    </a>
                @endif
                @if ($order->project)
                    <a href="{{ route('projects.show', $order->project) }}"
                       class="px-3 py-2 bg-indigo-600 rounded-md text-white text-xs font-medium hover:bg-indigo-700">
                        Detail Project
                    </a>
                @endif
                <a href="{{ route('tracking.index') }}"
                   class="px-4 py-2 bg-gray-200 rounded-md text-gray-700 text-sm font-medium hover:bg-gray-300">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $steps = $tracking['steps'] ?? [];
        $chronologicalLogs = $tracking['chronological_logs'] ?? [];
        $project = $order->project;
        $projectItems = $project?->projectItems ?? collect();
        $fieldDocs = $project?->fieldDocumentations ?? collect();
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-blue-600 px-6 py-4 text-white flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                    <div>
                        <p class="text-xs uppercase font-medium text-blue-100">Status Saat Ini</p>
                        <h3 class="text-lg font-semibold mt-0.5">{{ $tracking['current_headline'] ?? '-' }}</h3>
                    </div>
                    <div class="text-left md:text-right">
                        <p class="text-xs text-blue-100">No. Order: {{ $order->order_number }}</p>
                        <p class="text-sm font-semibold">Progress: {{ $tracking['overall_progress'] ?? 0 }}%</p>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                        @foreach ($steps as $step)
                            @php
                                $isDone = $step['state'] === 'done';
                                $isActive = $step['state'] === 'active';
                            @endphp
                            <div class="p-3.5 rounded-lg border text-center
                                {{ $isDone ? 'border-green-400 bg-green-50' : ($isActive ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-gray-50 opacity-65') }}">
                                <div class="mx-auto w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold mb-2
                                    {{ $isDone ? 'bg-green-600 text-white' : ($isActive ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-700') }}">
                                    {{ $step['number'] }}
                                </div>

                                <p class="text-xs font-semibold text-gray-900">{{ $step['title'] }}</p>
                                <p class="text-[11px] text-gray-500 mt-0.5">{{ $step['subtitle'] }}</p>

                                <div class="mt-2 pt-2 border-t border-gray-200">
                                    @if ($isDone)
                                        <span class="text-[10px] font-semibold uppercase text-green-700">Selesai</span>
                                    @elseif ($isActive)
                                        <span class="text-[10px] font-semibold uppercase text-blue-700">Berjalan</span>
                                    @else
                                        <span class="text-[10px] font-medium uppercase text-gray-400">Menunggu</span>
                                    @endif

                                    @if ($step['time'])
                                        <p class="text-[10px] text-gray-600 mt-0.5">{{ $step['time'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="space-y-6">
                    <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm space-y-3 text-sm">
                        <h3 class="font-semibold text-gray-900 text-base border-b pb-2">Informasi Order</h3>
                        <div>
                            <p class="text-xs text-gray-500">Perusahaan / Client</p>
                            <p class="font-semibold text-gray-900">{{ $order->company_name }} ({{ $order->contact_person }})</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Nama Event</p>
                            <p class="font-semibold text-gray-900">{{ $order->event_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Lokasi</p>
                            <p class="font-semibold text-gray-900">{{ $order->event_location }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Tanggal Event</p>
                            <p class="font-semibold text-gray-900">{{ $order->event_date?->format('d M Y') ?? '-' }}</p>
                        </div>
                        @if ($order->survey)
                            <div class="pt-2 border-t">
                                <p class="text-xs text-gray-500">Nomor Surat Perintah Survey</p>
                                <p class="font-semibold text-blue-700">{{ $order->survey->sps_number ?: 'Menunggu Persetujuan Direktur' }}</p>
                                <p class="text-xs text-gray-600 mt-0.5">
                                    Petugas Lapangan: {{ $order->survey->assignedUser?->name ?? 'Belum dipilih' }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm space-y-3 text-sm">
                        <h3 class="font-semibold text-gray-900 text-base border-b pb-2">Pemakaian Stok Barang</h3>
                        @forelse ($projectItems as $pi)
                            <div class="p-3 rounded-lg border {{ $pi->status === 'returned' ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200' }}">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-gray-900">{{ $pi->item?->name }}</span>
                                    <span class="text-xs font-semibold">
                                        {{ $pi->quantity }} {{ $pi->item?->unit }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-600 mt-1">
                                    Stok Awal: {{ $pi->item?->total_stock }} {{ $pi->item?->unit }} | Sisa: {{ $pi->item?->available_stock }} {{ $pi->item?->unit }}
                                </p>
                                <p class="text-xs font-medium mt-1 {{ $pi->status === 'returned' ? 'text-green-700' : 'text-yellow-800' }}">
                                    {{ $pi->status === 'returned' ? 'Sudah kembali ke stok awal (' . ($pi->returned_at?->format('d/m/Y H:i') ?? '-') . ')' : 'Sedang dipakai di proyek' }}
                                </p>
                            </div>
                        @empty
                            <p class="text-xs text-gray-500">Belum ada pemakaian stok barang pada pekerjaan ini.</p>
                        @endforelse
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                        <div class="border-b pb-3 mb-5">
                            <h3 class="font-semibold text-lg text-gray-900">
                                Riwayat Status Pekerjaan
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Catatan tahapan pekerjaan dari awal survey sampai selesai.
                            </p>
                        </div>

                        <div class="relative pl-6 border-l-2 border-gray-200 space-y-5">
                            @foreach ($chronologicalLogs as $log)
                                @php
                                    $isDone = $log['state'] === 'done';
                                    $isActive = $log['state'] === 'active';
                                    $isRejected = $log['state'] === 'rejected';
                                @endphp
                                <div class="relative">
                                    <span class="absolute -left-[31px] top-1.5 w-3.5 h-3.5 rounded-full border-2 border-white
                                        {{ $isDone ? 'bg-green-600' : ($isActive ? 'bg-blue-600' : ($isRejected ? 'bg-red-600' : 'bg-gray-300')) }}">
                                    </span>

                                    <div class="p-4 rounded-lg border
                                        {{ $isDone ? 'border-green-200 bg-green-50/40' : ($isActive ? 'border-blue-300 bg-blue-50/60' : ($isRejected ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50 opacity-70')) }}">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-1">
                                            <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold
                                                {{ $isDone ? 'bg-green-100 text-green-800' : ($isActive ? 'bg-blue-100 text-blue-800' : ($isRejected ? 'bg-red-100 text-red-800' : 'bg-gray-200 text-gray-600')) }}">
                                                {{ $log['badge'] }}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                {{ $log['timestamp'] }}
                                            </span>
                                        </div>

                                        <h4 class="font-semibold text-gray-900 text-sm mt-1">
                                            {{ $log['title'] }}
                                        </h4>
                                        <p class="text-sm text-gray-700 mt-1">
                                            {{ $log['description'] }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1.5">
                                            Oleh: {{ $log['actor'] }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="font-semibold text-base text-gray-900">
                                    Dokumentasi Lapangan Pengerjaan
                                </h3>
                                <p class="text-xs text-gray-500">Dokumentasi yang diinput oleh petugas lapangan.</p>
                            </div>
                            <a href="{{ route('field-documentations.index') }}" class="text-xs font-medium text-blue-600 hover:underline">
                                Lihat Semua
                            </a>
                        </div>

                        <div class="space-y-3">
                            @forelse ($fieldDocs as $doc)
                                <div class="p-4 rounded-lg border border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $doc->title }} ({{ $doc->documentation_number }})</p>
                                        <p class="text-xs text-gray-600 mt-0.5">{{ $doc->description }}</p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            Tanggal: {{ $doc->activity_date?->format('d M Y') }} | Petugas: {{ $doc->uploader?->name ?? '-' }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if ($doc->file_path)
                                            <a href="{{ route('field-documentations.download', $doc) }}"
                                               class="px-3 py-1.5 bg-blue-600 text-white rounded text-xs font-medium hover:bg-blue-700">
                                                Unduh File
                                            </a>
                                        @endif
                                        <a href="{{ route('field-documentations.show', $doc) }}"
                                           class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded text-xs font-medium hover:bg-gray-200">
                                            Detail
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 text-center py-4">
                                    Belum ada dokumentasi lapangan untuk pekerjaan ini.
                                </p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
