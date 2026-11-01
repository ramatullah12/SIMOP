<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Dokumentasi Lapangan Pengerjaan
            </h2>

            <div class="flex items-center gap-2">
                @if ($fieldDocumentation->file_path)
                    <a href="{{ route('field-documentations.download', $fieldDocumentation) }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md text-white text-sm font-medium hover:bg-blue-700">
                        Unduh Lampiran Bukti
                    </a>
                @endif

                <a href="{{ route('field-documentations.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-md text-gray-700 text-sm font-medium hover:bg-gray-300">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-gray-500">Nomor Dokumentasi</p>
                        <p class="font-semibold text-gray-900">{{ $fieldDocumentation->documentation_number }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Tanggal Pengerjaan</p>
                        <p class="font-semibold text-gray-900">{{ $fieldDocumentation->activity_date?->format('d M Y') ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Proyek & Kegiatan</p>
                        <p class="font-semibold text-gray-900">
                            <a href="{{ route('projects.show', $fieldDocumentation->project) }}" class="text-blue-600 hover:underline">
                                {{ $fieldDocumentation->project?->project_number }} - {{ $fieldDocumentation->project?->title }}
                            </a>
                        </p>
                        <p class="text-xs text-gray-500">{{ $fieldDocumentation->project?->order?->event_name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Diunggah Oleh (Petugas Lapangan)</p>
                        <p class="font-semibold text-gray-900">{{ $fieldDocumentation->uploader?->name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Judul Aktivitas Pengerjaan</p>
                        <p class="font-semibold text-gray-900">{{ $fieldDocumentation->title }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Kemajuan Lapangan Saat Diunggah</p>
                        <p class="font-semibold text-blue-700">{{ $fieldDocumentation->progress_percentage !== null ? $fieldDocumentation->progress_percentage . '%' : '-' }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-gray-500">Deskripsi Pengerjaan Lapangan</p>
                        <p class="font-medium text-gray-900 whitespace-pre-line mt-1">{{ $fieldDocumentation->description }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-gray-500">Catatan Tambahan</p>
                        <p class="font-medium text-gray-900 whitespace-pre-line mt-1">{{ $fieldDocumentation->notes ?: '-' }}</p>
                    </div>

                    @if ($fieldDocumentation->file_path)
                        <div class="md:col-span-2 border-t pt-4">
                            <p class="text-gray-500 mb-2">Berkas / Foto Bukti Pengerjaan Lapangan</p>
                            @if (\Illuminate\Support\Str::startsWith($fieldDocumentation->mime_type ?? '', 'image/'))
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $fieldDocumentation->file_path) }}"
                                         alt="{{ $fieldDocumentation->title }}"
                                         class="max-h-96 rounded-lg border border-gray-200 shadow-sm">
                                </div>
                            @endif
                            <p class="text-xs text-gray-600">
                                Nama Berkas: <strong>{{ $fieldDocumentation->file_name }}</strong>
                                ({{ $fieldDocumentation->file_size ? number_format($fieldDocumentation->file_size / 1024, 1) . ' KB' : '-' }})
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
