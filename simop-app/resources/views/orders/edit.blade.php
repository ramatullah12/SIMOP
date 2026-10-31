<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Ubah Pesanan — {{ $order->order_number }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('orders.update', $order) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Perusahaan / Instansi</label>
                            <input type="text" name="company_name" value="{{ old('company_name', $order->company_name) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('company_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Penanggung Jawab (PIC) / Narahubung</label>
                            <input type="text" name="contact_person" value="{{ old('contact_person', $order->contact_person) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('contact_person') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">No. Telepon / WhatsApp</label>
                            <input type="text" name="phone" value="{{ old('phone', $order->phone) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('phone') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" value="{{ old('email', $order->email) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Kegiatan / Pekerjaan</label>
                            <input type="text" name="event_name" value="{{ old('event_name', $order->event_name) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('event_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Lokasi Kegiatan</label>
                            <input type="text" name="event_location" value="{{ old('event_location', $order->event_location) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('event_location') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Kegiatan</label>
                            <input type="date" name="event_date"
                                   value="{{ old('event_date', $order->event_date?->format('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('event_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi Kebutuhan Pekerjaan</label>
                        <textarea name="description" rows="5" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description', $order->description) }}</textarea>
                        @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if(auth()->user()->role?->slug === 'admin')
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status Pesanan</label>
                            <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @foreach (['masuk', 'diproses', 'menunggu_survey', 'survey_selesai', 'disetujui', 'ditolak', 'selesai'] as $status)
                                    <option value="{{ $status }}" @selected(old('status', $order->status) === $status)>
                                        {{ strtoupper(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Catatan Admin</label>
                            <textarea name="admin_notes" rows="4"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                            @error('admin_notes') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('orders.show', $order) }}"
                           class="px-4 py-2 rounded-md border border-gray-300 text-gray-700">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

