<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $survey->status === \App\Models\Survey::STATUS_REJECTED ? 'Perbaiki Pengajuan Survei' : 'Ubah Draf Pengajuan Survei' }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $survey->customer_display_name }} — Pesanan: {{ $survey->order?->order_number ?? '-' }}
                </p>
            </div>
            <a href="{{ route('surveys.show', $survey) }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Kembali ke Rincian
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($survey->status === \App\Models\Survey::STATUS_REJECTED && $survey->rejection_notes)
                <div class="rounded-lg bg-red-50 border-2 border-red-300 p-4 text-sm text-red-900">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-600 text-white font-bold text-xs shrink-0">!</span>
                        <div>
                            <p class="font-bold">Alasan Penolakan / Catatan Revisi dari Direktur:</p>
                            <p class="mt-1 whitespace-pre-line text-red-800">{{ $survey->rejection_notes }}</p>
                            <p class="text-xs text-red-600 mt-2">Ditolak pada: {{ $survey->rejected_at?->format('d M Y, H:i') ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            @endif

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
                <form method="POST" action="{{ route('surveys.update', $survey) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- 1. Data Pelanggan & Pesanan -->
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-blue-700 border-b pb-2 mb-4">
                            1. Data Pelanggan & Pesanan
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Pesanan Terkait <span class="text-red-500">*</span>
                                </label>
                                <select name="order_id" required class="w-full rounded-md border-gray-300 text-sm">
                                    @foreach ($orders as $order)
                                        <option value="{{ $order->id }}" @selected(old('order_id', $survey->order_id) == $order->id)>
                                            {{ $order->order_number }} — {{ $order->company_name }} ({{ $order->event_name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Pelanggan / Perusahaan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="customer_name" required
                                       value="{{ old('customer_name', $survey->customer_name ?: $survey->order?->company_name) }}"
                                       class="w-full rounded-md border-gray-300 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Alamat Lengkap Lokasi Survei <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="location" required
                                       value="{{ old('location', $survey->location) }}"
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
                                <select name="survey_type" required class="w-full rounded-md border-gray-300 text-sm">
                                    @foreach ($surveyTypes as $type)
                                        <option value="{{ $type }}" @selected(old('survey_type', $survey->survey_type) === $type)>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tujuan Pelaksanaan Survei <span class="text-red-500">*</span>
                                </label>
                                <textarea name="purpose" rows="3" required
                                          class="w-full rounded-md border-gray-300 text-sm">{{ old('purpose', $survey->purpose) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Penugasan Petugas & Jadwal -->
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-blue-700 border-b pb-2 mb-4">
                            3. Penugasan Petugas & Jadwal Survei
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Petugas Survei (Petugas Lapangan)
                                </label>
                                <select name="assigned_to" class="w-full rounded-md border-gray-300 text-sm">
                                    <option value="">-- Pilih Petugas Survei --</option>
                                    @foreach ($fieldOfficers as $officer)
                                        <option value="{{ $officer->id }}" @selected(old('assigned_to', $survey->assigned_to) == $officer->id)>
                                            {{ $officer->name }} ({{ $officer->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Jadwal Pelaksanaan Survei
                                </label>
                                <input type="datetime-local" name="scheduled_at"
                                       value="{{ old('scheduled_at', $survey->scheduled_at?->format('Y-m-d\TH:i')) }}"
                                       class="w-full rounded-md border-gray-300 text-sm">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Keterangan Tambahan / Catatan untuk Direktur
                                </label>
                                <textarea name="notes" rows="2"
                                          class="w-full rounded-md border-gray-300 text-sm">{{ old('notes', $survey->notes) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t flex flex-col sm:flex-row items-center justify-between gap-3">
                        <a href="{{ route('surveys.show', $survey) }}" class="text-sm text-gray-600 hover:text-gray-900">
                            Batal
                        </a>

                        <div class="flex items-center gap-2">
                            <button type="submit" name="action" value="draft"
                                    class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md text-sm font-medium hover:bg-gray-200">
                                Simpan Perubahan
                            </button>
                            <button type="submit" name="action" value="submit"
                                    class="px-5 py-2 bg-blue-600 text-white rounded-md text-sm font-semibold hover:bg-blue-700 shadow-sm">
                                {{ $survey->status === \App\Models\Survey::STATUS_REJECTED ? 'Simpan & Kirim Ulang ke Direktur' : 'Simpan & Kirim ke Direktur' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
