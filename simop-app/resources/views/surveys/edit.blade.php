<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $role === 'petugas-lapangan' ? 'Pelaksanaan Surat Perintah Survei (' . ($survey->sps_number ?? 'SPS') . ')' : 'Ubah Data Survei' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('surveys.update', $survey) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @if ($role === 'admin')
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pesanan</label>
                            <select name="order_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @foreach ($orders as $order)
                                    <option value="{{ $order->id }}" @selected(old('order_id', $survey->order_id) == $order->id)>
                                        {{ $order->order_number }} - {{ $order->company_name }} ({{ $order->event_name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('order_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Petugas Lapangan</label>
                                <select name="assigned_to" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @foreach ($fieldOfficers as $officer)
                                        <option value="{{ $officer->id }}" @selected(old('assigned_to', $survey->assigned_to) == $officer->id)>
                                            {{ $officer->name }} ({{ $officer->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jadwal Survei</label>
                                <input type="datetime-local" name="scheduled_at"
                                       value="{{ old('scheduled_at', $survey->scheduled_at?->format('Y-m-d\TH:i')) }}"
                                       required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @error('scheduled_at') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Lokasi Survei</label>
                            <input type="text" name="location" value="{{ old('location', $survey->location) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('location') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    @else
                        <div class="p-4 rounded-lg bg-blue-50 border border-blue-200 text-sm space-y-1">
                            <p><strong>Nomor Surat Perintah Survei:</strong> {{ $survey->sps_number }}</p>
                            <p><strong>Kegiatan / Lokasi:</strong> {{ $survey->order?->event_name }} — {{ $survey->location }}</p>
                            <p><strong>Disetujui Direktur:</strong> {{ $survey->approver?->name ?? 'Direktur' }} ({{ $survey->approved_at?->format('d M Y H:i') }})</p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status Pelaksanaan Survei</label>
                        <select name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @foreach (['dijadwalkan' => 'SPS Diterima / Dijadwalkan', 'berlangsung' => 'Survei Sedang Berlangsung di Lokasi', 'selesai' => 'Survei Selesai Dilaksanakan'] as $val => $label)
                                <option value="{{ $val }}" @selected(old('status', $survey->status) === $val)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hasil Temuan Survei Lapangan</label>
                        <textarea name="findings" rows="4"
                                  placeholder="Tuliskan hasil pengukuran area, kebutuhan kursi/barang, akses bongkar muat, dan kondisi lapangan..."
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('findings', $survey->findings) }}</textarea>
                        @error('findings') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Catatan Tambahan</label>
                        <textarea name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('notes', $survey->notes) }}</textarea>
                        @error('notes') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('surveys.show', $survey) }}" class="px-4 py-2 rounded-md border border-gray-300 text-gray-700">
                            Batal
                        </a>
                        <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 font-medium">
                            Simpan Hasil Survei
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
