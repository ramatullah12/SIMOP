<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    @if ($role === 'direktur')
                        Persetujuan Pengajuan Survei & Surat Perintah Survei (SPS)
                    @elseif ($role === 'petugas-lapangan')
                        Tugas Pelaksanaan Survei & Surat Perintah Survei (SPS)
                    @elseif ($role === 'client')
                        Informasi Jadwal & Hasil Survei
                    @else
                        Manajemen Pengajuan Survei & Surat Perintah Survei (SPS)
                    @endif
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    @if ($role === 'admin')
                        Buat pengajuan survei berdasarkan kebutuhan pelanggan, kirim ke Direktur untuk persetujuan, dan pantau pelaksanaan survei.
                    @elseif ($role === 'direktur')
                        Tinjau detail pengajuan survei dari Admin, berikan keputusan (Setujui atau Tolak disertai alasan), dan terbitkan SPS.
                    @elseif ($role === 'petugas-lapangan')
                        Daftar tugas survei yang telah disetujui Direktur. Laksanakan survei di lokasi dan isi form hasil serta tanda tangan pelanggan.
                    @else
                        Pantau status pelaksanaan survei untuk pesanan kegiatan Anda.
                    @endif
                </p>
            </div>

            @if ($role === 'admin')
                <a href="{{ route('surveys.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md font-medium text-sm text-white hover:bg-blue-700">
                    + Buat Pengajuan Survei
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">
            @if (session('success'))
                <div class="rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filter Status -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <form method="GET" action="{{ route('surveys.index') }}" class="flex flex-wrap items-center gap-2">
                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Saring Status:</label>
                    <select name="status" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm py-1.5 px-3">
                        <option value="">Semua Status</option>
                        @foreach ($statusOptions as $val => $lbl)
                            <option value="{{ $val }}" @selected($statusFilter === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @if ($statusFilter)
                        <a href="{{ route('surveys.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline ml-1">
                            Atur Ulang
                        </a>
                    @endif
                </form>

                <div class="text-xs text-gray-500">
                    Menampilkan total <strong>{{ $surveys->total() }}</strong> data survei
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">No. SPS / Dokumen</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Pelanggan & Kegiatan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Jenis & Tujuan Survei</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Petugas Survei</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Jadwal & Lokasi</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Status Alur</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($surveys as $survey)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3 align-top">
                                        @if ($survey->sps_number)
                                            <p class="font-bold text-blue-700 text-xs tracking-wide">{{ $survey->sps_number }}</p>
                                            <span class="text-[11px] text-gray-500">Terbit: {{ $survey->sps_issued_at?->format('d/m/Y') ?? $survey->approved_at?->format('d/m/Y') }}</span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs text-gray-500 bg-gray-100 italic">
                                                SPS Belum Terbit
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <p class="font-semibold text-gray-900">{{ $survey->customer_display_name }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            Pesanan: {{ $survey->order?->order_number ?? '-' }}
                                            @if ($survey->order?->event_name)
                                                ({{ $survey->order->event_name }})
                                            @endif
                                        </p>
                                    </td>
                                    <td class="px-4 py-3 align-top max-w-xs">
                                        <p class="font-medium text-gray-800 text-xs">{{ $survey->survey_type ?: 'Survei Lapangan' }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2" title="{{ $survey->purpose }}">
                                            {{ $survey->purpose ?: '-' }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        @if ($survey->assignedUser)
                                            <p class="font-medium text-gray-900">{{ $survey->assignedUser->name }}</p>
                                            @if (! $survey->approved_at)
                                                <span class="text-[11px] text-amber-600">(Menunggu Persetujuan Direktur)</span>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-400 italic">Belum ditentukan</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <p class="font-medium text-gray-900">{{ $survey->scheduled_at?->format('d M Y, H:i') ?? 'Belum dijadwalkan' }}</p>
                                        <p class="text-xs text-gray-500 line-clamp-1" title="{{ $survey->location }}">{{ $survey->location }}</p>
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $survey->status_color }}">
                                            {{ $survey->status_label }}
                                        </span>
                                        @if ($survey->status === \App\Models\Survey::STATUS_REJECTED && $survey->rejection_notes)
                                            <p class="text-xs text-red-600 mt-1 line-clamp-1" title="{{ $survey->rejection_notes }}">
                                                Alasan: {{ $survey->rejection_notes }}
                                            </p>
                                        @endif
                                        @if ($survey->isSigned())
                                            <span class="block text-[11px] text-emerald-700 font-medium mt-1">
                                                ✓ TTD Pelanggan ({{ $survey->signer_name }})
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 align-top text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            @if ($role === 'direktur' && $survey->status === \App\Models\Survey::STATUS_WAITING_DIRECTOR)
                                                <a href="{{ route('surveys.show', $survey) }}"
                                                   class="px-3 py-1 bg-purple-600 text-white rounded text-xs font-semibold hover:bg-purple-700">
                                                    Proses Persetujuan
                                                </a>
                                            @elseif ($role === 'petugas-lapangan')
                                                @if ($survey->status === \App\Models\Survey::STATUS_WAITING_EXECUTION)
                                                    <a href="{{ route('surveys.show', $survey) }}"
                                                       class="px-2.5 py-1 bg-blue-600 text-white rounded text-xs font-semibold hover:bg-blue-700">
                                                        Buka SPS & Mulai
                                                    </a>
                                                @elseif ($survey->status === \App\Models\Survey::STATUS_IN_PROGRESS)
                                                    <a href="{{ route('surveys.show', $survey) }}"
                                                       class="px-2.5 py-1 bg-indigo-600 text-white rounded text-xs font-semibold hover:bg-indigo-700">
                                                        Selesaikan Lapangan
                                                    </a>
                                                @elseif ($survey->status === \App\Models\Survey::STATUS_WAITING_RESULT)
                                                    <a href="{{ route('surveys.edit', $survey) }}"
                                                       class="px-2.5 py-1 bg-amber-600 text-white rounded text-xs font-semibold hover:bg-amber-700">
                                                        Isi Hasil & TTD
                                                    </a>
                                                @else
                                                    <a href="{{ route('surveys.show', $survey) }}"
                                                       class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium hover:bg-gray-200">
                                                        Rincian
                                                    </a>
                                                @endif
                                            @elseif ($role === 'admin' && $survey->isEditableByAdmin())
                                                <a href="{{ route('surveys.edit', $survey) }}"
                                                   class="px-2.5 py-1 bg-blue-600 text-white rounded text-xs font-medium hover:bg-blue-700">
                                                    {{ $survey->status === \App\Models\Survey::STATUS_REJECTED ? 'Perbaiki' : 'Ubah' }}
                                                </a>
                                                <a href="{{ route('surveys.show', $survey) }}"
                                                   class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium hover:bg-gray-200">
                                                    Rincian
                                                </a>
                                            @else
                                                <a href="{{ route('surveys.show', $survey) }}"
                                                   class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium hover:bg-gray-200">
                                                    Rincian
                                                </a>
                                            @endif

                                            @if ($survey->sps_number && $role !== 'client')
                                                <a href="{{ route('surveys.pdf.sps', $survey) }}" target="_blank"
                                                   class="p-1 text-gray-500 hover:text-blue-600" title="Unduh SPS PDF">
                                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </a>
                                            @endif

                                            @if ($survey->status === \App\Models\Survey::STATUS_DONE && $role !== 'client')
                                                <a href="{{ route('surveys.pdf.result', $survey) }}" target="_blank"
                                                   class="p-1 text-gray-500 hover:text-emerald-600" title="Unduh Hasil Survei PDF">
                                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        Tidak ada data survei yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($surveys->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $surveys->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
