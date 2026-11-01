<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Produksi & Instalasi — {{ $production->production_number }}
            </h2>

            <div class="flex flex-wrap items-center gap-2">
                @if ($production->planning?->project?->order)
                    <a href="{{ route('tracking.show', $production->planning->project->order) }}"
                       class="inline-flex items-center px-4 py-2 bg-emerald-600 rounded-md text-white text-sm font-medium hover:bg-emerald-700">
                        Status Pekerjaan
                    </a>
                @endif

                @if (auth()->user()?->role?->slug === 'petugas-lapangan')
                    <a href="{{ route('field-documentations.create', ['project_id' => $production->planning?->project_id]) }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md text-white text-sm font-medium hover:bg-blue-700">
                        + Unggah Dokumentasi Lapangan
                    </a>
                @endif

                @if (auth()->user()?->role?->slug === 'admin')
                    <a href="{{ route('productions.edit', $production) }}"
                       class="inline-flex items-center px-4 py-2 bg-yellow-500 rounded-md text-white text-sm font-medium hover:bg-yellow-600">
                        Ubah Produksi
                    </a>
                @endif

                <a href="{{ route('productions.index') }}"
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

            @php
                $statusLabel = match ($production->status) {
                    'pending' => 'Menunggu',
                    'in_progress' => 'Sedang Dikerjakan',
                    'completed' => 'Selesai',
                    'cancelled' => 'Dibatalkan',
                    default => strtoupper(str_replace('_', ' ', $production->status)),
                };
            @endphp

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-gray-500">Nomor Produksi</p>
                        <p class="font-semibold text-gray-900">{{ $production->production_number }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Judul Produksi</p>
                        <p class="font-semibold text-gray-900">{{ $production->title }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Perencanaan</p>
                        <p class="font-semibold text-gray-900">
                            {{ $production->planning?->planning_number ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Proyek</p>
                        <p class="font-semibold text-gray-900">
                            <a href="{{ route('projects.show', $production->planning?->project) }}" class="text-blue-600 hover:underline">
                                {{ $production->planning?->project?->project_number ?? '-' }}
                            </a>
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Klien & Kegiatan</p>
                        <p class="font-semibold text-gray-900">
                            {{ $production->planning?->project?->order?->company_name ?? '-' }} — {{ $production->planning?->project?->order?->event_name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Petugas Lapangan</p>
                        <p class="font-semibold text-gray-900">
                            {{ $production->assignedUser?->name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Tanggal Mulai</p>
                        <p class="font-semibold text-gray-900">
                            {{ $production->start_date ? $production->start_date->format('d M Y') : '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Tanggal Selesai</p>
                        <p class="font-semibold text-gray-900">
                            {{ $production->end_date ? $production->end_date->format('d M Y') : '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Status</p>
                        <p class="font-semibold text-gray-900">
                            {{ $statusLabel }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Kemajuan</p>
                        <p class="font-semibold text-gray-900">{{ $production->progress }}%</p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-gray-500">Deskripsi Pekerjaan Produksi</p>
                        <p class="font-semibold text-gray-900 whitespace-pre-line">
                            {{ $production->description }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-gray-500">Catatan</p>
                        <p class="font-semibold text-gray-900 whitespace-pre-line">
                            {{ $production->notes ?: '-' }}
                        </p>
                    </div>
                </div>
            </div>

            @if (
                auth()->user()?->role?->slug === 'petugas-lapangan' &&
                $production->assigned_to === auth()->id()
            )
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border-2 border-blue-200">
                    <h3 class="font-semibold text-lg text-gray-800 mb-1">
                        Perbarui Kemajuan Lapangan (Petugas Lapangan)
                    </h3>
                    <p class="text-xs text-gray-500 mb-4">
                        Saat kemajuan mencapai 100% (Selesai), status kegiatan/proyek otomatis selesai dan seluruh stok barang yang dibawa otomatis kembali ke stok awal.
                    </p>

                    <form method="POST"
                          action="{{ route('productions.update-progress', $production) }}"
                          class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Kemajuan (%)
                                </label>
                                <input type="number"
                                       name="progress"
                                       min="0"
                                       max="100"
                                       value="{{ old('progress', $production->progress) }}"
                                       required
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('progress')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Status
                                </label>
                                <select name="status"
                                        required
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="pending" @selected(old('status', $production->status) === 'pending')>Menunggu</option>
                                    <option value="in_progress" @selected(old('status', $production->status) === 'in_progress')>Sedang Dikerjakan</option>
                                    <option value="completed" @selected(old('status', $production->status) === 'completed')>Selesai (100% - Selesai & Kembalikan Stok)</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Catatan Lapangan
                            </label>
                            <textarea name="notes"
                                      rows="4"
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $production->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md text-white text-sm font-medium hover:bg-blue-700">
                            Simpan Kemajuan
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
