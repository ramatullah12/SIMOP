<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-blue-600">
                    <a href="{{ route('orders.index') }}" class="hover:underline">Pesanan</a>
                    <span>/</span>
                    <a href="{{ route('orders.show', $order) }}" class="hover:underline">{{ $order->order_number }}</a>
                    <span>/</span>
                    <span class="text-slate-500">Ubah Data</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight sm:text-2xl">
                    Ubah Pesanan — {{ $order->order_number }}
                </h2>
                <p class="text-xs text-slate-500 sm:text-sm">
                    {{ $order->company_name }} &bull; {{ $order->event_name }}
                </p>
            </div>
            <div>
                <a href="{{ route('orders.show', $order) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-300 bg-white text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                    &larr; Kembali ke Rincian
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
                        Periksa kembali data yang Anda masukkan:
                    </p>
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <form method="POST" action="{{ route('orders.update', $order) }}" class="space-y-8">
                    @csrf
                    @method('PUT')

                    {{-- 1. Data Pelanggan --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                            <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs font-bold shadow-sm">
                                1
                            </div>
                            <h3 class="font-extrabold text-slate-900 text-sm uppercase tracking-wider">
                                Data Pelanggan & Narahubung
                            </h3>
                        </div>

                        @if ($role === 'admin')
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Akun Pelanggan (Klien) Terdaftar <span class="text-rose-500">*</span>
                                </label>
                                <select name="client_id" required
                                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm font-medium">
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" @selected(old('client_id', $order->client_id) == $client->id)>
                                            {{ $client->name }} — {{ $client->email }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nama Perusahaan / Instansi <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="company_name" value="{{ old('company_name', $order->company_name) }}" required
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nama Penanggung Jawab (PIC) <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="contact_person" value="{{ old('contact_person', $order->contact_person) }}" required
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    No. Telepon / WhatsApp <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="phone" value="{{ old('phone', $order->phone) }}" required
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Email
                                </label>
                                <input type="email" name="email" value="{{ old('email', $order->email) }}"
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>
                        </div>
                    </div>

                    {{-- 2. Detail Kegiatan --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                            <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs font-bold shadow-sm">
                                2
                            </div>
                            <h3 class="font-extrabold text-slate-900 text-sm uppercase tracking-wider">
                                Rincian Kegiatan & Lokasi
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nama Kegiatan <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="event_name" value="{{ old('event_name', $order->event_name) }}" required
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Tanggal Pelaksanaan Kegiatan
                                </label>
                                <input type="date" name="event_date"
                                       value="{{ old('event_date', $order->event_date?->format('Y-m-d')) }}"
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Alamat Lokasi Kegiatan & Survei <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="event_location" value="{{ old('event_location', $order->event_location) }}" required
                                       class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Deskripsi Kebutuhan Pekerjaan <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="description" rows="4" required
                                          class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm leading-relaxed">{{ old('description', $order->description) }}</textarea>
                            </div>

                            @if ($role === 'admin')
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Status Pesanan <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="status" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                        @foreach ($statuses as $k => $v)
                                            <option value="{{ $k }}" @selected(old('status', $order->status) === $k)>
                                                {{ strtoupper($v) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Catatan Internal Admin (Opsional)
                                    </label>
                                    <textarea name="admin_notes" rows="3"
                                              class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm leading-relaxed">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <a href="{{ route('orders.show', $order) }}"
                           class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                            Batal & Kembali
                        </a>

                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl text-sm font-extrabold hover:bg-blue-700 shadow-md hover:shadow-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
