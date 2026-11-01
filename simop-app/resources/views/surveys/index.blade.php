<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight sm:text-2xl">
                    @if ($role === 'direktur')
                        Persetujuan Pengajuan Survei & SPS
                    @elseif ($role === 'petugas-lapangan')
                        Tugas Pelaksanaan Survei & SPS
                    @elseif ($role === 'client')
                        Informasi Jadwal & Riwayat Survei
                    @else
                        Manajemen Pengajuan Survei & SPS
                    @endif
                </h2>
                <p class="text-xs text-slate-500 mt-1 sm:text-sm">
                    @if ($role === 'admin')
                        Buat pengajuan survei pelanggan, kirim ke Direktur untuk persetujuan, dan pantau hasil pemeriksaan lapangan.
                    @elseif ($role === 'direktur')
                        Tinjau detail pengajuan survei dari Admin, berikan persetujuan untuk otomatis menerbitkan SPS, atau tolak disertai alasan.
                    @elseif ($role === 'petugas-lapangan')
                        Daftar tugas survei yang disetujui Direktur. Laksanakan survei di lokasi dan lengkapi bukti tanda tangan digital pelanggan.
                    @else
                        Pantau status pelaksanaan survei untuk pesanan kegiatan Anda secara transparan.
                    @endif
                </p>
            </div>

            @if ($role === 'admin')
                <div>
                    <a href="{{ route('surveys.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 shadow-md hover:shadow-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Pengajuan Survei
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 shadow-sm flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="flex-1 text-sm text-emerald-800">
                        <p class="font-bold">Berhasil</p>
                        <p class="mt-0.5 text-xs text-emerald-700">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 shadow-sm flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="flex-1 text-sm text-rose-800">
                        <p class="font-bold">Perhatian</p>
                        <p class="mt-0.5 text-xs text-rose-700">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            {{-- Filter Bar Modern --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <form method="GET" action="{{ route('surveys.index') }}" class="flex flex-wrap items-center gap-2.5">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Saring Status:</span>
                    <select name="status" onchange="this.form.submit()" class="rounded-xl border-slate-300 text-xs font-semibold py-2 px-3 focus:ring-blue-500 focus:border-blue-500 shadow-sm text-slate-700">
                        <option value="">Semua Status ({{ $surveys->total() }})</option>
                        @foreach ($statusOptions as $val => $lbl)
                            <option value="{{ $val }}" @selected($statusFilter === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @if ($statusFilter)
                        <a href="{{ route('surveys.index') }}" class="text-xs text-rose-600 hover:text-rose-800 font-semibold underline ml-1">
                            Reset Filter
                        </a>
                    @endif
                </form>

                <div class="text-xs text-slate-500">
                    Total: <strong class="text-slate-800">{{ $surveys->total() }}</strong> survei ditemukan
                </div>
            </div>

            {{-- Tabel Data Survei --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">No. Dokumen / SPS</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Pelanggan & Pesanan</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kebutuhan & Lokasi</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Petugas & Jadwal</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status Alur</th>
                                <th class="px-5 py-3.5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($surveys as $survey)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    {{-- No. Dokumen / SPS --}}
                                    <td class="px-5 py-4 align-top">
                                        @if ($survey->sps_number)
                                            <div class="font-bold font-mono text-blue-700 text-xs tracking-wide">
                                                {{ $survey->sps_number }}
                                            </div>
                                            <span class="text-[11px] text-slate-400 block mt-0.5">
                                                Terbit: {{ $survey->sps_issued_at?->format('d/m/Y') ?? $survey->approved_at?->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold text-slate-500 bg-slate-100">
                                                SPS Belum Terbit
                                            </span>
                                            <span class="text-[10px] text-slate-400 block mt-0.5">ID: #{{ $survey->id }}</span>
                                        @endif
                                    </td>

                                    {{-- Pelanggan & Pesanan --}}
                                    <td class="px-5 py-4 align-top">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-800 font-extrabold text-xs flex items-center justify-center flex-shrink-0">
                                                {{ strtoupper(substr($survey->customer_display_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 text-sm">{{ $survey->customer_display_name }}</p>
                                                <p class="text-xs text-slate-500 mt-0.5">
                                                    {{ $survey->order?->order_number ?? 'Pesanan -' }}
                                                    @if ($survey->order?->event_name)
                                                        &bull; {{ $survey->order->event_name }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kebutuhan & Lokasi --}}
                                    <td class="px-5 py-4 align-top max-w-xs">
                                        <p class="font-bold text-slate-800 text-xs">{{ $survey->survey_type ?: 'Survei Lapangan' }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5 line-clamp-1" title="{{ $survey->location }}">
                                            📍 {{ $survey->location }}
                                        </p>
                                    </td>

                                    {{-- Petugas & Jadwal --}}
                                    <td class="px-5 py-4 align-top">
                                        <p class="font-bold text-slate-800 text-xs">
                                            {{ $survey->assignedUser?->name ?? 'Belum Ditentukan' }}
                                        </p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">
                                            🗓 {{ $survey->scheduled_at?->format('d M Y, H:i') ?? 'Belum Terjadwal' }}
                                        </p>
                                    </td>

                                    {{-- Status Alur --}}
                                    <td class="px-5 py-4 align-top">
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $survey->status_color }}">
                                            {{ $survey->status_label }}
                                        </span>
                                        @if ($survey->status === \App\Models\Survey::STATUS_REJECTED && $survey->rejection_notes)
                                            <p class="text-[11px] text-rose-600 mt-1 line-clamp-1" title="{{ $survey->rejection_notes }}">
                                                Alasan: {{ $survey->rejection_notes }}
                                            </p>
                                        @endif
                                        @if ($survey->isSigned())
                                            <span class="block text-[11px] text-emerald-700 font-semibold mt-1">
                                                ✓ TTD Pelanggan Sah
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-5 py-4 align-top text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            @if ($role === 'direktur' && $survey->status === \App\Models\Survey::STATUS_WAITING_DIRECTOR)
                                                <a href="{{ route('surveys.show', $survey) }}"
                                                   class="px-3.5 py-1.5 bg-purple-600 text-white rounded-lg text-xs font-bold hover:bg-purple-700 shadow-sm transition">
                                                    Review & Keputusan
                                                </a>
                                            @elseif ($role === 'petugas-lapangan')
                                                @if ($survey->status === \App\Models\Survey::STATUS_WAITING_EXECUTION)
                                                    <a href="{{ route('surveys.show', $survey) }}"
                                                       class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 shadow-sm transition">
                                                        Buka SPS & Mulai
                                                    </a>
                                                @elseif ($survey->status === \App\Models\Survey::STATUS_IN_PROGRESS)
                                                    <a href="{{ route('surveys.show', $survey) }}"
                                                       class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700 shadow-sm transition">
                                                        Selesaikan & Isi
                                                    </a>
                                                @elseif ($survey->status === \App\Models\Survey::STATUS_WAITING_RESULT)
                                                    <a href="{{ route('surveys.edit', $survey) }}"
                                                       class="px-3 py-1.5 bg-amber-600 text-white rounded-lg text-xs font-bold hover:bg-amber-700 shadow-sm transition">
                                                        Isi Hasil & TTD
                                                    </a>
                                                @else
                                                    <a href="{{ route('surveys.show', $survey) }}"
                                                       class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg text-xs font-semibold hover:bg-slate-200 transition">
                                                        Rincian
                                                    </a>
                                                @endif
                                            @elseif ($role === 'admin' && $survey->isEditableByAdmin())
                                                <a href="{{ route('surveys.edit', $survey) }}"
                                                   class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 shadow-sm transition">
                                                    {{ $survey->status === \App\Models\Survey::STATUS_REJECTED ? 'Perbaiki' : 'Ubah' }}
                                                </a>
                                                <a href="{{ route('surveys.show', $survey) }}"
                                                   class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg text-xs font-semibold hover:bg-slate-200 transition">
                                                    Rincian
                                                </a>
                                            @else
                                                <a href="{{ route('surveys.show', $survey) }}"
                                                   class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg text-xs font-semibold hover:bg-slate-200 transition">
                                                    Rincian
                                                </a>
                                            @endif

                                            @if ($survey->sps_number && $role !== 'client')
                                                <a href="{{ route('surveys.pdf.sps', $survey) }}" target="_blank"
                                                   class="p-1.5 text-slate-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition" title="Unduh SPS (PDF)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </a>
                                            @endif

                                            @if ($survey->status === \App\Models\Survey::STATUS_DONE && $role !== 'client')
                                                <a href="{{ route('surveys.pdf.result', $survey) }}" target="_blank"
                                                   class="p-1.5 text-slate-400 hover:text-emerald-600 rounded-lg hover:bg-emerald-50 transition" title="Unduh Hasil Survei (PDF)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <h4 class="font-bold text-slate-800 text-sm">Tidak Ada Data Survei</h4>
                                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                            Tidak ditemukan data survei sesuai filter status yang dipilih.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($surveys->hasPages())
                    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                        {{ $surveys->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
