<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Survei & Surat Perintah Survei (SPS)
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Pesanan: {{ $survey->order?->order_number }} — {{ $survey->order?->event_name }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if ($survey->order)
                    <a href="{{ route('tracking.show', $survey->order) }}"
                       class="px-4 py-2 rounded-md bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700">
                        Lihat Status Pekerjaan
                    </a>
                @endif

                @if ($role === 'admin' && $survey->status === 'selesai' && ! $survey->order?->project)
                    <a href="{{ route('projects.create', ['survey_id' => $survey->id]) }}"
                       class="px-4 py-2 rounded-md bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
                        + Buat Proyek
                    </a>
                @endif

                @if ($role === 'petugas-lapangan' && $survey->approved_at)
                    <a href="{{ route('surveys.edit', $survey) }}"
                       class="px-4 py-2 rounded-md bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
                        Catat Hasil Pelaksanaan Survei
                    </a>
                @endif

                <a href="{{ route('surveys.index') }}"
                   class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
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

            <!-- Alur Status Survei & SPS -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-200">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-4">
                    Alur Persetujuan & Penerbitan Surat Perintah Survei (SPS)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <div class="p-4 rounded-lg border border-emerald-300 bg-emerald-50">
                        <p class="text-xs font-bold text-emerald-700 uppercase">1. Pengajuan Klien</p>
                        <p class="font-semibold text-gray-900 mt-1">Pengajuan Survei</p>
                        <p class="text-xs text-gray-600 mt-1">Diajukan oleh {{ $survey->order?->company_name }}</p>
                    </div>

                    <div class="p-4 rounded-lg border {{ $survey->submitted_to_direktur_at || $survey->approved_at ? 'border-emerald-300 bg-emerald-50' : 'border-amber-300 bg-amber-50' }}">
                        <p class="text-xs font-bold {{ $survey->submitted_to_direktur_at || $survey->approved_at ? 'text-emerald-700' : 'text-amber-700' }} uppercase">2. Proses Admin</p>
                        <p class="font-semibold text-gray-900 mt-1">Verifikasi & Pilih Petugas</p>
                        <p class="text-xs text-gray-600 mt-1">
                            {{ $survey->assignedUser ? 'Petugas: ' . $survey->assignedUser->name : 'Menunggu Admin memilih petugas & mengirim ke Direktur' }}
                        </p>
                    </div>

                    <div class="p-4 rounded-lg border {{ $survey->approved_at ? 'border-emerald-300 bg-emerald-50' : ($survey->status === 'menunggu_persetujuan_direktur' ? 'border-purple-300 bg-purple-50' : 'border-gray-200 bg-gray-50') }}">
                        <p class="text-xs font-bold {{ $survey->approved_at ? 'text-emerald-700' : ($survey->status === 'menunggu_persetujuan_direktur' ? 'text-purple-700' : 'text-gray-500') }} uppercase">3. Persetujuan Direktur</p>
                        <p class="font-semibold text-gray-900 mt-1">Terbit SPS ke Petugas</p>
                        <p class="text-xs text-gray-600 mt-1">
                            @if ($survey->approved_at)
                                Disetujui ({{ $survey->sps_number }})
                            @elseif ($survey->status === 'menunggu_persetujuan_direktur')
                                Menunggu Persetujuan Direktur
                            @elseif ($survey->status === 'ditolak_direktur')
                                Ditolak / Revisi Direktur
                            @else
                                Menunggu kiriman Admin
                            @endif
                        </p>
                    </div>

                    <div class="p-4 rounded-lg border {{ $survey->status === 'selesai' ? 'border-emerald-300 bg-emerald-50' : ($survey->approved_at ? 'border-blue-300 bg-blue-50' : 'border-gray-200 bg-gray-50') }}">
                        <p class="text-xs font-bold {{ $survey->status === 'selesai' ? 'text-emerald-700' : ($survey->approved_at ? 'text-blue-700' : 'text-gray-500') }} uppercase">4. Petugas Lapangan</p>
                        <p class="font-semibold text-gray-900 mt-1">Pelaksanaan Survei</p>
                        <p class="text-xs text-gray-600 mt-1">
                            @if ($survey->status === 'selesai')
                                Survei Selesai Dilaksanakan
                            @elseif ($survey->approved_at)
                                SPS Diterima Petugas ({{ strtoupper($survey->status) }})
                            @else
                                Menunggu SPS Terbit
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Surat Perintah Survei (SPS) Resmi -->
            @if ($survey->approved_at && $survey->sps_number)
                <div class="bg-blue-50 border-2 border-blue-300 rounded-lg p-6 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-blue-200 pb-4 mb-4">
                        <div>
                            <span class="inline-flex px-3 py-1 rounded-full bg-blue-600 text-white text-xs font-bold uppercase tracking-wider">
                                Surat Perintah Survei (SPS) Resmi
                            </span>
                            <h3 class="text-xl font-bold text-gray-900 mt-2">{{ $survey->sps_number }}</h3>
                        </div>
                        <div class="text-sm text-right">
                            <p class="text-gray-600">Disetujui Direktur:</p>
                            <p class="font-bold text-gray-900">{{ $survey->approver?->name ?? 'Direktur' }}</p>
                            <p class="text-xs text-gray-500">{{ $survey->approved_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Ditugaskan Kepada (Petugas Lapangan)</p>
                            <p class="font-bold text-gray-900 text-base">{{ $survey->assignedUser?->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Jadwal Pelaksanaan Survei</p>
                            <p class="font-semibold text-gray-900">{{ $survey->scheduled_at?->format('d M Y, H:i') ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Lokasi Survei</p>
                            <p class="font-semibold text-gray-900">{{ $survey->location }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($survey->rejection_notes)
                <div class="bg-red-50 border border-red-300 rounded-lg p-4 text-sm text-red-800">
                    <strong>Catatan Penolakan / Revisi dari Direktur:</strong>
                    <p class="mt-1">{{ $survey->rejection_notes }}</p>
                </div>
            @endif

            <!-- Panel Tindakan Admin -->
            @if ($role === 'admin' && ! $survey->approved_at)
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border-2 border-amber-300">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">
                        Proses Admin: Pilih Petugas Lapangan & Kirim ke Direktur
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Pengajuan survei dari Klien harus diteruskan oleh Admin ke Direktur. Setelah Direktur menyetujui, Surat Perintah Survei (SPS) akan dikirim ke Petugas Lapangan.
                    </p>

                    <form method="POST" action="{{ route('surveys.submit-to-direktur', $survey) }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Petugas Lapangan</label>
                                <select name="assigned_to" required class="w-full rounded-lg border-gray-300 text-sm">
                                    <option value="">-- Pilih Petugas Lapangan --</option>
                                    @foreach ($fieldOfficers as $officer)
                                        <option value="{{ $officer->id }}" @selected(old('assigned_to', $survey->assigned_to) == $officer->id)>
                                            {{ $officer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jadwal Survei</label>
                                <input type="datetime-local" name="scheduled_at" required
                                       value="{{ old('scheduled_at', $survey->scheduled_at?->format('Y-m-d\TH:i') ?? now()->addDay()->format('Y-m-d\T09:00')) }}"
                                       class="w-full rounded-lg border-gray-300 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Survei</label>
                                <input type="text" name="location" required
                                       value="{{ old('location', $survey->location) }}"
                                       class="w-full rounded-lg border-gray-300 text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Pengantar untuk Direktur</label>
                            <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 text-sm">{{ old('notes', $survey->notes) }}</textarea>
                        </div>

                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700">
                            Kirim ke Direktur (Menunggu Persetujuan Direktur)
                        </button>
                    </form>
                </div>
            @endif

            <!-- Panel Tindakan Direktur -->
            @if ($role === 'direktur' && ! $survey->approved_at)
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border-2 border-purple-300 space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            Persetujuan Direktur — Penerbitan Surat Perintah Survei (SPS)
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Setujui pengajuan survei dari Admin ini untuk menerbitkan <strong>Surat Perintah Survei (SPS)</strong> dan mengirimkannya ke <strong>Petugas Lapangan ({{ $survey->assignedUser?->name ?? 'Belum dipilih' }})</strong>.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <form method="POST" action="{{ route('surveys.approve-direktur', $survey) }}" class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 space-y-3">
                            @csrf
                            <h4 class="font-semibold text-emerald-900 text-sm">Setujui & Kirim SPS ke Petugas Lapangan</h4>
                            <div>
                                <label class="block text-xs font-medium text-emerald-800 mb-1">Instruksi Direktur pada SPS (Opsional)</label>
                                <textarea name="notes" rows="2" placeholder="Catatan instruksi Direktur untuk Petugas Lapangan..."
                                          class="w-full rounded-lg border-emerald-300 text-sm">{{ $survey->notes }}</textarea>
                            </div>
                            <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700">
                                Setujui & Kirim Surat Perintah Survei ke Petugas Lapangan
                            </button>
                        </form>

                        <form method="POST" action="{{ route('surveys.reject-direktur', $survey) }}" class="p-4 rounded-lg bg-red-50 border border-red-200 space-y-3">
                            @csrf
                            <h4 class="font-semibold text-red-900 text-sm">Tolak / Minta Revisi ke Admin</h4>
                            <div>
                                <label class="block text-xs font-medium text-red-800 mb-1">Alasan Penolakan / Revisi (Wajib)</label>
                                <textarea name="rejection_notes" rows="2" required placeholder="Contoh: Ganti jadwal survei atau ganti petugas..."
                                          class="w-full rounded-lg border-red-300 text-sm"></textarea>
                            </div>
                            <button type="submit" class="w-full py-2.5 px-4 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700">
                                Kembalikan ke Admin (Revisi)
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Detail Informasi Survei & Hasil Temuan Lapangan -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-gray-500">Nomor Pesanan</p>
                        <p class="font-semibold text-gray-900 mt-1">{{ $survey->order?->order_number ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Klien & Kegiatan</p>
                        <p class="font-semibold text-gray-900 mt-1">
                            {{ $survey->order?->company_name ?? '-' }} - {{ $survey->order?->event_name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Petugas Lapangan</p>
                        <p class="font-semibold text-gray-900 mt-1">{{ $survey->assignedUser?->name ?? 'Belum dipilih' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Jadwal Survei</p>
                        <p class="font-semibold text-gray-900 mt-1">{{ $survey->scheduled_at?->format('d M Y H:i') ?? 'Belum dijadwalkan' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Lokasi Survei</p>
                        <p class="font-semibold text-gray-900 mt-1">{{ $survey->location }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Status Saat Ini</p>
                        <p class="font-semibold text-gray-900 mt-1">{{ strtoupper(str_replace('_', ' ', $survey->status)) }}</p>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <p class="text-sm text-gray-500">Hasil Temuan Survei Lapangan (Dicatat oleh Petugas Lapangan)</p>
                    <p class="mt-2 text-gray-900 whitespace-pre-line font-medium">{{ $survey->findings ?: 'Belum ada hasil temuan survei.' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Catatan Tambahan</p>
                    <p class="mt-2 text-gray-900 whitespace-pre-line">{{ $survey->notes ?: '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
