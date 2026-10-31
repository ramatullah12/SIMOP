<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Ubah Data Pegawai — {{ $employee->employee_code }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Perbarui informasi pegawai {{ $employee->name }}.
                </p>
            </div>
            <a href="{{ route('employees.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                Kembali ke Data Pegawai
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                @if ($errors->any())
                    <div class="mb-4 rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3">
                        <ul class="list-disc pl-5 text-sm space-y-1">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('employees.update', $employee) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap Pegawai</label>
                            <input type="text" name="name" value="{{ old('name', $employee->name) }}" required
                                   class="w-full rounded-md border-gray-300 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                            <input type="text" name="position" value="{{ old('position', $employee->position) }}" required
                                   class="w-full rounded-md border-gray-300 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Divisi / Bagian</label>
                            <input type="text" name="department" value="{{ old('department', $employee->department) }}"
                                   class="w-full rounded-md border-gray-300 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk</label>
                            <input type="date" name="join_date" value="{{ old('join_date', $employee->join_date?->format('Y-m-d')) }}"
                                   class="w-full rounded-md border-gray-300 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}"
                                   class="w-full rounded-md border-gray-300 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $employee->email) }}"
                                   class="w-full rounded-md border-gray-300 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea name="address" rows="2" class="w-full rounded-md border-gray-300 text-sm">{{ old('address', $employee->address) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Pegawai</label>
                            <select name="status" required class="w-full rounded-md border-gray-300 text-sm">
                                <option value="aktif" @selected(old('status', $employee->status) === 'aktif')>Aktif</option>
                                <option value="nonaktif" @selected(old('status', $employee->status) === 'nonaktif')>Nonaktif</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                            <input type="text" name="notes" value="{{ old('notes', $employee->notes) }}"
                                   class="w-full rounded-md border-gray-300 text-sm">
                        </div>
                    </div>

                    <div class="pt-2 flex items-center gap-3">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
