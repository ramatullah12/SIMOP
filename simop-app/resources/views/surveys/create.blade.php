<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Buat Pengajuan Survei Baru
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Admin membuat pengajuan survei berdasarkan kebutuhan pelanggan untuk dikirimkan kepada Direktur.
                </p>
            </div>
            <a href="{{ route('surveys.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Kembali ke Daftar Survei
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($errors->any())
                <div class="rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3 text-sm">
                    <p class="font-bold mb-1">Periksa kembali data pengajuan Anda:</p>
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 p-6">
                <form method="POST" action="{{ route('surveys.store') }}" class="space-y-6" id="surveyCreateForm">
                    @csrf

                    <!-- 1. Data Pelanggan & Pesanan -->
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-blue-700 border-b pb-2 mb-4">
                            1. Data Pelanggan & Pesanan
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Pilih Pesanan Pelanggan <span class="text-red-500">*</span>
                                </label>
                                <select name="order_id" id="order_id" required class="w-full rounded-md border-gray-300 text-sm">
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Pelanggan / Perusahaan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="customer_name" id="customer_name"
                                       value="{{ old('customer_name') }}" required
                                       placeholder="Nama lengkap pelanggan / instansi"
                                       class="w-full rounded-md border-gray-300 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Alamat Lengkap Lokasi Survei <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="location" id="location"
                                       value="{{ old('location') }}" required
                                       placeholder="Gedung, jalan, kota lokasi survei"
                                       class="w-full rounded-md border-gray-300 text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- 2. Kebutuhan & Tujuan Survei -->
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-blue-700 border-b pb-2 mb-4">
                            2. Kebutuhan & Tujuan Survei
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Jenis Kebutuhan Survei <span class="text-red-500">*</span>
                                </label>
                                <select name="survey_type" id="survey_type" required class="w-full rounded-md border-gray-300 text-sm">
                                    <option value="">-- Pilih Jenis Survei --</option>
                                    @foreach ($surveyTypes as $type)
                                        <option value="{{ $type }}" @selected(old('survey_type') === $type)>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tujuan Pelaksanaan Survei <span class="text-red-500">*</span>
                                </label>
                                <textarea name="purpose" rows="3" required
                                          placeholder="Jelaskan secara spesifik tujuan survei (misal: pengukuran layout panggung dan booth, kapasitas daya listrik venue, rute loading barang, dll.)"
                                          class="w-full rounded-md border-gray-300 text-sm">{{ old('purpose') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Penugasan Petugas & Jadwal -->
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-blue-700 border-b pb-2 mb-4">
                            3. Rencana Penugasan & Jadwal Survei
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Pilih Petugas Survei (Petugas Lapangan)
                                </label>
                                <select name="assigned_to" class="w-full rounded-md border-gray-300 text-sm">
                                    <option value="">-- Pilih Petugas Survei --</option>
                                    @foreach ($fieldOfficers as $officer)
                                        <option value="{{ $officer->id }}" @selected(old('assigned_to') == $officer->id)>
                                            {{ $officer->name }} ({{ $officer->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Wajib dipilih sebelum dikirimkan ke Direktur.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Rencana Jadwal Survei
                                </label>
                                <input type="datetime-local" name="scheduled_at"
                                       value="{{ old('scheduled_at') }}"
                                       class="w-full rounded-md border-gray-300 text-sm">
                                <p class="text-xs text-gray-500 mt-1">Wajib ditentukan sebelum dikirimkan ke Direktur.</p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Keterangan Tambahan / Catatan untuk Direktur
                                </label>
                                <textarea name="notes" rows="2"
                                          placeholder="Catatan pengantar atau instruksi khusus dari Admin..."
                                          class="w-full rounded-md border-gray-300 text-sm">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t flex flex-col sm:flex-row items-center justify-between gap-3">
                        <a href="{{ route('surveys.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            Batal
                        </a>

                        <div class="flex items-center gap-2">
                            <button type="submit" name="action" value="draft"
                                    class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md text-sm font-medium hover:bg-gray-200">
                                Simpan sebagai Draf
                            </button>
                            <button type="submit" name="action" value="submit"
                                    class="px-5 py-2 bg-blue-600 text-white rounded-md text-sm font-semibold hover:bg-blue-700 shadow-sm">
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
