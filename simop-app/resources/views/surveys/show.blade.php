<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-blue-600">
                    <a href="{{ route('surveys.index') }}" class="hover:underline">Survei</a>
                    <span>/</span>
                    <span class="text-slate-500">{{ $survey->sps_number ?: ('SRV-' . str_pad($survey->id, 4, '0', STR_PAD_LEFT)) }}</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight sm:text-2xl">
                    Rincian Survei & Surat Perintah Survei (SPS)
                </h2>
                <p class="text-xs text-slate-500 sm:text-sm">
                    {{ $survey->customer_display_name }} &bull; Pesanan: <span class="font-semibold text-slate-700">{{ $survey->order?->order_number ?? '-' }}</span>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if ($survey->order)
                    <a href="{{ route('tracking.show', $survey->order) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-slate-800 text-white text-xs font-semibold hover:bg-slate-900 shadow-sm transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Lacak Alur Pekerjaan
                    </a>
                @endif

                {{-- Tombol Unduh Dokumen Resmi PDF --}}
                @if ($survey->approved_at && $survey->sps_number && $role !== 'client')
                    <a href="{{ route('surveys.pdf.sps', $survey) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 shadow-sm transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Unduh SPS (PDF)
                    </a>
                @endif

                @if ($survey->status === \App\Models\Survey::STATUS_DONE)
                    <a href="{{ route('surveys.pdf.result', $survey) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 shadow-sm transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Unduh Hasil Survei (PDF)
                    </a>
                @endif

                @if ($role === 'admin' && $survey->isEditableByAdmin())
                    <a href="{{ route('surveys.edit', $survey) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700 shadow-sm transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{ $survey->status === \App\Models\Survey::STATUS_REJECTED ? 'Perbaiki Pengajuan' : 'Ubah Pengajuan' }}
                    </a>
                @endif

                @if ($role === 'admin' && $survey->status === \App\Models\Survey::STATUS_DONE && ! $survey->order?->project)
                    <a href="{{ route('projects.create', ['survey_id' => $survey->id]) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-teal-600 text-white text-xs font-bold hover:bg-teal-700 shadow-sm transition">
                        + Buat Proyek
                    </a>
                @endif

                <a href="{{ route('surveys.index') }}"
                   class="inline-flex items-center gap-1 px-3.5 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 shadow-sm transition">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Notifications --}}
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

            {{-- WORKFLOW PROGRESS TRACKER 8 TAHAPAN --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-3 border-b border-slate-100 gap-2">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">
                            Alur Tahapan Pengajuan & Pelaksanaan Survei
                        </h3>
                        <p class="text-xs text-slate-600 mt-0.5">Pemantauan end-to-end dari inisiasi Admin hingga penyelesaian laporan.</p>
                    </div>
                    <div>
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $survey->status_color }}">
                            {{ $survey->status_label }}
                        </span>
                    </div>
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

                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-2 text-center text-[11px] pt-1">
                    {{-- 1 --}}
                    <div class="p-2 rounded-xl border flex flex-col items-center justify-between {{ $step1 ? 'bg-blue-50 border-blue-200 text-blue-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                        <span class="w-5 h-5 rounded-full {{ $step1 ? 'bg-blue-600 text-white' : 'bg-slate-300 text-slate-600' }} text-[10px] flex items-center justify-center mb-1">1</span>
                        <span>Draf Admin</span>
                    </div>

                    {{-- 2 --}}
                    <div class="p-2 rounded-xl border flex flex-col items-center justify-between {{ $step2 ? 'bg-purple-50 border-purple-200 text-purple-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                        <span class="w-5 h-5 rounded-full {{ $step2 ? 'bg-purple-600 text-white' : 'bg-slate-300 text-slate-600' }} text-[10px] flex items-center justify-center mb-1">2</span>
                        <span>Ke Direktur</span>
                    </div>

                    {{-- 3 --}}
                    <div class="p-2 rounded-xl border flex flex-col items-center justify-between {{ $survey->status === \App\Models\Survey::STATUS_REJECTED ? 'bg-rose-50 border-rose-300 text-rose-900 font-bold' : ($step3 ? 'bg-emerald-50 border-emerald-200 text-emerald-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-400') }}">
                        <span class="w-5 h-5 rounded-full {{ $survey->status === \App\Models\Survey::STATUS_REJECTED ? 'bg-rose-600 text-white' : ($step3 ? 'bg-emerald-600 text-white' : 'bg-slate-300 text-slate-600') }} text-[10px] flex items-center justify-center mb-1">3</span>
                        <span>{{ $survey->status === \App\Models\Survey::STATUS_REJECTED ? 'Ditolak' : 'Disetujui' }}</span>
                    </div>

                    {{-- 4 --}}
                    <div class="p-2 rounded-xl border flex flex-col items-center justify-between {{ $step4 ? 'bg-blue-50 border-blue-200 text-blue-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                        <span class="w-5 h-5 rounded-full {{ $step4 ? 'bg-blue-600 text-white' : 'bg-slate-300 text-slate-600' }} text-[10px] flex items-center justify-center mb-1">4</span>
                        <span>Terbit SPS</span>
                    </div>

                    {{-- 5 --}}
                    <div class="p-2 rounded-xl border flex flex-col items-center justify-between {{ $step5 ? 'bg-indigo-50 border-indigo-200 text-indigo-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                        <span class="w-5 h-5 rounded-full {{ $step5 ? 'bg-indigo-600 text-white' : 'bg-slate-300 text-slate-600' }} text-[10px] flex items-center justify-center mb-1">5</span>
                        <span>Pelaksanaan</span>
                    </div>

                    {{-- 6 --}}
                    <div class="p-2 rounded-xl border flex flex-col items-center justify-between {{ $step6 ? 'bg-amber-50 border-amber-200 text-amber-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                        <span class="w-5 h-5 rounded-full {{ $step6 ? 'bg-amber-500 text-white' : 'bg-slate-300 text-slate-600' }} text-[10px] flex items-center justify-center mb-1">6</span>
                        <span>Form Hasil</span>
                    </div>

                    {{-- 7 --}}
                    <div class="p-2 rounded-xl border flex flex-col items-center justify-between {{ $step7 ? 'bg-teal-50 border-teal-200 text-teal-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                        <span class="w-5 h-5 rounded-full {{ $step7 ? 'bg-teal-600 text-white' : 'bg-slate-300 text-slate-600' }} text-[10px] flex items-center justify-center mb-1">7</span>
                        <span>TTD Klien</span>
                    </div>

                    {{-- 8 --}}
                    <div class="p-2 rounded-xl border flex flex-col items-center justify-between {{ $step8 ? 'bg-emerald-100 border-emerald-300 text-emerald-950 font-bold' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                        <span class="w-5 h-5 rounded-full {{ $step8 ? 'bg-emerald-600 text-white' : 'bg-slate-300 text-slate-600' }} text-[10px] flex items-center justify-center mb-1">8</span>
                        <span>Selesai</span>
                    </div>
                </div>
            </div>

            {{-- PANEL DIREKTUR: APPROVE / REJECT --}}
            @if ($role === 'direktur' && $survey->status === \App\Models\Survey::STATUS_WAITING_DIRECTOR)
                <div class="bg-white rounded-2xl border-2 border-purple-400 p-6 shadow-xl space-y-5">
                    <div class="border-b border-purple-100 pb-3 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-bold uppercase tracking-wider">
                                Tindakan Direktur
                            </span>
                            <h3 class="font-extrabold text-slate-900 text-lg mt-0.5">
                                Keputusan Persetujuan Pengajuan Survei
                            </h3>
                            <p class="text-xs text-slate-500">
                                Tinjau rincian pengajuan. Pilih <strong>Setujui</strong> untuk otomatis menerbitkan Surat Perintah Survei (SPS), atau <strong>Tolak</strong> disertai alasan penolakan.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-1">
                        {{-- Form Approve --}}
                        <form method="POST" action="{{ route('surveys.approve-direktur', $survey) }}" class="p-5 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-300 space-y-4 shadow-sm flex flex-col justify-between">
                            @csrf
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-emerald-900 font-bold text-sm">
                                    <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs">✓</div>
                                    <span>Pilihan 1: Setujui & Terbitkan SPS</span>
                                </div>
                                <p class="text-xs text-emerald-800 leading-relaxed">
                                    Sistem otomatis mencatat nama Anda, tanggal, dan waktu approval, serta menerbitkan SPS resmi untuk petugas lapangan: <strong>{{ $survey->assignedUser?->name ?? 'Petugas Lapangan' }}</strong>.
                                </p>
                                <div>
                                    <label class="block text-xs font-semibold text-emerald-900 mb-1">Catatan Instruksi Direktur (Opsional)</label>
                                    <textarea name="approval_notes" rows="2" placeholder="Catatan instruksi teknis untuk petugas survei..."
                                              class="w-full rounded-xl border-emerald-300 text-xs focus:ring-emerald-500 focus:border-emerald-500 shadow-sm"></textarea>
                                </div>
                            </div>
                            <button type="submit"
                                    onclick="return confirm('Apakah Anda yakin ingin menyetujui pengajuan survei ini? Surat Perintah Survei (SPS) akan langsung diterbitkan.')"
                                    class="w-full py-3 bg-emerald-600 text-white rounded-xl text-sm font-extrabold hover:bg-emerald-700 shadow-md hover:shadow-lg transition">
                                Setujui & Terbitkan Surat Perintah Survei (SPS)
                            </button>
                        </form>

                        {{-- Form Reject --}}
                        <form method="POST" action="{{ route('surveys.reject-direktur', $survey) }}" class="p-5 rounded-2xl bg-gradient-to-br from-rose-50 to-pink-50 border border-rose-300 space-y-4 shadow-sm flex flex-col justify-between">
                            @csrf
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-rose-900 font-bold text-sm">
                                    <div class="w-6 h-6 rounded-full bg-rose-600 text-white flex items-center justify-center text-xs">&times;</div>
                                    <span>Pilihan 2: Tolak / Kembalikan ke Admin</span>
                                </div>
                                <p class="text-xs text-rose-800 leading-relaxed">
                                    Pengajuan yang ditolak akan dikembalikan ke dashboard Admin untuk diperbaiki sesuai alasan yang Anda cantumkan di bawah ini.
                                </p>
                                <div>
                                    <label class="block text-xs font-semibold text-rose-900 mb-1">
                                        Alasan Penolakan <span class="text-rose-600">* (Wajib diisi)</span>
                                    </label>
                                    <textarea name="rejection_notes" rows="2" required
                                              placeholder="Jelaskan alasan penolakan (misal: jadwal bentrok dengan agenda lain, petugas survei perlu diganti, atau informasi lokasi belum lengkap)..."
                                              class="w-full rounded-xl border-rose-300 text-xs focus:ring-rose-500 focus:border-rose-500 shadow-sm"></textarea>
                                </div>
                            </div>
                            <button type="submit"
                                    onclick="return confirm('Apakah Anda yakin ingin menolak pengajuan survei ini dan mengembalikannya ke Admin?')"
                                    class="w-full py-3 bg-rose-600 text-white rounded-xl text-sm font-extrabold hover:bg-rose-700 shadow-md hover:shadow-lg transition">
                                Tolak & Kembalikan ke Admin
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- PANEL PETUGAS SURVEI: ACTION BANNER --}}
            @if ($role === 'petugas-lapangan' && $survey->approved_at)
                <div class="bg-gradient-to-r from-blue-900 to-indigo-900 rounded-2xl p-6 text-white shadow-xl flex flex-col sm:flex-row items-center justify-between gap-5">
                    <div class="space-y-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-blue-300">Tugas Lapangan Anda</span>
                        <h4 class="font-extrabold text-white text-lg">
                            @if ($survey->status === \App\Models\Survey::STATUS_WAITING_EXECUTION)
                                SPS Telah Diterbitkan &bull; Siap Dilaksanakan
                            @elseif ($survey->status === \App\Models\Survey::STATUS_IN_PROGRESS)
                                Survei Sedang Berlangsung di Lokasi
                            @elseif ($survey->status === \App\Models\Survey::STATUS_WAITING_RESULT)
                                Pemeriksaan Lapangan Selesai &bull; Lengkapi Hasil & Tanda Tangan
                            @else
                                Survei Lapangan Telah Selesai
                            @endif
                        </h4>
                        <p class="text-xs text-blue-200">
                            Lakukan pemeriksaan langsung di lokasi pelanggan, catat seluruh temuan fisik, dan minta pelanggan menandatangani form.
                        </p>
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        @if ($survey->status === \App\Models\Survey::STATUS_WAITING_EXECUTION)
                            <form method="POST" action="{{ route('surveys.start', $survey) }}">
                                @csrf
                                <button type="submit"
                                        class="px-6 py-3 bg-blue-500 hover:bg-blue-400 text-white rounded-xl text-sm font-bold shadow-lg transition transform hover:-translate-y-0.5">
                                    Mulai Pelaksanaan Survei
                                </button>
                            </form>
                        @elseif ($survey->status === \App\Models\Survey::STATUS_IN_PROGRESS)
                            <form method="POST" action="{{ route('surveys.finish-field', $survey) }}">
                                @csrf
                                <button type="submit"
                                        class="px-6 py-3 bg-indigo-500 hover:bg-indigo-400 text-white rounded-xl text-sm font-bold shadow-lg transition transform hover:-translate-y-0.5">
                                    Selesaikan Pemeriksaan & Buka Form Hasil
                                </button>
                            </form>
                        @elseif ($survey->status === \App\Models\Survey::STATUS_WAITING_RESULT)
                            <a href="{{ route('surveys.edit', $survey) }}"
                               class="inline-flex items-center gap-2 px-6 py-3 bg-amber-500 hover:bg-amber-400 text-slate-900 rounded-xl text-sm font-extrabold shadow-lg transition transform hover:-translate-y-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                                Isi Form Hasil & Tanda Tangan Pelanggan
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- DOKUMEN SURAT PERINTAH SURVEI (SPS) RESMI --}}
            @if ($survey->sps_number)
                <div class="bg-white rounded-2xl border-2 border-blue-200 p-6 shadow-md space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-blue-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold shadow">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="px-2.5 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] font-bold uppercase tracking-wider">
                                    Dokumen Perintah Resmi
                                </span>
                                <h3 class="text-xl font-extrabold text-slate-900 font-mono tracking-wide mt-0.5">
                                    {{ $survey->sps_number }}
                                </h3>
                                <p class="text-xs text-slate-500">Diterbitkan otomatis setelah persetujuan Direktur.</p>
                            </div>
                        </div>

                        <div class="text-xs sm:text-right">
                            <span class="text-slate-400 block font-medium">Disetujui Oleh</span>
                            <p class="font-extrabold text-slate-900 text-sm mt-0.5">{{ $survey->approver?->name ?? 'Direktur' }}</p>
                            <span class="text-slate-500 font-medium">{{ $survey->approved_at?->format('d F Y, H:i') }} WIB</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-400 font-bold uppercase block">Petugas Ditugaskan</span>
                            <p class="font-bold text-slate-900 text-sm mt-1">{{ $survey->assignedUser?->name ?? '-' }}</p>
                            <p class="text-slate-500 mt-0.5">{{ $survey->assignedUser?->email ?? '' }}</p>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-400 font-bold uppercase block">Jadwal Pelaksanaan</span>
                            <p class="font-bold text-slate-900 text-sm mt-1">
                                {{ $survey->scheduled_at?->format('d F Y, H:i') ?? 'Sesuai Arahan' }} WIB
                            </p>
                            <p class="text-slate-500 mt-0.5">Dikoordinasikan dengan pelanggan</p>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-400 font-bold uppercase block">Lokasi Survei</span>
                            <p class="font-bold text-slate-900 text-sm mt-1 line-clamp-2">{{ $survey->location }}</p>
                        </div>
                    </div>

                    @if ($survey->approval_notes)
                        <div class="rounded-xl bg-blue-50/60 p-4 text-xs text-blue-900 border border-blue-200">
                            <span class="font-bold uppercase tracking-wider block text-[11px] text-blue-800">Catatan Khusus Direktur:</span>
                            <p class="mt-1 leading-relaxed whitespace-pre-line">{{ $survey->approval_notes }}</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- CATATAN PENOLAKAN DIREKTUR JIKA ADA --}}
            @if ($survey->status === \App\Models\Survey::STATUS_REJECTED)
                <div class="bg-rose-50 border-2 border-rose-300 rounded-2xl p-6 text-sm text-rose-900 space-y-3 shadow-sm">
                    <div class="flex items-center justify-between pb-2 border-b border-rose-200">
                        <span class="font-bold text-rose-700 uppercase tracking-wider text-xs flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Pengajuan Survei Ditolak oleh Direktur
                        </span>
                        <span class="text-xs text-rose-600 font-medium">{{ $survey->rejected_at?->format('d M Y, H:i') }} WIB</span>
                    </div>
                    <p class="font-bold text-rose-950 text-xs uppercase">Alasan Penolakan:</p>
                    <div class="p-4 bg-white rounded-xl border border-rose-200 text-rose-900 text-xs leading-relaxed whitespace-pre-line shadow-inner">
                        {{ $survey->rejection_notes ?: 'Tidak ada catatan alasan penolakan.' }}
                    </div>
                    <p class="text-xs text-rose-700">
                        Diputuskan oleh: <strong>{{ $survey->rejecter?->name ?? 'Direktur' }}</strong>. Pengajuan telah dikembalikan ke akun Admin untuk diperbaiki.
                    </p>
                </div>
            @endif

            {{-- INFORMASI LENGKAP PENGAJUAN SURVEI --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Informasi Pengajuan Survei</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Detail kebutuhan survei yang dibuat oleh Admin.</p>
                    </div>
                    <span class="text-xs font-mono text-slate-400">ID: #{{ $survey->id }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                    <div>
                        <span class="text-slate-400 font-bold uppercase block">Nama Pelanggan</span>
                        <p class="font-extrabold text-slate-900 text-sm mt-0.5">{{ $survey->customer_display_name }}</p>
                    </div>

                    <div>
                        <span class="text-slate-400 font-bold uppercase block">Pesanan Terkait</span>
                        <p class="font-extrabold text-slate-900 text-sm mt-0.5">
                            {{ $survey->order?->order_number ?? '-' }}
                            @if ($survey->order?->event_name)
                                <span class="text-xs font-normal text-slate-500">({{ $survey->order->event_name }})</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <span class="text-slate-400 font-bold uppercase block">Jenis Kebutuhan Survei</span>
                        <p class="font-semibold text-slate-800 text-sm mt-0.5">{{ $survey->survey_type ?: 'Survei Kebutuhan Umum' }}</p>
                    </div>

                    <div>
                        <span class="text-slate-400 font-bold uppercase block">Dibuat Oleh</span>
                        <p class="font-semibold text-slate-800 text-sm mt-0.5">{{ $survey->requester?->name ?? 'Admin Operasional' }}</p>
                        <span class="text-[11px] text-slate-400">{{ $survey->created_at?->format('d M Y, H:i') }} WIB</span>
                    </div>

                    <div class="md:col-span-2">
                        <span class="text-slate-400 font-bold uppercase block">Alamat Lengkap Lokasi Survei</span>
                        <p class="font-medium text-slate-800 text-sm mt-0.5 leading-relaxed">{{ $survey->location }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <span class="text-slate-400 font-bold uppercase block">Tujuan Pelaksanaan Survei</span>
                        <div class="mt-1 p-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-xs whitespace-pre-line leading-relaxed">
                            {{ $survey->purpose ?: '-' }}
                        </div>
                    </div>

                    @if ($survey->notes)
                        <div class="md:col-span-2">
                            <span class="text-slate-400 font-bold uppercase block">Catatan Tambahan Admin</span>
                            <p class="mt-1 text-slate-700 leading-relaxed whitespace-pre-line">{{ $survey->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- HASIL PEMERIKSAAN LAPANGAN --}}
            @if ($survey->findings || $survey->location_condition || $survey->conclusion)
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 text-base">Hasil Pemeriksaan Lapangan</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Dilaksanakan pada: <strong>{{ $survey->executed_on?->format('d F Y') ?? '-' }}</strong>
                                &bull; Petugas: <strong>{{ $survey->assignedUser?->name ?? '-' }}</strong>
                            </p>
                        </div>
                        @if ($role === 'petugas-lapangan' && $survey->status === \App\Models\Survey::STATUS_WAITING_RESULT && ! $survey->isSigned())
                            <a href="{{ route('surveys.edit', $survey) }}" class="text-xs font-bold text-blue-600 hover:underline">
                                Edit Hasil Lapangan
                            </a>
                        @endif
                    </div>

                    <div class="space-y-4 text-xs">
                        <div>
                            <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px] block mb-1">Hasil Temuan Fisik:</span>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 whitespace-pre-line leading-relaxed">
                                {{ $survey->findings }}
                            </div>
                        </div>

                        <div>
                            <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px] block mb-1">Kondisi Lokasi:</span>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 whitespace-pre-line leading-relaxed">
                                {{ $survey->location_condition }}
                            </div>
                        </div>

                        @if ($survey->result_notes)
                            <div>
                                <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px] block mb-1">Catatan Tambahan:</span>
                                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 whitespace-pre-line leading-relaxed">
                                    {{ $survey->result_notes }}
                                </div>
                            </div>
                        @endif

                        <div>
                            <span class="font-bold text-blue-900 uppercase tracking-wider text-[11px] block mb-1">Kesimpulan & Rekomendasi:</span>
                            <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-200 text-blue-950 font-medium whitespace-pre-line leading-relaxed">
                                {{ $survey->conclusion }}
                            </div>
                        </div>

                        {{-- Galeri Dokumentasi Foto --}}
                        @if ($survey->photos->isNotEmpty())
                            <div class="pt-3">
                                <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px] block mb-2">
                                    Dokumentasi Foto Lokasi ({{ $survey->photos->count() }} Foto):
                                </span>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                    @foreach ($survey->photos as $photo)
                                        <div class="rounded-xl border border-slate-200 overflow-hidden bg-slate-100 group shadow-sm">
                                            <a href="{{ route('surveys.photos.show', [$survey, $photo]) }}" target="_blank">
                                                <img src="{{ route('surveys.photos.show', [$survey, $photo]) }}"
                                                     alt="{{ $photo->caption ?: 'Foto Lokasi' }}"
                                                     class="w-full h-28 object-cover group-hover:scale-105 transition duration-300">
                                            </a>
                                            <div class="p-2 bg-white text-[11px] text-slate-600 truncate border-t border-slate-100">
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

            {{-- BUKTI TANDA TANGAN DIGITAL PELANGGAN --}}
            @if ($survey->isSigned())
                <div class="bg-white rounded-2xl border-2 border-emerald-300 p-6 shadow-sm space-y-4">
                    <div class="border-b border-emerald-100 pb-3 flex items-center justify-between">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold uppercase tracking-wider">
                                Bukti Persetujuan Pelanggan
                            </span>
                            <h3 class="font-bold text-slate-900 text-base mt-1">Tanda Tangan Digital Sah</h3>
                        </div>
                        <span class="text-xs text-slate-500 font-medium">
                            {{ $survey->signed_at?->format('d F Y, H:i') }} WIB
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 items-center">
                        <div class="sm:col-span-2 text-xs space-y-3">
                            <div>
                                <span class="text-slate-400 font-bold uppercase block">Nama Penandatangan</span>
                                <p class="font-extrabold text-slate-900 text-base mt-0.5">{{ $survey->signer_name }}</p>
                            </div>
                            @if ($survey->signer_identity)
                                <div>
                                    <span class="text-slate-400 font-bold uppercase block">No. Identitas / Telepon</span>
                                    <p class="font-medium text-slate-800 mt-0.5">{{ $survey->signer_identity }}</p>
                                </div>
                            @endif
                            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Pelanggan telah memeriksa dan menyetujui seluruh hasil survei lapangan.</span>
                            </div>
                        </div>

                        <div class="flex flex-col items-center justify-center p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Tanda Tangan Digital</span>
                            <div class="w-full h-28 flex items-center justify-center p-2 bg-white rounded-lg border border-slate-200">
                                <img src="{{ route('surveys.signature.image', $survey) }}"
                                     alt="Tanda Tangan Pelanggan"
                                     class="max-h-24 max-w-full object-contain">
                            </div>
                        </div>
                    </div>

                    {{-- Prosedur Koreksi Khusus Admin --}}
                    @if ($role === 'admin')
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-500">Perlu mengoreksi hasil survei yang sudah ditandatangani?</span>
                            <button type="button" onclick="document.getElementById('modalCorrection').classList.remove('hidden')"
                                    class="text-rose-600 hover:text-rose-800 font-bold underline">
                                Buka Prosedur Koreksi Hasil
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            {{-- RIWAYAT AKTIVITAS (AUDIT LOG) --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Riwayat Aktivitas Survei (Audit Log)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Catatan transparan setiap tahapan yang dilakukan oleh Admin, Direktur, dan Petugas Lapangan.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">
                        {{ $survey->activities->count() }} Aktivitas
                    </span>
                </div>

                <div class="relative pl-6 space-y-5 border-l-2 border-slate-200 text-xs">
                    @forelse ($survey->activities as $act)
                        <div class="relative group">
                            <span class="absolute -left-[31px] top-1 w-3.5 h-3.5 rounded-full bg-blue-600 border-2 border-white shadow"></span>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-slate-900 text-xs">
                                        {{ \App\Services\SurveyActivityLogger::actionLabel($act->action) }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $act->user_name }} ({{ $act->user_role }})
                                    </span>
                                </div>
                                <span class="text-[11px] text-slate-400">
                                    {{ $act->created_at?->format('d M Y, H:i:s') }} WIB
                                </span>
                            </div>
                            <p class="text-slate-600 mt-1 leading-relaxed">{{ $act->description }}</p>
                            @if ($act->status_before || $act->status_after)
                                <div class="mt-1 text-[11px] text-slate-400 flex items-center gap-1.5">
                                    <span>Status:</span>
                                    <span class="font-medium text-slate-700">{{ \App\Models\Survey::labelFor($act->status_before) }}</span>
                                    <span>&rarr;</span>
                                    <span class="font-semibold text-blue-600">{{ \App\Models\Survey::labelFor($act->status_after) }}</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-slate-400 italic">Belum ada riwayat aktivitas yang tercatat.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL KOREKSI HASIL UNTUK ADMIN --}}
    @if ($role === 'admin')
        <div id="modalCorrection" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-base">Buka Koreksi Hasil Survei</h4>
                        <p class="text-xs text-slate-500">Prosedur perbaikan data hasil pemeriksaan lapangan.</p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed bg-amber-50 p-3 rounded-xl border border-amber-200">
                    Tanda tangan pelanggan saat ini akan diarsipkan ke log audit. Status survei akan dikembalikan ke pengisian hasil agar Petugas Survei dapat memperbaiki data dan meminta tanda tangan ulang pelanggan.
                </p>

                <form method="POST" action="{{ route('surveys.open-correction', $survey) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Alasan Pembukaan Koreksi <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="correction_reason" rows="3" required
                                  placeholder="Jelaskan alasan koreksi (misal: penyesuaian ukuran panggung atas permintaan pelanggan)..."
                                  class="w-full rounded-xl border-slate-300 text-xs focus:ring-rose-500 focus:border-rose-500 shadow-sm"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="document.getElementById('modalCorrection').classList.add('hidden')"
                                class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-xl text-xs font-bold hover:bg-rose-700 shadow-sm">
                            Buka Koreksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-app-layout>
