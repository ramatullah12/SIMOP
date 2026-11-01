<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-blue-600">
                    <a href="{{ route('orders.index') }}" class="hover:underline">Pesanan</a>
                    <span>/</span>
                    <span class="text-slate-500">Buat Pesanan Baru</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight sm:text-2xl">
                    {{ $role === 'admin' ? 'Buat Pesanan untuk Pelanggan (Admin)' : 'Buat Pesanan Kegiatan Baru' }}
                </h2>
                <p class="text-xs text-slate-500 sm:text-sm">
                    @if ($role === 'admin')
                        Admin menginput pesanan atas nama pelanggan terdaftar ke dalam sistem operasional.
                    @else
                        Kirimkan rincian kebutuhan kegiatan Anda untuk ditinjau oleh tim Admin SIMOP.
                    @endif
                </p>
            </div>
            <div>
                <a href="{{ route('orders.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-300 bg-white text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                    &larr; Kembali ke Daftar Pesanan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Info Banner --}}
            @if ($role === 'admin')
                <div class="rounded-2xl bg-blue-50/80 border border-blue-200 p-5 text-xs text-blue-900 flex items-start gap-3 shadow-sm">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-sm text-blue-950">Panduan Input Pesanan oleh Admin</p>
                        <p class="mt-0.5 leading-relaxed text-blue-800">
                            Pilih data pelanggan dari akun klien yang sudah terdaftar. Sistem akan secara otomatis mengaitkan pesanan dengan ID pelanggan tersebut sehingga pelanggan dapat memantau status pesanan dan survei melalui dashboard akun miliknya.
                        </p>
                    </div>
                </div>
            @else
                <div class="rounded-2xl bg-blue-50/80 border border-blue-200 p-5 text-xs text-blue-900 flex items-start gap-3 shadow-sm">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-sm text-blue-950">Pengajuan Pesanan Kegiatan</p>
                        <p class="mt-0.5 leading-relaxed text-blue-800">
                            Kirimkan data kegiatan Anda dengan lengkap. Setelah pesanan masuk, Admin akan meninjau spesifikasi dan membuat pengajuan survei lokasi.
                        </p>
                    </div>
                </div>
            @endif

            {{-- Error Flash --}}
            @if ($errors->any())
                <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 shadow-sm text-xs text-rose-800 space-y-1">
                    <p class="font-bold text-sm text-rose-900 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Mohon lengkapi dan periksa kembali data isian:
                    </p>
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <form method="POST" action="{{ route('orders.store') }}" class="space-y-8" id="orderCreateForm">
                    @csrf

                    {{-- 1. Identitas Pelanggan --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                            <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs font-bold shadow-sm">
                                1
                            </div>
                            <h3 class="font-extrabold text-slate-900 text-sm uppercase tracking-wider">
                                Identitas Pelanggan
                            </h3>
                        </div>

                        {{-- Pemilihan Klien Khusus Admin --}}
                        @if ($role === 'admin')
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Pilih Akun Pelanggan (Klien) Terdaftar <span class="text-rose-500">*</span>
                                </label>
                                <select name="client_id" id="client_select" required
                                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm font-medium">
                                    <option value="">-- Pilih Akun Pelanggan Terdaftar --</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}"
                                                data-name="{{ $client->name }}"
                                                data-email="{{ $client->email }}"
                                                @selected(old('client_id', $selectedClientId) == $client->id)>
                                            {{ $client->name }} — {{ $client->email }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-slate-500 mt-1">
                                    Pesanan akan terhubung otomatis ke akun pelanggan yang dipilih. Belum punya akun?
                                    <a href="{{ route('admin.users.create') }}" target="_blank" class="text-blue-600 hover:underline font-semibold">
                                        Tambah akun klien di sini &rarr;
                                    </a>
                                </p>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nama Perusahaan / Instansi Pelanggan <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="company_name" id="company_name"
                                       value="{{ old('company_name') }}" required
                                       placeholder="PT Contoh Sukses / Nama Lembaga"
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nama Penanggung Jawab (PIC) / Narahubung <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="contact_person" id="contact_person"
                                       value="{{ old('contact_person', $role === 'client' ? auth()->user()->name : '') }}" required
                                       placeholder="Nama lengkap narahubung"
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    No. Telepon / WhatsApp <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="phone" id="phone"
                                       value="{{ old('phone') }}" required
                                       placeholder="08xxxxxxxxxx"
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Email Narahubung
                                </label>
                                <input type="email" name="email" id="email"
                                       value="{{ old('email', $role === 'client' ? auth()->user()->email : '') }}"
                                       placeholder="kontak@perusahaan.com"
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>
                        </div>
                    </div>

                    {{-- 2. Detail Kegiatan & Kebutuhan --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                            <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs font-bold shadow-sm">
                                2
                            </div>
                            <h3 class="font-extrabold text-slate-900 text-sm uppercase tracking-wider">
                                Rincian Kegiatan & Lokasi Pekerjaan
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nama Kegiatan / Acara <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="event_name" value="{{ old('event_name') }}" required
                                       placeholder="Pameran Teknologi 2026 / Konser Akbar"
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Tanggal Pelaksanaan Kegiatan
                                </label>
                                <input type="date" name="event_date" value="{{ old('event_date') }}"
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Alamat Lengkap Lokasi Kegiatan & Survei <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="event_location" value="{{ old('event_location') }}" required
                                       placeholder="Gedung Exhibition Hall B, Jalan Sudirman No. 10, Jakarta"
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Deskripsi Spesifikasi Kebutuhan & Survei <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="description" rows="4" required
                                          placeholder="Jelaskan kebutuhan pekerjaan operasional (misal: pemasangan panggung rigging 10x8m, backdrop LED, 5 unit booth pameran, survei daya listrik dan akses loading barang)..."
                                          class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm leading-relaxed">{{ old('description') }}</textarea>
                            </div>

                            @if ($role === 'admin')
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Status Awal Pesanan
                                    </label>
                                    <select name="status" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                        <option value="masuk" @selected(old('status') === 'masuk')>MASUK (Baru)</option>
                                        <option value="diproses" @selected(old('status') === 'diproses')>DIPROSES</option>
                                        <option value="menunggu_survey" @selected(old('status') === 'menunggu_survey')>MENUNGGU SURVEI</option>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Catatan Internal Admin (Opsional)
                                    </label>
                                    <textarea name="admin_notes" rows="2"
                                              placeholder="Catatan internal tim operasional terkait pesanan ini..."
                                              class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm leading-relaxed">{{ old('admin_notes') }}</textarea>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <a href="{{ route('orders.index') }}"
                           class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                            Batal & Kembali
                        </a>

                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl text-sm font-extrabold hover:bg-blue-700 shadow-md hover:shadow-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $role === 'admin' ? 'Simpan Pesanan Pelanggan' : 'Kirim Pesanan ke Admin' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($role === 'admin')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const clientSelect = document.getElementById('client_select');
                const contactInput = document.getElementById('contact_person');
                const emailInput = document.getElementById('email');
                const companyInput = document.getElementById('company_name');

                if (clientSelect) {
                    clientSelect.addEventListener('change', function () {
                        const opt = clientSelect.options[clientSelect.selectedIndex];
                        if (opt && opt.value) {
                            if (!contactInput.value || contactInput.dataset.autoFilled === 'true') {
                                contactInput.value = opt.dataset.name || '';
                                contactInput.dataset.autoFilled = 'true';
                            }
                            if (!emailInput.value || emailInput.dataset.autoFilled === 'true') {
                                emailInput.value = opt.dataset.email || '';
                                emailInput.dataset.autoFilled = 'true';
                            }
                            if (!companyInput.value || companyInput.dataset.autoFilled === 'true') {
                                companyInput.value = opt.dataset.name || '';
                                companyInput.dataset.autoFilled = 'true';
                            }
                        }
                    });

                    contactInput.addEventListener('input', () => contactInput.dataset.autoFilled = 'false');
                    emailInput.addEventListener('input', () => emailInput.dataset.autoFilled = 'false');
                    companyInput.addEventListener('input', () => companyInput.dataset.autoFilled = 'false');
                }
            });
        </script>
    @endif
</x-app-layout>
