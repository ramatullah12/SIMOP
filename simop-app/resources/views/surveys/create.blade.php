<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $role === 'client' ? 'Ajukan Permintaan Survei ke Admin' : 'Buat & Kirim Pengajuan Survei ke Direktur' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="rounded-lg bg-blue-50 border border-blue-200 text-blue-900 p-4 text-sm">
                Pengajuan survei dari Klien akan diterima oleh Admin untuk dijadwalkan dan diteruskan ke Direktur. Setelah disetujui Direktur, Surat Perintah Survei akan dikirimkan ke Petugas Lapangan.
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if ($orders->isEmpty() && $role === 'client')
                    <div class="text-center py-6 space-y-3">
                        <p class="text-gray-700 font-medium">Semua pesanan Anda sudah memiliki pengajuan survei aktif, atau Anda belum membuat pesanan baru.</p>
                        <a href="{{ route('orders.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md text-white text-sm font-medium hover:bg-blue-700">
                            + Buat Pesanan & Pengajuan Survei Baru
                        </a>
                    </div>
                @else
                    <form method="POST" action="{{ route('surveys.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pilih Pesanan / Kegiatan</label>
                            <select name="order_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">-- Pilih Pesanan --</option>
                                @foreach ($orders as $order)
                                    <option value="{{ $order->id }}" @selected(old('order_id', $selectedOrderId) == $order->id)>
                                        {{ $order->order_number }} - {{ $order->company_name }} ({{ $order->event_name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('order_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        @if ($role === 'admin')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Petugas Lapangan yang Diajukan ke Direktur</label>
                                    <select name="assigned_to" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="">-- Pilih Petugas Lapangan --</option>
                                        @foreach ($fieldOfficers as $officer)
                                            <option value="{{ $officer->id }}" @selected(old('assigned_to') == $officer->id)>
                                                {{ $officer->name }} ({{ $officer->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_to') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Jadwal Pelaksanaan Survei</label>
                                    <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @error('scheduled_at') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Usulan Waktu Survei (Opsional)</label>
                                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @error('scheduled_at') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Lokasi Survei</label>
                            <input type="text" name="location" value="{{ old('location') }}" required
                                   placeholder="Masukkan alamat lengkap lokasi survei"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('location') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Catatan Pengajuan Survei</label>
                            <textarea name="notes" rows="3"
                                      placeholder="Catatan kebutuhan survei di lokasi..."
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('notes') }}</textarea>
                            @error('notes') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('surveys.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-gray-700">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 font-medium">
                                {{ $role === 'client' ? 'Kirim Pengajuan Survei ke Admin' : 'Kirim ke Direktur (Minta Persetujuan SPS)' }}
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
