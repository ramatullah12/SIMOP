<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Buat Order & Ajukan Survey ke Admin
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="rounded-lg bg-blue-50 border border-blue-200 text-blue-900 p-4 text-sm">
                Saat Anda mengirim form ini, <strong>Pengajuan Survey</strong> otomatis dikirim ke <strong>Admin</strong>, lalu diteruskan ke <strong>Direktur</strong> untuk persetujuan <strong>Surat Perintah Survey (SPS)</strong> kepada <strong>Petugas Lapangan</strong>.
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('orders.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Perusahaan / Instansi</label>
                            <input type="text" name="company_name" value="{{ old('company_name') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('company_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama PIC / Contact Person</label>
                            <input type="text" name="contact_person" value="{{ old('contact_person', auth()->user()->name) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('contact_person') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">No. Telepon / WhatsApp</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('phone') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Event / Pekerjaan</label>
                            <input type="text" name="event_name" value="{{ old('event_name') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('event_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Lokasi Event & Survey</label>
                            <input type="text" name="event_location" value="{{ old('event_location') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('event_location') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Pelaksanaan Event</label>
                            <input type="date" name="event_date" value="{{ old('event_date') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('event_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi Kebutuhan Pekerjaan & Survey</label>
                        <textarea name="description" rows="5" required
                                  placeholder="Jelaskan kebutuhan event, perlengkapan (kursi/tenda/booth), dan area yang perlu disurvey..."
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description') }}</textarea>
                        @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('orders.index') }}"
                           class="px-4 py-2 rounded-md border border-gray-300 text-gray-700">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 font-medium">
                            Kirim Order & Pengajuan Survey ke Admin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
