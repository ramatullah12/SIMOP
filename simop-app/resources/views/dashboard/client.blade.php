<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Beranda Klien</h2>
                <p class="mt-0.5 text-sm text-slate-500">
                    Pengajuan pesanan kegiatan, pemantauan status survei lokasi, dan progres pekerjaan operasional.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('orders.create') }}" class="btn-primary">
                    + Buat Pesanan Baru
                </a>
                <a href="{{ route('surveys.index') }}" class="btn-secondary">
                    Riwayat Survei
                </a>
                <a href="{{ route('tracking.index') }}" class="btn-secondary">
                    Status Pekerjaan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Ringkasan Statistik --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Total Pesanan</span>
                <div class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['orders_count'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Pesanan yang diajukan</span>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Survei Diproses</span>
                <div class="text-2xl font-bold text-blue-600 mt-2">{{ $stats['surveys_in_progress'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Tahap persetujuan & pelaksanaan</span>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Survei Selesai</span>
                <div class="text-2xl font-bold text-emerald-600 mt-2">{{ $stats['surveys_completed'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Hasil survei telah terbit</span>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase text-gray-500">Pekerjaan Selesai</span>
                <div class="text-2xl font-bold text-green-600 mt-2">{{ $stats['completed_projects'] ?? 0 }}</div>
                <span class="text-xs text-gray-500">Proyek operasional selesai</span>
            </div>
        </div>

        {{-- Monitoring Status & Riwayat Survei --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        Monitoring Status & Riwayat Survei
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Pantau progres survei lokasi pelanggan yang diajukan oleh Admin dan disetujui Direktur.
                    </p>
                </div>
                <a href="{{ route('surveys.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1">
                    Lihat Semua Riwayat Survei &rarr;
                </a>
            </div>

            <div class="p-5 space-y-6">
                @forelse ($clientSurveys as $survey)
                    @php
                        $isRejected = $survey->status === \App\Models\Survey::STATUS_REJECTED;
                        $isApproved = !empty($survey->approved_at);
                        $isCompleted = $survey->status === \App\Models\Survey::STATUS_DONE;
                        $isInProgress = in_array($survey->status, [\App\Models\Survey::STATUS_IN_PROGRESS, \App\Models\Survey::STATUS_WAITING_RESULT]);
                        $isWaitingExecution = in_array($survey->status, [\App\Models\Survey::STATUS_WAITING_EXECUTION, \App\Models\Survey::STATUS_APPROVED, 'dijadwalkan']);
                        $isWaitingDirector = in_array($survey->status, [\App\Models\Survey::STATUS_WAITING_DIRECTOR, 'diajukan_client']);

                        // Status index for 6 steps:
                        // 1. Pengajuan Dibuat Admin
                        // 2. Menunggu Persetujuan Direktur
                        // 3. Disetujui / Ditolak Direktur
                        // 4. Menunggu Pelaksanaan Survei (SPS Terbit)
                        // 5. Survei Sedang Berlangsung
                        // 6. Survei Selesai
                        $step = 1;
                        if ($isWaitingDirector) {
                            $step = 2;
                        } elseif ($isRejected) {
                            $step = 3;
                        } elseif ($isWaitingExecution) {
                            $step = 4;
                        } elseif ($isInProgress) {
                            $step = 5;
                        } elseif ($isCompleted) {
                            $step = 6;
                        } elseif ($isApproved) {
                            $step = 4;
                        }
                    @endphp

                    <div class="border border-slate-200 rounded-xl p-5 bg-slate-50/50 hover:bg-slate-50 transition">
                        {{-- Header Survei --}}
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-200/80 gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-bold text-slate-900">
                                        {{ $survey->sps_number ?: ('SRV-' . str_pad($survey->id, 4, '0', STR_PAD_LEFT)) }}
                                    </span>
                                    @if ($survey->sps_number)
                                        <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-100 text-blue-800">
                                            SPS Terbit
                                        </span>
                                    @endif
                                    <span class="text-xs text-slate-500">
                                        &bull; Pengajuan: {{ $survey->created_at->format('d M Y, H:i') }} WIB
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600">
                                    Pesanan: <span class="font-medium text-slate-800">{{ $survey->order?->order_number ?? '-' }}</span> — {{ $survey->order?->event_name ?? 'Kegiatan Pelanggan' }}
                                </p>
                            </div>

                            <div>
                                @if ($isCompleted)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        Survei Selesai
                                    </span>
                                @elseif ($isRejected)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                                        Ditolak Direktur
                                    </span>
                                @elseif ($isInProgress)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 animate-pulse">
                                        Sedang Berlangsung
                                    </span>
                                @elseif ($isWaitingExecution)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-cyan-100 text-cyan-800">
                                        Menunggu Pelaksanaan
                                    </span>
                                @elseif ($isWaitingDirector)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                        Menunggu Persetujuan Direktur
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-200 text-slate-800">
                                        {{ strtoupper(str_replace('_', ' ', $survey->status)) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Metadata Survei --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 py-4 text-xs">
                            <div>
                                <span class="text-slate-400 block font-semibold uppercase">Lokasi Survei</span>
                                <span class="font-medium text-slate-800 mt-0.5 block line-clamp-2">
                                    {{ $survey->location }}
                                </span>
                            </div>
                            <div>
                                <span class="text-slate-400 block font-semibold uppercase">Jadwal Survei</span>
                                <span class="font-medium text-slate-800 mt-0.5 block">
                                    {{ $survey->scheduled_at ? $survey->scheduled_at->format('d M Y, H:i') . ' WIB' : 'Belum dijadwalkan' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-slate-400 block font-semibold uppercase">Petugas Survei</span>
                                <span class="font-medium text-slate-800 mt-0.5 block">
                                    {{ $survey->assignedUser?->name ?? 'Belum ditentukan' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-slate-400 block font-semibold uppercase">Tujuan / Kebutuhan</span>
                                <span class="font-medium text-slate-800 mt-0.5 block line-clamp-2">
                                    {{ $survey->purpose ?: ($survey->survey_type ?? 'Survei Kebutuhan Lapangan') }}
                                </span>
                            </div>
                        </div>

                        {{-- Progress Tracker 6 Tahapan --}}
                        <div class="py-4 border-t border-slate-200/80">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-3">
                                Progres Tahapan Survei:
                            </span>

                            <div class="grid grid-cols-2 md:grid-cols-6 gap-2">
                                {{-- Step 1 --}}
                                <div class="flex flex-col items-center text-center p-2 rounded-lg {{ $step >= 1 ? 'bg-blue-50 border border-blue-200 text-blue-900' : 'bg-slate-100 text-slate-400' }}">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mb-1 {{ $step >= 1 ? 'bg-blue-600 text-white' : 'bg-slate-300 text-slate-600' }}">
                                        &check;
                                    </div>
                                    <span class="text-[11px] font-semibold leading-tight">1. Pengajuan Admin</span>
                                    <span class="text-[10px] text-slate-500 mt-0.5">Dibuat</span>
                                </div>

                                {{-- Step 2 --}}
                                <div class="flex flex-col items-center text-center p-2 rounded-lg {{ $step >= 2 ? ($step == 2 ? 'bg-amber-50 border border-amber-300 text-amber-900' : 'bg-blue-50 border border-blue-200 text-blue-900') : 'bg-slate-100 text-slate-400' }}">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mb-1 {{ $step >= 2 ? ($step == 2 ? 'bg-amber-500 text-white' : 'bg-blue-600 text-white') : 'bg-slate-300 text-slate-600' }}">
                                        {{ $step > 2 ? '✓' : '2' }}
                                    </div>
                                    <span class="text-[11px] font-semibold leading-tight">2. Review Direktur</span>
                                    <span class="text-[10px] text-slate-500 mt-0.5">{{ $step == 2 ? 'Diproses' : ($step > 2 ? 'Selesai' : 'Menunggu') }}</span>
                                </div>

                                {{-- Step 3 --}}
                                @if ($isRejected)
                                    <div class="flex flex-col items-center text-center p-2 rounded-lg bg-rose-50 border border-rose-300 text-rose-900">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mb-1 bg-rose-600 text-white">
                                            &times;
                                        </div>
                                        <span class="text-[11px] font-semibold leading-tight">3. Ditolak Direktur</span>
                                        <span class="text-[10px] text-rose-600 mt-0.5">Perlu perbaikan</span>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center text-center p-2 rounded-lg {{ $step >= 3 ? ($step == 3 ? 'bg-amber-50 border border-amber-300 text-amber-900' : 'bg-blue-50 border border-blue-200 text-blue-900') : 'bg-slate-100 text-slate-400' }}">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mb-1 {{ $step >= 3 ? 'bg-blue-600 text-white' : 'bg-slate-300 text-slate-600' }}">
                                            {{ $step > 3 ? '✓' : '3' }}
                                        </div>
                                        <span class="text-[11px] font-semibold leading-tight">3. Persetujuan</span>
                                        <span class="text-[10px] text-slate-500 mt-0.5">{{ $step >= 4 ? 'Disetujui' : 'Menunggu' }}</span>
                                    </div>
                                @endif

                                {{-- Step 4 --}}
                                <div class="flex flex-col items-center text-center p-2 rounded-lg {{ $step >= 4 && !$isRejected ? ($step == 4 ? 'bg-cyan-50 border border-cyan-300 text-cyan-900' : 'bg-blue-50 border border-blue-200 text-blue-900') : 'bg-slate-100 text-slate-400' }}">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mb-1 {{ $step >= 4 && !$isRejected ? ($step == 4 ? 'bg-cyan-600 text-white' : 'bg-blue-600 text-white') : 'bg-slate-300 text-slate-600' }}">
                                        {{ $step > 4 && !$isRejected ? '✓' : '4' }}
                                    </div>
                                    <span class="text-[11px] font-semibold leading-tight">4. SPS & Jadwal</span>
                                    <span class="text-[10px] text-slate-500 mt-0.5">{{ $step >= 4 && !$isRejected ? ($step == 4 ? 'Terbit/Tunggu' : 'Siap') : 'Belum' }}</span>
                                </div>

                                {{-- Step 5 --}}
                                <div class="flex flex-col items-center text-center p-2 rounded-lg {{ $step >= 5 && !$isRejected ? ($step == 5 ? 'bg-indigo-50 border border-indigo-300 text-indigo-900' : 'bg-blue-50 border border-blue-200 text-blue-900') : 'bg-slate-100 text-slate-400' }}">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mb-1 {{ $step >= 5 && !$isRejected ? ($step == 5 ? 'bg-indigo-600 text-white' : 'bg-blue-600 text-white') : 'bg-slate-300 text-slate-600' }}">
                                        {{ $step > 5 && !$isRejected ? '✓' : '5' }}
                                    </div>
                                    <span class="text-[11px] font-semibold leading-tight">5. Pelaksanaan</span>
                                    <span class="text-[10px] text-slate-500 mt-0.5">{{ $step == 5 && !$isRejected ? 'Berlangsung' : ($step > 5 ? 'Selesai' : 'Belum') }}</span>
                                </div>

                                {{-- Step 6 --}}
                                <div class="flex flex-col items-center text-center p-2 rounded-lg {{ $step >= 6 && !$isRejected ? 'bg-emerald-50 border border-emerald-300 text-emerald-900' : 'bg-slate-100 text-slate-400' }}">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mb-1 {{ $step >= 6 && !$isRejected ? 'bg-emerald-600 text-white' : 'bg-slate-300 text-slate-600' }}">
                                        {{ $step >= 6 && !$isRejected ? '✓' : '6' }}
                                    </div>
                                    <span class="text-[11px] font-semibold leading-tight">6. Survei Selesai</span>
                                    <span class="text-[10px] text-slate-500 mt-0.5">{{ $step >= 6 && !$isRejected ? 'Lengkap & PDF' : 'Belum' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Alert jika ditolak Direktur --}}
                        @if ($isRejected)
                            <div class="mt-3 p-3 bg-rose-50 border border-rose-200 rounded-lg text-xs text-rose-800">
                                <div class="font-bold flex items-center gap-1.5 mb-1">
                                    <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    Pengajuan Survei Ditolak oleh Direktur
                                </div>
                                <p class="text-rose-700 pl-5">
                                    Alasan: <span class="font-medium">{{ $survey->rejection_notes ?: 'Pengajuan sedang dikembalikan kepada Admin untuk penyesuaian data.' }}</span>
                                </p>
                            </div>
                        @endif

                        {{-- Info jika sedang berlangsung --}}
                        @if ($isInProgress)
                            <div class="mt-3 p-3 bg-indigo-50 border border-indigo-200 rounded-lg text-xs text-indigo-900 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div>
                                    <span class="font-bold flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                        Petugas Survei Sedang Melakukan Pemeriksaan Lapangan
                                    </span>
                                    <p class="text-indigo-700 mt-0.5">
                                        Petugas: <span class="font-semibold">{{ $survey->assignedUser?->name ?? 'Petugas Lapangan' }}</span>
                                        @if ($survey->scheduled_at)
                                            &bull; Jadwal: {{ $survey->scheduled_at->format('d M Y, H:i') }} WIB
                                        @endif
                                    </p>
                                </div>
                                <span class="text-[11px] text-indigo-600 bg-white px-2.5 py-1 rounded border border-indigo-200 font-medium">
                                    Pemeriksaan Langsung di Lokasi
                                </span>
                            </div>
                        @endif

                        {{-- Ringkasan hasil & tombol PDF jika selesai --}}
                        @if ($isCompleted)
                            <div class="mt-3 p-4 bg-emerald-50/70 border border-emerald-200 rounded-lg text-xs">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-2">
                                    <span class="font-bold text-emerald-900 flex items-center gap-1.5 text-sm">
                                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Hasil Pemeriksaan Lapangan Selesai
                                    </span>
                                    <a href="{{ route('surveys.pdf.result', $survey) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-700 text-white rounded-md text-xs font-medium hover:bg-emerald-800 shadow-sm transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Unduh Hasil Survei (PDF)
                                    </a>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-slate-700 pt-2 border-t border-emerald-200/60">
                                    <div>
                                        <span class="font-semibold text-slate-500 block">Tgl Pelaksanaan:</span>
                                        <span>{{ $survey->executed_on ? \Carbon\Carbon::parse($survey->executed_on)->format('d M Y') : ($survey->completed_at ? $survey->completed_at->format('d M Y') : '-') }}</span>
                                    </div>
                                    <div>
                                        <span class="font-semibold text-slate-500 block">Kondisi Lokasi:</span>
                                        <span class="line-clamp-2">{{ $survey->location_condition ?: '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="font-semibold text-slate-500 block">Kesimpulan:</span>
                                        <span class="line-clamp-2">{{ $survey->conclusion ?: '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Footer Aksi Read-Only --}}
                        <div class="mt-4 pt-3 border-t border-slate-200/80 flex items-center justify-between text-xs">
                            <span class="text-slate-400">
                                Hak akses Klien: <span class="font-medium text-slate-600">Read-Only (Hanya Melihat Progres)</span>
                            </span>
                            <a href="{{ route('surveys.show', $survey) }}"
                               class="inline-flex items-center gap-1 font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                Lihat Rincian Survei Lengkap &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 px-4">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm">Belum Ada Riwayat Survei</h4>
                        <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                            Survei lokasi akan diajukan dan dijadwalkan oleh tim Admin setelah pesanan kegiatan Anda diterima.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Daftar Pesanan & Status Pekerjaan --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-900 text-base">Daftar Pesanan & Status Pekerjaan</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Seluruh pesanan kegiatan yang telah Anda ajukan.</p>
                </div>
                <a href="{{ route('tracking.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">
                    Lihat Semua
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">No. Pesanan</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Kegiatan & Lokasi</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Status Survei</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Status Proyek</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-gray-900">{{ $order->order_number }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">{{ $order->event_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->event_location }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($order->survey)
                                        <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-xs font-medium">
                                            {{ $order->survey->sps_number ?: strtoupper(str_replace('_', ' ', $order->survey->status)) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">Belum ada</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($order->project)
                                        <span class="px-2 py-0.5 rounded bg-green-100 text-green-800 text-xs font-medium">
                                            {{ strtoupper($order->project->status) }} ({{ $order->project->progress }}%)
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-500">Dalam Proses</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('tracking.show', $order) }}"
                                       class="px-3 py-1.5 bg-blue-600 text-white rounded text-xs font-medium hover:bg-blue-700">
                                        Lihat Status
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                    Belum ada data pesanan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
