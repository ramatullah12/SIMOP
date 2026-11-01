<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Proyek — {{ $project->project_number }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $project->title }} ({{ $project->workEstimate?->order?->event_name ?? '-' }})
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if ($project->workEstimate?->order)
                    <a href="{{ route('tracking.show', $project->workEstimate->order) }}"
                       class="inline-flex items-center px-4 py-2 bg-emerald-600 rounded-md text-white text-sm font-medium hover:bg-emerald-700">
                        Status Pekerjaan
                    </a>
                @endif

                @if (auth()->user()?->role?->slug === 'admin')
                    @if (! $project->planning)
                        <a href="{{ route('plannings.create', ['project_id' => $project->id]) }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 rounded-md text-white text-sm font-medium hover:bg-indigo-700">
                            + Buat Perencanaan
                        </a>
                    @else
                        <a href="{{ route('plannings.show', $project->planning) }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 rounded-md text-white text-sm font-medium hover:bg-indigo-700">
                            Lihat Perencanaan
                        </a>
                    @endif

                    <a href="{{ route('projects.edit', $project) }}"
                       class="inline-flex items-center px-4 py-2 bg-yellow-500 rounded-md text-white text-sm font-medium hover:bg-yellow-600">
                        Ubah Proyek
                    </a>
                @endif

                @if (auth()->user()?->role?->slug === 'petugas-lapangan')
                    <a href="{{ route('field-documentations.create', ['project_id' => $project->id]) }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md text-white text-sm font-medium hover:bg-blue-700">
                        + Input Dokumentasi Lapangan
                    </a>
                @endif

                @if (
                    auth()->user()?->role?->slug === 'client' &&
                    $project->status === 'completed' &&
                    ! $project->rating
                )
                    <a href="{{ route('ratings.create', ['project_id' => $project->id]) }}"
                       class="inline-flex items-center px-4 py-2 bg-amber-500 rounded-md text-white text-sm font-medium hover:bg-amber-600">
                        + Beri Penilaian
                    </a>
                @endif

                <a href="{{ route('projects.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-md text-gray-700 text-sm font-medium hover:bg-gray-300">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3 font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3 font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-gray-500">Nomor Proyek</p>
                        <p class="font-semibold text-gray-900">{{ $project->project_number }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Judul Proyek</p>
                        <p class="font-semibold text-gray-900">{{ $project->title }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Nomor Pesanan</p>
                        <p class="font-semibold text-gray-900">
                            {{ $project->order?->order_number ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Klien & Kegiatan</p>
                        <p class="font-semibold text-gray-900">
                            {{ $project->order?->company_name ?? '-' }} — {{ $project->order?->event_name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Petugas Lapangan</p>
                        <p class="font-semibold text-gray-900">
                            {{ $project->assignedUser?->name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Dibuat Oleh</p>
                        <p class="font-semibold text-gray-900">
                            {{ $project->creator?->name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Tanggal Mulai</p>
                        <p class="font-semibold text-gray-900">
                            {{ $project->start_date ? $project->start_date->format('d M Y') : '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Batas Waktu</p>
                        <p class="font-semibold text-gray-900">
                            {{ $project->deadline ? $project->deadline->format('d M Y') : '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Status Proyek</p>
                        <p class="font-semibold text-gray-900">
                            {{ strtoupper(str_replace('_', ' ', $project->status)) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Kemajuan</p>
                        <p class="font-semibold text-gray-900">{{ $project->progress }}%</p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-gray-500">Catatan</p>
                        <p class="font-semibold text-gray-900">{{ $project->notes ?: '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Stok Barang yang Dibawa ke Proyek Ini -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-200 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h3 class="font-semibold text-lg text-gray-800">
                            Stok Barang yang Dibawa ke Proyek Ini
                        </h3>
                        <p class="text-sm text-gray-500">
                            Saat event/proyek selesai (Completed), seluruh barang di bawah ini otomatis dikembalikan ke stok awal.
                        </p>
                    </div>

                    @if (in_array(auth()->user()?->role?->slug, ['admin', 'direktur']))
                        <a href="{{ route('inventories.index') }}" class="text-blue-600 hover:underline text-sm font-semibold">
                            Buka Data Stok Barang
                        </a>
                    @endif
                </div>

                @if (auth()->user()?->role?->slug === 'admin' && ! in_array($project->status, ['completed', 'cancelled']))
                    <form method="POST" action="{{ route('inventories.allocate') }}" class="p-4 rounded-lg bg-gray-50 border border-gray-200 grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Pilih Barang dari Stok Gudang</label>
                            <select name="item_id" required class="w-full rounded-md border-gray-300 text-sm">
                                <option value="">-- Pilih Barang --</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->name }} (Tersedia: {{ $item->available_stock }} / Stok Awal: {{ $item->total_stock }} {{ $item->unit }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Jumlah Dibawa</label>
                            <input type="number" name="quantity" min="1" required placeholder="Contoh: 50"
                                   class="w-full rounded-md border-gray-300 text-sm">
                        </div>

                        <div>
                            <button type="submit" class="w-full py-2 px-4 bg-blue-600 text-white rounded-md text-sm font-semibold hover:bg-blue-700">
                                + Bawa ke Proyek
                            </button>
                        </div>
                    </form>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama Barang</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600">Stok Awal Gudang</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600">Dibawa ke Proyek Ini</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600">Sisa Stok Tersedia Saat Ini</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Status Pengembalian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($project->projectItems as $pi)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">
                                        {{ $pi->item?->name }}
                                        <span class="text-xs text-gray-500">({{ $pi->item?->item_code }})</span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold">{{ $pi->item?->total_stock }} {{ $pi->item?->unit }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-amber-700">{{ $pi->quantity }} {{ $pi->item?->unit }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-emerald-700">{{ $pi->item?->available_stock }} {{ $pi->item?->unit }}</td>
                                    <td class="px-4 py-3">
                                        @if ($pi->status === 'returned')
                                            <span class="inline-flex px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold">
                                                Sudah Kembali ke Stok Awal ({{ $pi->returned_at?->format('d M Y H:i') }})
                                            </span>
                                        @else
                                            <span class="inline-flex px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold">
                                                Sedang Dibawa Proyek
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-5 text-center text-gray-500">
                                        Belum ada barang operasional yang dialokasikan ke proyek ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Dokumentasi Lapangan Pengerjaan -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-200 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-lg text-gray-800">
                            Dokumentasi Lapangan Pengerjaan (Oleh Petugas Lapangan)
                        </h3>
                        <p class="text-sm text-gray-500">Laporan dan bukti pengerjaan yang diinput oleh Petugas Lapangan.</p>
                    </div>
                    <a href="{{ route('field-documentations.index') }}" class="text-blue-600 hover:underline text-sm font-semibold">
                        Lihat Semua Dokumentasi
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">No. Dok</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Judul Aktivitas</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Petugas Lapangan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($project->fieldDocumentations as $doc)
                                <tr>
                                    <td class="px-4 py-3 font-medium">{{ $doc->documentation_number }}</td>
                                    <td class="px-4 py-3">{{ $doc->title }}</td>
                                    <td class="px-4 py-3">{{ $doc->activity_date?->format('d M Y') }}</td>
                                    <td class="px-4 py-3">{{ $doc->uploader?->name ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('field-documentations.show', $doc) }}" class="text-blue-600 hover:underline font-medium">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-5 text-center text-gray-500">
                                        Belum ada dokumentasi lapangan pengerjaan untuk proyek ini.
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
