<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-blue-600">
                    <a href="{{ route('surveys.index') }}" class="hover:underline">Survei</a>
                    <span>/</span>
                    <a href="{{ route('surveys.show', $survey) }}" class="hover:underline">{{ $survey->sps_number ?: 'Rincian' }}</a>
                    <span>/</span>
                    <span class="text-slate-500">Hasil & Tanda Tangan</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight sm:text-2xl">
                    Form Hasil Survei Lapangan & Tanda Tangan Pelanggan
                </h2>
                <p class="text-xs text-slate-500 sm:text-sm">
                    {{ $survey->customer_display_name }} &bull; Pesanan: <span class="font-semibold text-slate-700">{{ $survey->order?->order_number ?? '-' }}</span>
                </p>
            </div>
            <div>
                <a href="{{ route('surveys.show', $survey) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-slate-300 bg-white text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Rincian Survei
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

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
                        <p class="font-bold">Terjadi Kendala</p>
                        <p class="mt-0.5 text-xs text-rose-700">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 shadow-sm text-xs text-rose-800 space-y-1">
                    <p class="font-bold text-sm text-rose-900 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Mohon periksa data isian berikut:
                    </p>
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Ringkasan Identitas Surat Perintah Survei (SPS) --}}
            <div class="rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-blue-950 p-6 text-white shadow-xl relative overflow-hidden">
                <div class="absolute right-0 top-0 -mt-6 -mr-6 w-40 h-40 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-white/10 gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-600/30 border border-blue-400/30 flex items-center justify-center text-blue-300 font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-blue-400">Surat Perintah Survei Resmi</span>
                            <h3 class="text-base font-extrabold tracking-wide text-white font-mono">
                                {{ $survey->sps_number ?: ('SRV-' . str_pad($survey->id, 4, '0', STR_PAD_LEFT)) }}
                            </h3>
                        </div>
                    </div>
                    <div>
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $survey->status_color }}">
                            {{ $survey->status_label }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-4 text-xs">
                    <div>
                        <span class="text-slate-400 block font-medium">Nama Pelanggan</span>
                        <p class="font-bold text-white text-sm mt-0.5 truncate">{{ $survey->customer_display_name }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Lokasi Survei</span>
                        <p class="font-medium text-slate-200 mt-0.5 line-clamp-2">{{ $survey->location }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Petugas Ditugaskan</span>
                        <p class="font-medium text-slate-200 mt-0.5 truncate">{{ $survey->assignedUser?->name ?? 'Belum ditentukan' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Jadwal / Pelaksanaan</span>
                        <p class="font-medium text-slate-200 mt-0.5">
                            {{ $survey->executed_on ? \Carbon\Carbon::parse($survey->executed_on)->format('d M Y') : ($survey->scheduled_at ? $survey->scheduled_at->format('d M Y, H:i') : 'Sesuai Jadwal') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Step Indicator Banner --}}
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center gap-3 p-3 rounded-lg {{ $survey->isSigned() ? 'bg-slate-50 border border-slate-200' : 'bg-blue-50/70 border-2 border-blue-500' }}">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $survey->isSigned() ? 'bg-slate-300 text-slate-700' : 'bg-blue-600 text-white shadow' }}">
                            1
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">Bagian 1: Hasil Pemeriksaan</p>
                            <p class="text-[11px] text-slate-500">Temuan fisik, kondisi lapangan, foto, & kesimpulan.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3 rounded-lg {{ $survey->isSigned() ? 'bg-emerald-50 border-2 border-emerald-500' : 'bg-indigo-50/60 border border-indigo-200' }}">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $survey->isSigned() ? 'bg-emerald-600 text-white shadow' : 'bg-indigo-600 text-white shadow' }}">
                            2
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">Bagian 2: Tanda Tangan Pelanggan</p>
                            <p class="text-[11px] text-slate-500">
                                {{ $survey->isSigned() ? 'Tanda tangan digital tersimpan & terverifikasi.' : 'Wajib ditandatangani pelanggan sebelum survei diselesaikan.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BAGIAN 1: FORM PENGISIAN HASIL SURVEI --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center flex-shrink-0 font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-base">Bagian 1: Hasil Pemeriksaan Lapangan</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Dilengkapi langsung oleh Petugas Lapangan setelah pengecekan fisik lokasi.</p>
                        </div>
                    </div>
                    @if ($survey->isSigned())
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold border border-slate-200">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            Terkunci (Sudah Ada Tanda Tangan)
                        </span>
                    @endif
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('surveys.update', $survey) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Tanggal Pelaksanaan Lapangan <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" name="executed_on" required
                                       value="{{ old('executed_on', $survey->executed_on?->format('Y-m-d') ?? now()->toDateString()) }}"
                                       @disabled($survey->isSigned())
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm disabled:bg-slate-100 disabled:text-slate-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Petugas Survei Penanggung Jawab
                                </label>
                                <input type="text" value="{{ $survey->assignedUser?->name ?? auth()->user()->name }}" disabled
                                       class="w-full rounded-xl border-slate-200 bg-slate-100 text-sm text-slate-700 font-medium">
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Hasil Pemeriksaan & Temuan Lapangan <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[11px] text-slate-400">Dimensi, layout, kapasitas & teknis</span>
                            </div>
                            <textarea name="findings" rows="4" required
                                      @disabled($survey->isSigned())
                                      placeholder="Tuliskan temuan fisik detail di lokasi (misal: luas area 20x15m, tinggi ceiling 6m, daya listrik 10.000 VA, jalur loading melalui pintu timur, butuh proteksi kabel di jalur pengunjung)..."
                                      class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm leading-relaxed disabled:bg-slate-100 disabled:text-slate-500">{{ old('findings', $survey->findings) }}</textarea>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Kondisi Lokasi Survei <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[11px] text-slate-400">Aksesibilitas, cuaca & keamanan</span>
                            </div>
                            <textarea name="location_condition" rows="3" required
                                      @disabled($survey->isSigned())
                                      placeholder="Kondisi lantai, akses kendaraan loading/truk, jarak parkir ke titik acara, kondisi pencahayaan dan sirkulasi udara..."
                                      class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm leading-relaxed disabled:bg-slate-100 disabled:text-slate-500">{{ old('location_condition', $survey->location_condition) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Catatan Tambahan / Kendala Teknis (Opsional)
                            </label>
                            <textarea name="result_notes" rows="2"
                                      @disabled($survey->isSigned())
                                      placeholder="Catatan aturan khusus gedung, perizinan damkar/satpam, jam operasional loading barang, atau permintaan khusus pelanggan..."
                                      class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm leading-relaxed disabled:bg-slate-100 disabled:text-slate-500">{{ old('result_notes', $survey->result_notes) }}</textarea>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Kesimpulan Hasil Survei <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[11px] text-slate-400">Rekomendasi final</span>
                            </div>
                            <textarea name="conclusion" rows="3" required
                                      @disabled($survey->isSigned())
                                      placeholder="Rekomendasi teknis kelayakan (misal: Lokasi sangat layak untuk pelaksanaan event; disarankan menambah 1 unit genset 60kVA backup dan penyesuaian leveling panggung)..."
                                      class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm leading-relaxed disabled:bg-slate-100 disabled:text-slate-500">{{ old('conclusion', $survey->conclusion) }}</textarea>
                        </div>

                        {{-- Dokumentasi Foto Lokasi --}}
                        <div class="pt-4 border-t border-slate-200">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Dokumentasi Foto Lapangan (Maksimal 10 Foto @ 5 MB)
                            </label>

                            @if (! $survey->isSigned())
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center p-3 rounded-xl bg-slate-50 border border-dashed border-slate-300">
                                    <div class="sm:col-span-2">
                                        <input type="file" name="photos[]" multiple accept="image/*"
                                               class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                                    </div>
                                    <div>
                                        <input type="text" name="photo_caption" placeholder="Keterangan singkat foto..."
                                               class="w-full rounded-lg border-slate-300 text-xs shadow-sm">
                                    </div>
                                </div>
                            @endif

                            @if ($survey->photos->isNotEmpty())
                                <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                    @foreach ($survey->photos as $photo)
                                        <div class="relative group rounded-xl border border-slate-200 overflow-hidden bg-slate-100 shadow-sm">
                                            <img src="{{ route('surveys.photos.show', [$survey, $photo]) }}"
                                                 alt="{{ $photo->caption ?: 'Dokumentasi Lapangan' }}"
                                                 class="w-full h-28 object-cover group-hover:scale-105 transition duration-300">
                                            <div class="p-1.5 bg-white text-[11px] text-slate-600 truncate border-t border-slate-100">
                                                {{ $photo->caption ?: $photo->file_name }}
                                            </div>
                                            @if (! $survey->isSigned())
                                                <button type="submit" form="delPhoto_{{ $photo->id }}"
                                                        onclick="return confirm('Hapus foto ini?')"
                                                        title="Hapus foto"
                                                        class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-rose-600 text-white text-xs font-bold flex items-center justify-center opacity-90 hover:opacity-100 shadow-md">
                                                    &times;
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @if (! $survey->isSigned())
                            <div class="pt-4 border-t border-slate-200 flex justify-end">
                                <button type="submit"
                                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 shadow-md transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                    </svg>
                                    Simpan Hasil Pemeriksaan Lapangan
                                </button>
                            </div>
                        @endif
                    </form>

                    {{-- Form Hapus Foto Hidden --}}
                    @if (! $survey->isSigned())
                        @foreach ($survey->photos as $photo)
                            <form id="delPhoto_{{ $photo->id }}" method="POST" action="{{ route('surveys.photos.destroy', [$survey, $photo]) }}" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- BAGIAN 2: TANDA TANGAN DIGITAL PELANGGAN --}}
            <div class="bg-white rounded-2xl border-2 border-indigo-200 shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-indigo-100 bg-gradient-to-r from-indigo-50/80 via-white to-blue-50/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-extrabold text-slate-900 text-base">Bagian 2: Tanda Tangan Digital Pelanggan</h3>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800 uppercase tracking-wider">
                                    Wajib
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 mt-0.5">
                                Bukti otentik bahwa survei telah terlaksana langsung di lokasi dan disetujui bersama pelanggan.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    @if ($survey->isSigned())
                        {{-- Sertifikat Tanda Tangan Terverifikasi --}}
                        <div class="rounded-2xl bg-gradient-to-br from-emerald-50 via-teal-50 to-white border-2 border-emerald-300 p-6 shadow-sm space-y-5">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-emerald-200 gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center shadow">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-emerald-950 text-base">Tanda Tangan Digital Pelanggan Sah</h4>
                                        <p class="text-xs text-emerald-700">Telah dibubuhkan & tersimpan aman dalam dokumen resmi survei.</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold border border-emerald-300">
                                    {{ $survey->signed_at?->format('d M Y, H:i') }} WIB
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                                <div class="space-y-3 text-xs">
                                    <div>
                                        <span class="text-slate-500 font-semibold uppercase tracking-wider block">Penandatangan Resmi</span>
                                        <p class="font-extrabold text-slate-900 text-base mt-0.5">{{ $survey->signer_name }}</p>
                                    </div>
                                    @if ($survey->signer_identity)
                                        <div>
                                            <span class="text-slate-500 font-semibold uppercase tracking-wider block">No. Identitas / Kontak</span>
                                            <p class="font-medium text-slate-800 mt-0.5">{{ $survey->signer_identity }}</p>
                                        </div>
                                    @endif
                                    <div class="p-3 rounded-xl bg-white border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Pelanggan telah menyetujui seluruh isi laporan pemeriksaan lapangan.</span>
                                    </div>
                                </div>

                                <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm flex flex-col items-center justify-center">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Pratinjau Tanda Tangan</span>
                                    <div class="w-full h-32 flex items-center justify-center p-2 bg-slate-50/50 rounded-lg border border-dashed border-slate-200">
                                        <img src="{{ route('surveys.signature.image', $survey) }}"
                                             alt="Tanda Tangan Pelanggan"
                                             class="max-h-28 max-w-full object-contain">
                                    </div>
                                    <span class="text-[10px] text-slate-400 mt-2 font-mono">Format: Digital Canvas PNG (Stored Securely)</span>
                                </div>
                            </div>

                            @if ($survey->status === \App\Models\Survey::STATUS_WAITING_RESULT)
                                <div class="pt-4 border-t border-emerald-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-bold text-slate-900">Langkah Terakhir:</p>
                                        <p class="text-xs text-slate-600">Seluruh isian dan tanda tangan lengkap. Kirim hasil untuk menyelesaikan status survei.</p>
                                    </div>
                                    <form method="POST" action="{{ route('surveys.submit-result', $survey) }}">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Apakah Anda yakin ingin menyelesaikan survei ini? Status akan berubah menjadi Selesai dan laporan PDF akan resmi diterbitkan.')"
                                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-extrabold hover:from-emerald-700 hover:to-teal-700 shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Kirim & Selesaikan Survei
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @else
                        {{-- Formulir Signature Pad Interaktif --}}
                        @if (! $survey->findings || ! $survey->location_condition || ! $survey->conclusion)
                            <div class="mb-5 rounded-xl bg-amber-50 border border-amber-300 p-4 text-xs text-amber-900 flex items-start gap-3">
                                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <div>
                                    <p class="font-bold">Lengkapi Bagian 1 Terlebih Dahulu</p>
                                    <p class="mt-0.5 text-amber-800">
                                        Pastikan data hasil pemeriksaan di Bagian 1 sudah disimpan sebelum meminta pelanggan menandatangani form ini.
                                    </p>
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('surveys.signature.save', $survey) }}" id="signatureForm" class="space-y-5">
                            @csrf
                            <input type="hidden" name="signature" id="signatureInput">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Nama Lengkap Pelanggan / Penerima Survei <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="signer_name" required
                                           value="{{ old('signer_name', $survey->order?->contact_person ?: $survey->customer_display_name) }}"
                                           placeholder="Nama lengkap yang menandatangani"
                                           class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        No. Identitas / No. Telepon (Opsional)
                                    </label>
                                    <input type="text" name="signer_identity"
                                           value="{{ old('signer_identity', $survey->order?->phone) }}"
                                           placeholder="No. KTP / Telepon / NIK / ID"
                                           class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                </div>
                            </div>

                            {{-- Canvas Signature Box Modern --}}
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Area Tanda Tangan Pelanggan <span class="text-rose-500">*</span>
                                    </label>
                                    <button type="button" id="clearSigBtn"
                                            class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600 hover:text-rose-800 px-3 py-1 rounded-lg border border-rose-200 hover:bg-rose-50 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Bersihkan Tanda Tangan
                                    </button>
                                </div>

                                <div class="w-full border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50/70 hover:bg-white transition-colors relative shadow-inner overflow-hidden" style="touch-action: none;">
                                    <canvas id="signatureCanvas" class="w-full block" height="230" style="cursor: crosshair;"></canvas>
                                    
                                    <div id="canvasPlaceholder" class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center text-slate-400 text-xs p-4 text-center">
                                        <div class="w-12 h-12 rounded-full bg-slate-200/60 flex items-center justify-center mb-2">
                                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </div>
                                        <p class="font-semibold text-slate-600">Bubuhkan tanda tangan pelanggan di sini</p>
                                        <span class="text-[11px] text-slate-400 mt-0.5">Dapat digoreskan menggunakan jari (HP/Tablet) atau kursor mouse/touchpad (Komputer).</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Persetujuan Pelanggan --}}
                            <div class="rounded-xl bg-indigo-50/80 border border-indigo-200 p-4">
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" name="signer_agreed" value="1" required
                                           @checked(old('signer_agreed'))
                                           class="mt-1 w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-xs text-slate-800 leading-relaxed">
                                        Saya selaku pelanggan / perwakilan resmi menyatakan bahwa survei lapangan ini telah benar-benar dilaksanakan, seluruh informasi hasil pemeriksaan telah saya periksa, dan saya menyetujui hasil pelaksanaan survei ini.
                                    </span>
                                </label>
                            </div>

                            <div class="pt-2 flex items-center justify-end">
                                <button type="submit" id="saveSigBtn"
                                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 shadow-md hover:shadow-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Simpan Tanda Tangan Pelanggan
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- Script Canvas Signature Pad dengan Smoothing Kurva --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('signatureCanvas');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            const clearBtn = document.getElementById('clearSigBtn');
            const sigInput = document.getElementById('signatureInput');
            const placeholder = document.getElementById('canvasPlaceholder');
            const form = document.getElementById('signatureForm');

            let isDrawing = false;
            let hasDrawn = false;
            let points = [];

            function resizeCanvas() {
                const rect = canvas.getBoundingClientRect();
                const dpr = window.devicePixelRatio || 1;

                const tempCanvas = document.createElement('canvas');
                tempCanvas.width = canvas.width;
                tempCanvas.height = canvas.height;
                const tempCtx = tempCanvas.getContext('2d');
                tempCtx.drawImage(canvas, 0, 0);

                canvas.width = rect.width * dpr;
                canvas.height = 230 * dpr;
                ctx.scale(dpr, dpr);
                ctx.lineWidth = 2.6;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                ctx.strokeStyle = '#0f2b5c'; // Premium dark ink

                if (hasDrawn) {
                    ctx.drawImage(tempCanvas, 0, 0, rect.width, 230);
                }
            }

            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);

            function getPos(e) {
                const rect = canvas.getBoundingClientRect();
                let clientX = e.clientX;
                let clientY = e.clientY;
                if (e.touches && e.touches.length > 0) {
                    clientX = e.touches[0].clientX;
                    clientY = e.touches[0].clientY;
                }
                return {
                    x: clientX - rect.left,
                    y: clientY - rect.top
                };
            }

            function startDraw(e) {
                e.preventDefault();
                isDrawing = true;
                points = [];
                const pos = getPos(e);
                points.push(pos);

                if (!hasDrawn && placeholder) {
                    placeholder.style.display = 'none';
                }
            }

            function draw(e) {
                if (!isDrawing) return;
                e.preventDefault();
                const pos = getPos(e);
                points.push(pos);

                if (points.length >= 3) {
                    const xc = (points[points.length - 2].x + points[points.length - 1].x) / 2;
                    const yc = (points[points.length - 2].y + points[points.length - 1].y) / 2;
                    ctx.beginPath();
                    ctx.moveTo(points[points.length - 3].x, points[points.length - 3].y);
                    ctx.quadraticCurveTo(points[points.length - 2].x, points[points.length - 2].y, xc, yc);
                    ctx.stroke();
                } else if (points.length === 2) {
                    ctx.beginPath();
                    ctx.moveTo(points[0].x, points[0].y);
                    ctx.lineTo(points[1].x, points[1].y);
                    ctx.stroke();
                }
                hasDrawn = true;
            }

            function stopDraw(e) {
                if (!isDrawing) return;
                isDrawing = false;
                points = [];
            }

            canvas.addEventListener('mousedown', startDraw);
            canvas.addEventListener('mousemove', draw);
            window.addEventListener('mouseup', stopDraw);

            canvas.addEventListener('touchstart', startDraw, { passive: false });
            canvas.addEventListener('touchmove', draw, { passive: false });
            window.addEventListener('touchend', stopDraw);
            window.addEventListener('touchcancel', stopDraw);

            if (clearBtn) {
                clearBtn.addEventListener('click', function () {
                    const rect = canvas.getBoundingClientRect();
                    ctx.clearRect(0, 0, rect.width, 230);
                    hasDrawn = false;
                    sigInput.value = '';
                    if (placeholder) {
                        placeholder.style.display = 'flex';
                    }
                });
            }

            if (form) {
                form.addEventListener('submit', function (e) {
                    if (!hasDrawn) {
                        e.preventDefault();
                        alert('Silakan bubuhkan tanda tangan terlebih dahulu pada area canvas yang disediakan.');
                        return false;
                    }
                    sigInput.value = canvas.toDataURL('image/png');
                });
            }
        });
    </script>
</x-app-layout>
