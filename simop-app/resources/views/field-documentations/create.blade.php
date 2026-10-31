<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Unggah Dokumentasi Lapangan Pengerjaan
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('field-documentations.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Proyek / Pengerjaan Lapangan</label>
                        <select name="project_id" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Pilih Proyek --</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}"
                                    @selected(old('project_id', $selectedProject?->id) == $project->id)>
                                    {{ $project->project_number }} - {{ $project->title }} ({{ $project->order?->event_name ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Dokumentasi Pengerjaan</label>
                            <input type="text" name="title" value="{{ old('title') }}" required
                                   placeholder="Contoh: Pemasangan Struktur, Kursi & Stan Pameran Area Utama"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Aktivitas</label>
                            <input type="date" name="activity_date" value="{{ old('activity_date', now()->format('Y-m-d')) }}" required
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('activity_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kemajuan Pengerjaan Lapangan (%)</label>
                            <input type="number" name="progress_percentage" min="0" max="100"
                                   value="{{ old('progress_percentage', $selectedProject?->progress ?? 50) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('progress_percentage')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Foto / Berkas Bukti Lapangan (Opsional)</label>
                            <input type="file" name="file"
                                   class="w-full rounded-lg border border-gray-300 p-2 text-sm text-gray-700">
                            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, WEBP, PDF, DOC, ZIP (Maks. 10MB)</p>
                            @error('file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Pengerjaan di Lapangan</label>
                        <textarea name="description" rows="4" required
                                  placeholder="Jelaskan detail pekerjaan yang sudah dikerjakan oleh tim lapangan..."
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Kendala / Tambahan (Opsional)</label>
                        <textarea name="notes" rows="2"
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md text-white text-sm font-medium hover:bg-blue-700">
                            Simpan Dokumentasi Lapangan
                        </button>
                        <a href="{{ route('field-documentations.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-md text-gray-700 text-sm font-medium hover:bg-gray-300">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
