<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Rincian Survei & Surat Perintah Survei (SPS)
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $survey->customer_display_name }} — Pesanan: {{ $survey->order?->order_number ?? '-' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if ($survey->order)
                    <a href="{{ route('tracking.show', $survey->order) }}"
                       class="px-3.5 py-2 rounded-md bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700">
                        Lacak Alur Pekerjaan
                    </a>
                @endif

                <!-- Tombol Cetak / Unduh PDF -->
                @if ($survey->approved_at && $survey->sps_number && $role !== 'client')
                    <a href="{{ route('surveys.pdf.sps', $survey) }}" target="_blank"
                       class="px-3.5 py-2 rounded-md bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 inline-flex items-center gap-1.5 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Unduh SPS (PDF)
                    </a>
                @endif

                @if ($survey->status === \App\Models\Survey::STATUS_DONE)
                    <a href="{{ route('surveys.pdf.result', $survey) }}" target="_blank"
                       class="px-3.5 py-2 rounded-md bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 inline-flex items-center gap-1.5 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Unduh Hasil Survei (PDF)
                    </a>
                @endif

                @if ($role === 'admin' && $survey->isEditableByAdmin())
                    <a href="{{ route('surveys.edit', $survey) }}"
                       class="px-3.5 py-2 rounded-md bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700">
                        {{ $survey->status === \App\Models\Survey::STATUS_REJECTED ? 'Perbaiki Pengajuan' : 'Ubah Pengajuan' }}
                    </a>
                @endif

                @if ($role === 'admin' && $survey->status === \App\Models\Survey::STATUS_DONE && ! $survey->order?->project)
                    <a href="{{ route('projects.create', ['survey_id' => $survey->id]) }}"
                       class="px-3.5 py-2 rounded-md bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700">
                        + Buat Proyek
                    </a>
                @endif

                <a href="{{ route('surveys.index') }}"
                   class="px-3.5 py-2 rounded-md border border-gray-300 text-gray-700 text-xs font-medium hover:bg-gray-50">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3 text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3 text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <!-- TAHAPAN ALUR WORKFLOW SURVEI -->
            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">
                        Alur Proses Pengajuan & Pelaksanaan Survei
                    </h3>
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $survey->status_color }}">
                        Status: {{ $survey->status_label }}
                    </span>
                </div>

                @php
                    $step1 = true;
                    $step2 = in_array($survey->status, [\App\Models\Survey::STATUS_WAITING_DIRECTOR, \App\Models\Survey::STATUS_APPROVED, \App\Models\Survey::STATUS_WAITING_EXECUTION, \App\Models\Survey::STATUS_IN_PROGRESS, \App\Models\Survey::STATUS_WAITING_RESULT, \App\Models\Survey::STATUS_DONE]);
                    $step3 = $survey->approved_at !== null;
                    $step4 = $survey->sps_issued_at !== null;
                    $step5 = in_array($survey->status, [\App\Models\Survey::STATUS_IN_PROGRESS, \App\Models\Survey::STATUS_WAITING_RESULT, \App\Models\Survey::STATUS_DONE]);
                    $step6 = in_array($survey->status, [\App\Models\Survey::STATUS_WAITING_RESULT, \App\Models\Survey::STATUS_DONE]);
                    $step7 = $survey->isSigned();
                    $step8 = $survey->status === \App\Models\Survey::STATUS_DONE;
                @endphp

                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-2 text-center text-[11px] pt-2">
                    <div class="p-2 rounded border {{ $step1 ? 'bg-blue-50 border-blue-300 text-blue-900 font-bold' : 'bg-gray-50 border-gray-200 text-gray-400' }}">
                        1. Draf Admin
                    </div>
                    <div class="p-2 rounded border {{ $step2 ? 'bg-purple-50 border-purple-300 text-purple-900 font-bold' : 'bg-gray-50 border-gray-200 text-gray-400' }}">
                        2. Ke Direktur
                    </div>
                    <div class="p-2 rounded border {{ $survey->status === \App\Models\Survey::STATUS_REJECTED ? 'bg-red-50 border-red-300 text-red-900 font-bold' : ($step3 ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'bg-gray-50 border-gray-200 text-gray-400') }}">
                        3. {{ $survey->status === \App\Models\Survey::STATUS_REJECTED ? 'Ditolak' : 'Disetujui' }}
                    </div>
                    <div class="p-2 rounded border {{ $step4 ? 'bg-blue-50 border-blue-300 text-blue-900 font-bold' : 'bg-gray-50 border-gray-200 text-gray-400' }}">
                        4. Terbit SPS
                    </div>
                    <div class="p-2 rounded border {{ $step5 ? 'bg-indigo-50 border-indigo-300 text-indigo-900 font-bold' : 'bg-gray-50 border-gray-200 text-gray-400' }}">
                        5. Pelaksanaan
                    </div>
                    <div class="p-2 rounded border {{ $step6 ? 'bg-amber-50 border-amber-300 text-amber-900 font-bold' : 'bg-gray-50 border-gray-200 text-gray-400' }}">
                        6. Form Hasil
                    </div>
                    <div class="p-2 rounded border {{ $step7 ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'bg-gray-50 border-gray-200 text-gray-400' }}">
                        7. TTD Pelanggan
                    </div>
                    <div class="p-2 rounded border {{ $step8 ? 'bg-green-100 border-green-400 text-green-900 font-bold' : 'bg-gray-50 border-gray-200 text-gray-400' }}">
                        8. Selesai
                    </div>
                </div>
            </div>

            <!-- PANEL DIREKTUR: APPROVE / REJECT -->
            @if ($role === 'direktur' && $survey->status === \App\Models\Survey::STATUS_WAITING_DIRECTOR)
                <div class="bg-white rounded-lg border-2 border-purple-400 p-6 shadow-md space-y-4">
                    <div class="border-b pb-3">
                        <span class="inline-flex px-2.5 py-0.5 rounded bg-purple-100 text-purple-800 text-xs font-bold uppercase tracking-wider">
                            Persetujuan Direktur Diperlukan
                        </span>
                        <h3 class="font-bold text-gray-900 text-lg mt-1">
                            Keputusan Persetujuan Pengajuan Survei
                        </h3>
                        <p class="text-xs text-gray-600 mt-1">
                            Tinjau seluruh rincian pengajuan di bawah ini. Anda dapat menyetujui pengajuan untuk otomatis menerbitkan <strong>Surat Perintah Survei (SPS)</strong> ke Petugas Survei, atau menolaknya dengan menyertakan alasan penolakan.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                        <!-- Form Approve -->
                        <form method="POST" action="{{ route('surveys.approve-direktur', $survey) }}" class="p-5 rounded-lg bg-emerald-50 border border-emerald-300 space-y-3">
                            @csrf
                            <div class="flex items-center gap-2 text-emerald-900 font-bold text-sm">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Pilihan 1: Setujui & Terbitkan SPS</span>
                            </div>
                            <p class="text-xs text-emerald-800 leading-relaxed">
                                Sistem akan otomatis mencatat nama Anda, tanggal, dan waktu persetujuan, serta menerbitkan Surat Perintah Survei resmi untuk <strong>{{ $survey->assignedUser?->name ?? 'Petugas Survei' }}</strong>.
                            </p>
                            <div>
                                <label class="block text-xs font-medium text-emerald-900 mb-1">Catatan Persetujuan (Opsional)</label>
                                <textarea name="approval_notes" rows="2" placeholder="Catatan atau instruksi tambahan untuk petugas survei..."
                                          class="w-full rounded-md border-emerald-300 text-xs"></textarea>
                            </div>
                            <button type="submit"
                                    onclick="return confirm('Setujui pengajuan survei ini dan terbitkan Surat Perintah Survei (SPS)?')"
                                    class="w-full py-2.5 bg-emerald-600 text-white rounded-md text-sm font-bold hover:bg-emerald-700 shadow">
                                Setujui & Terbitkan Surat Perintah Survei (SPS)
                            </button>
                        </form>

                        <!-- Form Reject -->
                        <form method="POST" action="{{ route('surveys.reject-direktur', $survey) }}" class="p-5 rounded-lg bg-red-50 border border-red-300 space-y-3">
                            @csrf
                            <div class="flex items-center gap-2 text-red-900 font-bold text-sm">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <span>Pilihan 2: Tolak / Kembalikan ke Admin</span>
                            </div>
                            <p class="text-xs text-red-800 leading-relaxed">
                                Pengajuan yang ditolak akan dikembalikan kepada Admin untuk diperbaiki sesuai catatan alasan penolakan yang Anda berikan.
                            </p>
                            <div>
                                <label class="block text-xs font-medium text-red-900 mb-1">
                                    Alasan Penolakan <span class="text-red-600">* (Wajib diisi)</span>
                                </label>
                                <textarea name="rejection_notes" rows="2" required
                                          placeholder="Jelaskan alasan penolakan (misal: jadwal bentrok, petugas perlu diganti, tujuan kurang jelas, dll.)..."
                                          class="w-full rounded-md border-red-300 text-xs"></textarea>
                            </div>
                            <button type="submit"
                                    onclick="return confirm('Tolak pengajuan survei ini dan kembalikan ke Admin?')"
                                    class="w-full py-2.5 bg-red-600 text-white rounded-md text-sm font-bold hover:bg-red-700 shadow">
                                Tolak & Kembalikan ke Admin
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- PANEL PETUGAS SURVEI: ACTION BUTTONS -->
            @if ($role === 'petugas-lapangan' && $survey->approved_at)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold text-blue-700 uppercase">Tugas Pelaksanaan Survei</span>
                        <h4 class="font-bold text-gray-900 text-base mt-0.5">
                            @if ($survey->status === \App\Models\Survey::STATUS_WAITING_EXECUTION)
                                Surat Perintah Survei Diterima — Menunggu Memulai Pelaksanaan
                            @elseif ($survey->status === \App\Models\Survey::STATUS_IN_PROGRESS)
                                Survei Sedang Berlangsung di Lokasi Pelanggan
                            @elseif ($survey->status === \App\Models\Survey::STATUS_WAITING_RESULT)
                                Pemeriksaan Lapangan Selesai — Lengkapi Form Hasil & Tanda Tangan
                            @else
                                Survei Lapangan Telah Selesai
                            @endif
                        </h4>
                        <p class="text-xs text-gray-600 mt-1">
                            Lakukan pemeriksaan fisik di lokasi pelanggan, catat temuan, dan minta pelanggan menandatangani bukti survei.
                        </p>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        @if ($survey->status === \App\Models\Survey::STATUS_WAITING_EXECUTION)
                            <form method="POST" action="{{ route('surveys.start', $survey) }}">
                                @csrf
                                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-md text-sm font-bold hover:bg-blue-700 shadow">
                                    Mulai Pelaksanaan Survei
                                </button>
                            </form>
                        @elseif ($survey->status === \App\Models\Survey::STATUS_IN_PROGRESS)
                            <form method="POST" action="{{ route('surveys.finish-field', $survey) }}">
                                @csrf
                                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-md text-sm font-bold hover:bg-indigo-700 shadow">
                                    Selesaikan Pemeriksaan & Buka Form Hasil
                                </button>
                            </form>
                        @elseif ($survey->status === \App\Models\Survey::STATUS_WAITING_RESULT)
                            <a href="{{ route('surveys.edit', $survey) }}"
                               class="px-5 py-2.5 bg-amber-600 text-white rounded-md text-sm font-bold hover:bg-amber-700 shadow">
                                Isi Form Hasil & Tanda Tangan Pelanggan
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- DOKUMEN SURAT PERINTAH SURVEI (SPS) RESMI -->
            @if ($survey->sps_number)
                <div class="bg-white rounded-lg border-2 border-blue-400 p-6 shadow-sm space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-blue-100 pb-4">
                        <div>
                            <span class="inline-flex px-3 py-1 rounded bg-blue-600 text-white text-xs font-bold uppercase tracking-wider">
                                Surat Perintah Survei (SPS) Resmi
                            </span>
                            <h3 class="text-xl font-extrabold text-gray-900 mt-2 tracking-wide">{{ $survey->sps_number }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Diterbitkan otomatis oleh sistem setelah persetujuan Direktur</p>
                        </div>

                        <div class="text-right text-xs">
                            <span class="text-gray-500">Pihak yang Menyetujui:</span>
                            <p class="font-bold text-gray-900 text-sm mt-0.5">{{ $survey->approver?->name ?? 'Direktur' }}</p>
                            <p class="text-gray-500">Tanggal: {{ $survey->approved_at?->format('d F Y, H:i') }} WIB</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                        <div class="p-3 rounded bg-blue-50/60 border border-blue-100">
                            <span class="text-gray-500 font-medium">Petugas yang Ditugaskan:</span>
                            <p class="font-bold text-gray-900 text-sm mt-1">{{ $survey->assignedUser?->name ?? '-' }}</p>
                            <p class="text-gray-500 mt-0.5">{{ $survey->assignedUser?->email ?? '' }}</p>
                        </div>

                        <div class="p-3 rounded bg-blue-50/60 border border-blue-100">
                            <span class="text-gray-500 font-medium">Jadwal Pelaksanaan:</span>
                            <p class="font-bold text-gray-900 text-sm mt-1">{{ $survey->scheduled_at?->format('d F Y, H:i') ?? '-' }} WIB</p>
                        </div>

                        <div class="p-3 rounded bg-blue-50/60 border border-blue-100">
                            <span class="text-gray-500 font-medium">Lokasi Survei:</span>
                            <p class="font-bold text-gray-900 text-sm mt-1">{{ $survey->location }}</p>
                        </div>
                    </div>

                    @if ($survey->approval_notes)
                        <div class="rounded bg-gray-50 p-3 text-xs text-gray-700 border">
                            <strong>Instruksi Direktur pada SPS:</strong>
                            <p class="mt-0.5 whitespace-pre-line">{{ $survey->approval_notes }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- CATATAN PENOLAKAN DIREKTUR -->
            @if ($survey->status === \App\Models\Survey::STATUS_REJECTED)
                <div class="bg-red-50 border-2 border-red-300 rounded-lg p-5 text-sm text-red-900 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-red-700 uppercase tracking-wider text-xs">Pengajuan Ditolak oleh Direktur</span>
                        <span class="text-xs text-red-600">{{ $survey->rejected_at?->format('d M Y, H:i') }} WIB</span>
                    </div>
                    <p class="font-semibold text-gray-900">Alasan Penolakan:</p>
                    <p class="p-3 bg-white rounded border border-red-200 text-red-800 whitespace-pre-line">
                        {{ $survey->rejection_notes ?: 'Tidak ada catatan.' }}
                    </p>
                    <p class="text-xs text-red-700">
                        Oleh: <strong>{{ $survey->rejecter?->name ?? 'Direktur' }}</strong>. Pengajuan dikembalikan kepada Admin untuk diperbaiki.
                    </p>
                </div>
            @endif

            <!-- 1. DETAIL PENGAJUAN SURVEI -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm space-y-5">
                <div class="border-b pb-3 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-base">Informasi Pengajuan Survei</h3>
                    <span class="text-xs text-gray-400">ID Survei: #{{ $survey->id }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                    <div>
                        <span class="text-gray-500">Nama Pelanggan</span>
                        <p class="font-bold text-gray-900 text-sm mt-0.5">{{ $survey->customer_display_name }}</p>
                    </div>

                    <div>
                        <span class="text-gray-500">Pesanan Terkait</span>
                        <p class="font-bold text-gray-900 text-sm mt-0.5">
                            {{ $survey->order?->order_number ?? '-' }}
                            @if ($survey->order?->event_name)
                                ({{ $survey->order->event_name }})
                            @endif
                        </p>
                    </div>

                    <div>
                        <span class="text-gray-500">Jenis Kebutuhan Survei</span>
                        <p class="font-semibold text-gray-900 text-sm mt-0.5">{{ $survey->survey_type ?: '-' }}</p>
                    </div>

                    <div>
                        <span class="text-gray-500">Dibuat Oleh</span>
                        <p class="font-semibold text-gray-900 text-sm mt-0.5">{{ $survey->requester?->name ?? 'Admin' }}</p>
                        <span class="text-[11px] text-gray-400">{{ $survey->created_at?->format('d M Y, H:i') }} WIB</span>
                    </div>

                    <div class="md:col-span-2">
                        <span class="text-gray-500">Alamat Lengkap Lokasi Survei</span>
                        <p class="font-semibold text-gray-900 text-sm mt-0.5">{{ $survey->location }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <span class="text-gray-500">Tujuan Pelaksanaan Survei</span>
                        <p class="mt-1 p-3 rounded bg-gray-50 border text-gray-800 text-xs whitespace-pre-line leading-relaxed">
                            {{ $survey->purpose ?: '-' }}
                        </p>
                    </div>

                    @if ($survey->notes)
                        <div class="md:col-span-2">
                            <span class="text-gray-500">Keterangan / Catatan Tambahan Admin</span>
                            <p class="mt-1 text-gray-700 whitespace-pre-line">{{ $survey->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 2. HASIL PEMERIKSAAN LAPANGAN -->
            @if ($survey->findings || $survey->location_condition || $survey->conclusion)
                <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm space-y-5">
                    <div class="border-b pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-gray-900 text-base">Hasil Pemeriksaan Lapangan</h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Dilaksanakan pada: {{ $survey->executed_on?->format('d F Y') ?? '-' }}
                                oleh Petugas: <strong>{{ $survey->assignedUser?->name ?? '-' }}</strong>
                            </p>
                        </div>
                        @if ($role === 'petugas-lapangan' && $survey->status === \App\Models\Survey::STATUS_WAITING_RESULT && ! $survey->isSigned())
                            <a href="{{ route('surveys.edit', $survey) }}" class="text-xs text-blue-600 font-semibold hover:underline">
                                Ubah Hasil
                            </a>
                        @endif
                    </div>

                    <div class="space-y-4 text-xs">
                        <div>
                            <span class="font-bold text-gray-700 uppercase tracking-wider text-[11px]">Hasil Pemeriksaan Fisik:</span>
                            <p class="mt-1 p-3 rounded bg-gray-50 border text-gray-800 whitespace-pre-line leading-relaxed">
                                {{ $survey->findings }}
                            </p>
                        </div>

                        <div>
                            <span class="font-bold text-gray-700 uppercase tracking-wider text-[11px]">Kondisi Lokasi:</span>
                            <p class="mt-1 p-3 rounded bg-gray-50 border text-gray-800 whitespace-pre-line leading-relaxed">
                                {{ $survey->location_condition }}
                            </p>
                        </div>

                        @if ($survey->result_notes)
                            <div>
                                <span class="font-bold text-gray-700 uppercase tracking-wider text-[11px]">Catatan Tambahan Lapangan:</span>
                                <p class="mt-1 p-3 rounded bg-gray-50 border text-gray-800 whitespace-pre-line leading-relaxed">
                                    {{ $survey->result_notes }}
                                </p>
                            </div>
                        @endif

                        <div>
                            <span class="font-bold text-gray-700 uppercase tracking-wider text-[11px]">Kesimpulan Hasil Survei:</span>
                            <p class="mt-1 p-3 rounded bg-blue-50/50 border border-blue-200 text-blue-950 font-medium whitespace-pre-line leading-relaxed">
                                {{ $survey->conclusion }}
                            </p>
                        </div>

                        @if ($survey->photos->isNotEmpty())
                            <div class="pt-2">
                                <span class="font-bold text-gray-700 uppercase tracking-wider text-[11px] block mb-2">
                                    Dokumentasi Foto Lokasi ({{ $survey->photos->count() }} Foto):
                                </span>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                    @foreach ($survey->photos as $photo)
                                        <div class="border rounded-lg overflow-hidden bg-gray-50">
                                            <a href="{{ route('surveys.photos.show', [$survey, $photo]) }}" target="_blank">
                                                <img src="{{ route('surveys.photos.show', [$survey, $photo]) }}"
                                                     alt="{{ $photo->caption ?: 'Foto Lokasi' }}"
                                                     class="w-full h-28 object-cover hover:opacity-90 transition-opacity">
                                            </a>
                                            <div class="p-1.5 text-[11px] text-gray-600 truncate">
                                                {{ $photo->caption ?: $photo->file_name }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- 3. BUKTI TANDA TANGAN DIGITAL PELANGGAN -->
            @if ($survey->isSigned())
                <div class="bg-white rounded-lg border-2 border-emerald-300 p-6 shadow-sm space-y-4">
                    <div class="border-b border-emerald-100 pb-3 flex items-center justify-between">
                        <div>
                            <span class="inline-flex px-2.5 py-0.5 rounded bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider">
                                Bukti Persetujuan Pelanggan
                            </span>
                            <h3 class="font-bold text-gray-900 text-base mt-1">Tanda Tangan Digital Pelanggan</h3>
                        </div>
                        <span class="text-xs text-gray-500">
                            Waktu: {{ $survey->signed_at?->format('d F Y, H:i:s') }} WIB
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 items-center">
                        <div class="sm:col-span-2 text-xs space-y-2">
                            <div>
                                <span class="text-gray-500">Nama Penandatangan:</span>
                                <p class="font-bold text-gray-900 text-sm mt-0.5">{{ $survey->signer_name }}</p>
                            </div>
                            @if ($survey->signer_identity)
                                <div>
                                    <span class="text-gray-500">Nomor Identitas / Telepon:</span>
                                    <p class="font-semibold text-gray-800 mt-0.5">{{ $survey->signer_identity }}</p>
                                </div>
                            @endif
                            <div class="p-2.5 rounded bg-emerald-50 border border-emerald-200 text-emerald-900 text-[11px] leading-relaxed">
                                ✓ Pelanggan menyatakan telah memeriksa dan menyetujui seluruh hasil pelaksanaan survei lapangan ini.
                            </div>
                        </div>

                        <div class="flex flex-col items-center justify-center p-3 rounded-lg bg-gray-50 border border-gray-200">
                            <span class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Tanda Tangan Tersimpan</span>
                            <img src="{{ route('surveys.signature.image', $survey) }}"
                                 alt="Tanda Tangan Pelanggan"
                                 class="max-h-24 max-w-full object-contain">
                        </div>
                    </div>

                    <!-- Prosedur Koreksi Admin -->
                    @if ($role === 'admin')
                        <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                            <span class="text-gray-500">Perlu mengubah hasil yang sudah ditandatangani?</span>
                            <button type="button" onclick="document.getElementById('modalCorrection').classList.remove('hidden')"
                                    class="text-red-600 hover:text-red-800 font-semibold underline">
                                Buka Prosedur Koreksi
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            <!-- RIWAYAT AKTIVITAS (ACTIVITY LOG) -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm space-y-4">
                <div class="border-b pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Riwayat Aktivitas Survei (Audit Log)</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Catatan transparan setiap tahapan proses survei oleh Admin, Direktur, dan Petugas Survei.</p>
                    </div>
                    <span class="text-xs text-gray-400">{{ $survey->activities->count() }} Aktivitas</span>
                </div>

                <div class="relative pl-6 space-y-4 border-l-2 border-gray-200 text-xs">
                    @forelse ($survey->activities as $act)
                        <div class="relative group">
                            <span class="absolute -left-[31px] top-1 w-3.5 h-3.5 rounded-full bg-blue-500 border-2 border-white shadow"></span>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-900 text-xs">
                                        {{ \App\Services\SurveyActivityLogger::actionLabel($act->action) }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700">
                                        {{ $act->user_name }} ({{ $act->user_role }})
                                    </span>
                                </div>
                                <span class="text-[11px] text-gray-400">
                                    {{ $act->created_at?->format('d M Y, H:i:s') }} WIB
                                </span>
                            </div>
                            <p class="text-gray-600 mt-1 leading-relaxed">{{ $act->description }}</p>
                            @if ($act->status_before || $act->status_after)
                                <div class="mt-1 text-[11px] text-gray-400">
                                    Status: <span class="font-medium text-gray-600">{{ \App\Models\Survey::labelFor($act->status_before) }}</span>
                                    &rarr; <span class="font-medium text-blue-700">{{ \App\Models\Survey::labelFor($act->status_after) }}</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-400 italic">Belum ada riwayat aktivitas yang tercatat.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL KOREKSI HASIL UNTUK ADMIN -->
    @if ($role === 'admin')
        <div id="modalCorrection" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 space-y-4">
                <h4 class="font-bold text-gray-900 text-base">Buka Koreksi Hasil Survei</h4>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Tanda tangan pelanggan saat ini akan diarsipkan ke log audit. Status survei akan dikembalikan ke tahap pengisian hasil agar Petugas Survei dapat memperbaiki temuan dan meminta tanda tangan ulang pelanggan.
                </p>
                <form method="POST" action="{{ route('surveys.open-correction', $survey) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Alasan Koreksi <span class="text-red-500">*</span>
                        </label>
                        <textarea name="correction_reason" rows="3" required
                                  placeholder="Jelaskan alasan pembukaan koreksi hasil survei..."
                                  class="w-full rounded-md border-gray-300 text-xs"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="document.getElementById('modalCorrection').classList.add('hidden')"
                                class="px-4 py-2 border rounded-md text-xs font-medium text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md text-xs font-bold hover:bg-red-700">
                            Buka Koreksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-app-layout>
