<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-blue-600">
                    <a href="{{ route('orders.index') }}" class="hover:underline">Pesanan</a>
                    <span>/</span>
                    <span class="text-slate-500">{{ $order->order_number }}</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight sm:text-2xl">
                    Rincian Pesanan Kegiatan
                </h2>
                <p class="text-xs text-slate-500 sm:text-sm">
                    {{ $order->company_name }} &bull; {{ $order->event_name }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('tracking.show', $order) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 shadow-sm transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Lacak Alur Pekerjaan
                </a>

                {{-- Status & Aksi Survei (HANYA Admin yang berhak membuat pengajuan survei) --}}
                @if ($order->survey)
                    <a href="{{ route('surveys.show', $order->survey) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700 shadow-sm transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Rincian Survei / SPS
                    </a>
                @elseif ($role === 'admin')
                    <a href="{{ route('surveys.create', ['order_id' => $order->id]) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-600 text-white text-xs font-extrabold hover:bg-blue-700 shadow-md transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        + Buat Pengajuan Survei
                    </a>
                @endif

                @if ($role === 'admin' || ($role === 'client' && in_array($order->status, ['masuk', 'menunggu_survey', 'ditolak'])))
                    <a href="{{ route('orders.edit', $order) }}"
                       class="inline-flex items-center gap-1 px-3.5 py-2 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold hover:bg-blue-100 transition">
                        Ubah Pesanan
                    </a>
                @endif

                <a href="{{ route('orders.index') }}"
                   class="inline-flex items-center gap-1 px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 shadow-sm transition">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Notification --}}
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

            {{-- Banner Status Pesanan --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-lg border border-blue-100">
                        📋
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Nomor Pesanan</span>
                            <span class="text-xs font-mono font-bold text-blue-700">{{ $order->order_number }}</span>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900 mt-0.5">{{ $order->event_name }}</h3>
                    </div>
                </div>

                <div class="text-left sm:text-right">
                    <span class="text-xs text-slate-400 block font-medium">Status Pesanan Saat Ini:</span>
                    @php
                        $statusColors = [
                            'masuk' => 'bg-blue-100 text-blue-800',
                            'diproses' => 'bg-amber-100 text-amber-800',
                            'menunggu_survey' => 'bg-indigo-100 text-indigo-800',
                            'survey_selesai' => 'bg-teal-100 text-teal-800',
                            'disetujui' => 'bg-emerald-100 text-emerald-800',
                            'ditolak' => 'bg-rose-100 text-rose-800',
                            'selesai' => 'bg-green-100 text-green-900',
                        ];
                        $badgeColor = $statusColors[$order->status] ?? 'bg-slate-100 text-slate-700';
                    @endphp
                    <span class="inline-flex px-3.5 py-1 rounded-full text-xs font-extrabold mt-1 {{ $badgeColor }}">
                        {{ strtoupper(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
            </div>

            {{-- Panel Verifikasi Pesanan (Khusus Admin) --}}
            @if ($role === 'admin' && in_array($order->status, ['masuk', 'diproses'], true))
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border-2 border-blue-200 p-6 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-blue-600 text-white">
                                    Verifikasi Admin
                                </span>
                                <h3 class="text-base font-extrabold text-slate-900">Verifikasi & Tindak Lanjut Pesanan</h3>
                            </div>
                            <p class="text-xs text-slate-600 max-w-xl">
                                Tinjau kelengkapan pesanan dari pelanggan. Verifikasi pesanan untuk meneruskannya langsung ke pengajuan survei, memproses operasional, atau menolaknya.
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2.5">
                            {{-- Form Verifikasi & Lanjut Survei --}}
                            <form method="POST" action="{{ route('orders.verify', $order) }}" class="inline">
                                @csrf
                                <input type="hidden" name="next_action" value="menunggu_survey">
                                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold rounded-xl shadow-sm transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Verifikasi & Buat Pengajuan Survei
                                </button>
                            </form>

                            {{-- Form Verifikasi & Proses --}}
                            @if ($order->status === 'masuk')
                                <form method="POST" action="{{ route('orders.verify', $order) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="next_action" value="diproses">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                                        Verifikasi & Proses
                                    </button>
                                </form>
                            @endif

                            {{-- Tombol Tolak Pesanan --}}
                            <button type="button" onclick="document.getElementById('rejectOrderModal').classList.remove('hidden')"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold rounded-xl transition">
                                Tolak Pesanan
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Modal Tolak Pesanan --}}
                <div id="rejectOrderModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-3 border-b">
                            <h4 class="font-extrabold text-slate-900 text-base">Tolak Pesanan Masuk</h4>
                            <button type="button" onclick="document.getElementById('rejectOrderModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                        </div>
                        <form method="POST" action="{{ route('orders.reject', $order) }}" class="mt-4 space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alasan Penolakan Pesanan</label>
                                <textarea name="rejection_reason" rows="3" required placeholder="Tuliskan alasan penolakan secara jelas untuk pelanggan..." class="w-full text-xs rounded-xl border-slate-300 focus:border-rose-500 focus:ring-rose-500"></textarea>
                            </div>
                            <div class="flex items-center justify-end gap-2 pt-2">
                                <button type="button" onclick="document.getElementById('rejectOrderModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-200 transition">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-sm transition">Kirim Penolakan</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Detail Informasi Pesanan --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <h3 class="font-extrabold text-slate-900 text-base">Informasi Pelanggan & Narahubung</h3>
                    <span class="text-xs text-slate-400 font-mono">Dibuat: {{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                    <div>
                        <span class="text-slate-400 font-bold uppercase block">Perusahaan / Instansi Pelanggan</span>
                        <p class="font-extrabold text-slate-900 text-sm mt-0.5">{{ $order->company_name }}</p>
                    </div>

                    <div>
                        <span class="text-slate-400 font-bold uppercase block">Penanggung Jawab (PIC)</span>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">{{ $order->contact_person }}</p>
                    </div>

                    <div>
                        <span class="text-slate-400 font-bold uppercase block">No. Telepon / WhatsApp</span>
                        <p class="font-semibold text-slate-800 mt-0.5">{{ $order->phone }}</p>
                    </div>

                    <div>
                        <span class="text-slate-400 font-bold uppercase block">Email</span>
                        <p class="font-semibold text-slate-800 mt-0.5">{{ $order->email ?: '-' }}</p>
                    </div>

                    @if ($order->client)
                        <div class="md:col-span-2 p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                            <div>
                                <span class="text-slate-400 text-[10px] font-bold uppercase block">Akun Klien SIMOP Terhubung:</span>
                                <span class="font-bold text-slate-800">{{ $order->client->name }}</span>
                                <span class="text-slate-500 font-normal">({{ $order->client->email }})</span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">
                                ID Pelanggan #{{ $order->client_id }}
                            </span>
                        </div>
                    @endif
                </div>

                <div class="border-b border-slate-100 pt-3 pb-3">
                    <h3 class="font-extrabold text-slate-900 text-base">Rincian Kegiatan & Lokasi</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                    <div>
                        <span class="text-slate-400 font-bold uppercase block">Nama Kegiatan</span>
                        <p class="font-bold text-slate-900 text-sm mt-0.5">{{ $order->event_name }}</p>
                    </div>

                    <div>
                        <span class="text-slate-400 font-bold uppercase block">Tanggal Pelaksanaan Kegiatan</span>
                        <p class="font-bold text-slate-900 text-sm mt-0.5">
                            {{ $order->event_date ? $order->event_date->format('d F Y') : 'Belum Ditentukan' }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <span class="text-slate-400 font-bold uppercase block">Alamat Lokasi Kegiatan & Survei</span>
                        <p class="font-medium text-slate-800 text-sm mt-0.5 leading-relaxed">{{ $order->event_location }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <span class="text-slate-400 font-bold uppercase block">Deskripsi Kebutuhan Pekerjaan</span>
                        <div class="mt-1 p-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 leading-relaxed whitespace-pre-line text-xs">
                            {{ $order->description }}
                        </div>
                    </div>

                    @if ($order->admin_notes)
                        <div class="md:col-span-2">
                            <span class="text-slate-400 font-bold uppercase block">Catatan Tambahan Admin</span>
                            <div class="mt-1 p-4 rounded-xl bg-amber-50/80 border border-amber-200 text-amber-900 text-xs leading-relaxed whitespace-pre-line">
                                {{ $order->admin_notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Kartu Status Survei & Proyek Terkait --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Survei --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-3">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <span class="text-blue-600">📍</span> Status Survei Lokasi
                        </h4>
                        @if ($order->survey)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $order->survey->status_color }}">
                                {{ $order->survey->status_label }}
                            </span>
                        @endif
                    </div>

                    @if ($order->survey)
                        <div class="text-xs space-y-2">
                            <p class="text-slate-600">
                                No. SPS: <strong class="text-slate-900 font-mono">{{ $order->survey->sps_number ?: '-' }}</strong>
                            </p>
                            <p class="text-slate-600">
                                Petugas: <strong class="text-slate-900">{{ $order->survey->assignedUser?->name ?? 'Belum ditentukan' }}</strong>
                            </p>
                            <p class="text-slate-600">
                                Jadwal: <strong class="text-slate-900">{{ $order->survey->scheduled_at ? $order->survey->scheduled_at->format('d M Y, H:i') : 'Menunggu' }}</strong>
                            </p>
                            <div class="pt-2">
                                <a href="{{ route('surveys.show', $order->survey) }}"
                                   class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                    Lihat Detail Survei &rarr;
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="text-xs text-slate-500 py-2">
                            <p>Belum ada pengajuan survei untuk pesanan ini.</p>
                            @if ($role === 'admin')
                                <p class="mt-1 text-slate-600">Sebagai Admin, Anda dapat membuat pengajuan survei untuk pesanan ini.</p>
                                <div class="mt-3">
                                    <a href="{{ route('surveys.create', ['order_id' => $order->id]) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 transition">
                                        + Buat Pengajuan Survei
                                    </a>
                                </div>
                            @else
                                <p class="mt-1 text-slate-400">Tim Admin akan meninjau dan membuat pengajuan survei lokasi.</p>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Proyek Operasional --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-3">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <span class="text-emerald-600">🏗️</span> Status Proyek Operasional
                        </h4>
                        @if ($order->project)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                {{ strtoupper($order->project->status) }}
                            </span>
                        @endif
                    </div>

                    @if ($order->project)
                        <div class="text-xs space-y-2">
                            <p class="text-slate-600">
                                No. Proyek: <strong class="text-slate-900 font-mono">{{ $order->project->project_number }}</strong>
                            </p>
                            <p class="text-slate-600">
                                Progres: <strong class="text-emerald-700">{{ $order->project->progress }}%</strong>
                            </p>
                            <div class="pt-2">
                                <a href="{{ route('tracking.show', $order) }}"
                                   class="text-xs font-bold text-emerald-600 hover:text-emerald-800 hover:underline">
                                    Pantau Progres Pekerjaan &rarr;
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="text-xs text-slate-500 py-2">
                            <p>Proyek operasional akan dimulai setelah hasil survei lapangan disetujui dan diselesaikan.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
