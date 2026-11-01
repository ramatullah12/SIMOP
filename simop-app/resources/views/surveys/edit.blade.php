<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-blue-600">
                    <a href="{{ route('surveys.index') }}" class="hover:underline">Survei</a>
                    <span>/</span>
                    <a href="{{ route('surveys.show', $survey) }}" class="hover:underline">#{{ $survey->id }}</a>
                    <span>/</span>
                    <span class="text-slate-500">Edit Pengajuan</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight sm:text-2xl">
                    {{ $survey->status === \App\Models\Survey::STATUS_REJECTED ? 'Perbaiki Pengajuan Survei' : 'Ubah Draf Pengajuan Survei' }}
                </h2>
                <p class="text-xs text-slate-500 sm:text-sm">
                    {{ $survey->customer_display_name }} &bull; Pesanan: <span class="font-semibold text-slate-700">{{ $survey->order?->order_number ?? '-' }}</span>
                </p>
            </div>
            <div>
                <a href="{{ route('surveys.show', $survey) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-300 bg-white text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                    &larr; Kembali ke Rincian
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Catatan Penolakan Direktur jika Rejection --}}
            @if ($survey->status === \App\Models\Survey::STATUS_REJECTED && $survey->rejection_notes)
                <div class="rounded-2xl bg-rose-50 border-2 border-rose-300 p-6 text-sm text-rose-900 space-y-2 shadow-sm">
                    <div class="flex items-center justify-between pb-2 border-b border-rose-200">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-rose-600 text-white font-extrabold text-xs flex items-center justify-center">!</span>
                            <span class="font-extrabold uppercase tracking-wider text-xs text-rose-800">Catatan Revisi dari Direktur</span>
                        </div>
                        <span class="text-xs text-rose-600">{{ $survey->rejected_at?->format('d M Y, H:i') }} WIB</span>
                    </div>
                    <div class="p-4 bg-white rounded-xl border border-rose-200 text-rose-900 text-xs leading-relaxed whitespace-pre-line shadow-inner">
                        {{ $survey->rejection_notes }}
                    </div>
                    <p class="text-xs text-rose-700">Silakan sesuaikan data di bawah ini dan kirimkan kembali untuk ditinjau ulang oleh Direktur.</p>
                </div>
            @endif

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
                <form method="POST" action="{{ route('surveys.update', $survey) }}" class="space-y-8">
                    @csrf
                    @method('PUT')

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
                                    Pesanan Terkait <span class="text-rose-500">*</span>
                                </label>
                                <select name="order_id" required
                                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                    @foreach ($orders as $order)
                                        <option value="{{ $order->id }}" @selected(old('order_id', $survey->order_id) == $order->id)>
                                            {{ $order->order_number }} — {{ $order->company_name }} ({{ $order->event_name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nama Pelanggan / Perusahaan <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="customer_name" required
                                       value="{{ old('customer_name', $survey->customer_name ?: $survey->order?->company_name) }}"
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Alamat Lengkap Lokasi Survei <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="location" required
                                       value="{{ old('location', $survey->location) }}"
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
                                <select name="survey_type" required
                                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                    @foreach ($surveyTypes as $type)
                                        <option value="{{ $type }}" @selected(old('survey_type', $survey->survey_type) === $type)>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Tujuan Pelaksanaan Survei <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="purpose" rows="3" required
                                          class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm leading-relaxed">{{ old('purpose', $survey->purpose) }}</textarea>
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
                                Penugasan Petugas & Jadwal Survei
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Petugas Survei Lapangan
                                </label>
                                <select name="assigned_to"
                                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                    <option value="">-- Pilih Petugas Survei --</option>
                                    @foreach ($fieldOfficers as $officer)
                                        <option value="{{ $officer->id }}" @selected(old('assigned_to', $survey->assigned_to) == $officer->id)>
                                            {{ $officer->name }} ({{ $officer->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Jadwal Pelaksanaan Survei
                                </label>
                                <input type="datetime-local" name="scheduled_at"
                                       value="{{ old('scheduled_at', $survey->scheduled_at?->format('Y-m-d\TH:i')) }}"
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Catatan Tambahan untuk Direktur (Opsional)
                                </label>
                                <textarea name="notes" rows="2"
                                          class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm leading-relaxed">{{ old('notes', $survey->notes) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <a href="{{ route('surveys.show', $survey) }}"
                           class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                            Batal & Kembali
                        </a>

                        <div class="flex items-center gap-3">
                            <button type="submit" name="action" value="draft"
                                    class="px-5 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-200 transition">
                                Simpan Perubahan
                            </button>
                            <button type="submit" name="action" value="submit"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-extrabold hover:bg-blue-700 shadow-md transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                {{ $survey->status === \App\Models\Survey::STATUS_REJECTED ? 'Simpan & Kirim Ulang ke Direktur' : 'Simpan & Kirim ke Direktur' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
