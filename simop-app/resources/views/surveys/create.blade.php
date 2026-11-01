<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-blue-600">
                    <a href="{{ route('surveys.index') }}" class="hover:underline">Survei</a>
                    <span>/</span>
                    <span class="text-slate-500">Pengajuan Baru</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight sm:text-2xl">
                    Buat Pengajuan Survei Baru
                </h2>
                <p class="text-xs text-slate-500 sm:text-sm">
                    Admin membuat pengajuan survei berdasarkan kebutuhan pelanggan untuk dikirimkan kepada Direktur.
                </p>
            </div>
            <div>
                <a href="{{ route('surveys.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-300 bg-white text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                    &larr; Kembali ke Daftar Survei
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if ($errors->any())
                <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 shadow-sm text-xs text-rose-800 space-y-1">
                    <p class="font-bold text-sm text-rose-900 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Periksa kembali data pengajuan Anda:
                    </p>
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <form method="POST" action="{{ route('surveys.store') }}" class="space-y-8" id="surveyCreateForm">
                    @csrf

                    <!-- 1. Data Pelanggan & Pesanan -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                            <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs font-bold shadow-sm">
                                1
                            </div>
                            <h3 class="font-extrabold text-slate-900 text-sm uppercase tracking-wider">
                                Data Pelanggan & Pesanan Kegiatan
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Pilih Pesanan Pelanggan <span class="text-rose-500">*</span>
                                </label>
                                <select name="order_id" id="order_id" required
                                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                    <option value="">-- Pilih Pesanan / Pelanggan --</option>
                                    @foreach ($orders as $order)
                                        <option value="{{ $order->id }}"
                                                data-customer="{{ $order->company_name }}"
                                                data-contact="{{ $order->contact_person }}"
                                                data-location="{{ $order->event_location }}"
                                                data-event="{{ $order->event_name }}"
                                                @selected(old('order_id', $selectedOrderId) == $order->id)>
                                            {{ $order->order_number }} — {{ $order->company_name }} ({{ $order->event_name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nama Pelanggan / Perusahaan <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="customer_name" id="customer_name"
                                       value="{{ old('customer_name') }}" required
                                       placeholder="Nama lengkap pelanggan / instansi"
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Alamat Lengkap Lokasi Survei <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="location" id="location"
                                       value="{{ old('location') }}" required
                                       placeholder="Gedung, jalan, kota lokasi survei"
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <!-- 2. Kebutuhan & Tujuan Survei -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                            <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs font-bold shadow-sm">
                                2
                            </div>
                            <h3 class="font-extrabold text-slate-900 text-sm uppercase tracking-wider">
                                Kebutuhan & Tujuan Survei
                            </h3>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Jenis Kebutuhan Survei <span class="text-rose-500">*</span>
                                </label>
                                <select name="survey_type" id="survey_type" required
                                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                    <option value="">-- Pilih Jenis Survei --</option>
                                    @foreach ($surveyTypes as $type)
                                        <option value="{{ $type }}" @selected(old('survey_type') === $type)>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Tujuan Pelaksanaan Survei <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="purpose" rows="3" required
                                          placeholder="Jelaskan secara spesifik tujuan survei (misal: pengukuran layout panggung dan booth, kapasitas daya listrik venue, rute loading barang, dll.)"
                                          class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm leading-relaxed">{{ old('purpose') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Penugasan Petugas & Jadwal -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                            <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs font-bold shadow-sm">
                                3
                            </div>
                            <h3 class="font-extrabold text-slate-900 text-sm uppercase tracking-wider">
                                Rencana Penugasan & Jadwal Survei
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Petugas Lapangan yang Ditugaskan
                                </label>
                                <select name="assigned_to"
                                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                    <option value="">-- Pilih Petugas Survei --</option>
                                    @foreach ($fieldOfficers as $officer)
                                        <option value="{{ $officer->id }}" @selected(old('assigned_to') == $officer->id)>
                                            {{ $officer->name }} ({{ $officer->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-slate-500 mt-1">Wajib dipilih sebelum dikirimkan kepada Direktur.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Rencana Jadwal Survei
                                </label>
                                <input type="datetime-local" name="scheduled_at"
                                       value="{{ old('scheduled_at') }}"
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                <p class="text-[11px] text-slate-500 mt-1">Wajib ditentukan sebelum pengajuan diajukan.</p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Catatan / Keterangan Tambahan untuk Direktur (Opsional)
                                </label>
                                <textarea name="notes" rows="2"
                                          placeholder="Catatan pengantar khusus dari Admin untuk bahan pertimbangan persetujuan Direktur..."
                                          class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm leading-relaxed">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <a href="{{ route('surveys.index') }}"
                           class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                            Batal & Kembali
                        </a>

                        <div class="flex items-center gap-3">
                            <button type="submit" name="action" value="draft"
                                    class="px-5 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-200 transition">
                                Simpan sebagai Draf
                            </button>
                            <button type="submit" name="action" value="submit"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-extrabold hover:bg-blue-700 shadow-md transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                Simpan & Kirim ke Direktur
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const orderSelect = document.getElementById('order_id');
            const customerInput = document.getElementById('customer_name');
            const locationInput = document.getElementById('location');

            function syncOrder() {
                const opt = orderSelect.options[orderSelect.selectedIndex];
                if (!opt || !opt.value) return;

                if (!customerInput.value || customerInput.dataset.autoFilled === 'true') {
                    customerInput.value = opt.dataset.customer || '';
                    customerInput.dataset.autoFilled = 'true';
                }
                if (!locationInput.value || locationInput.dataset.autoFilled === 'true') {
                    locationInput.value = opt.dataset.location || '';
                    locationInput.dataset.autoFilled = 'true';
                }
            }

            customerInput.addEventListener('input', () => customerInput.dataset.autoFilled = 'false');
            locationInput.addEventListener('input', () => locationInput.dataset.autoFilled = 'false');
            orderSelect.addEventListener('change', syncOrder);

            if (orderSelect.value && (!customerInput.value || !locationInput.value)) {
                syncOrder();
            }
        });
    </script>
</x-app-layout>
