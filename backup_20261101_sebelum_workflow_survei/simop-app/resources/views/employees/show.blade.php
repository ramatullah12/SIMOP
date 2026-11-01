<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Rincian Pegawai — {{ $employee->employee_code }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Informasi lengkap data pegawai {{ $employee->name }}.
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if ($role === 'admin')
                    <a href="{{ route('employees.edit', $employee) }}"
                       class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                        Ubah Pegawai
                    </a>
                @endif
                <a href="{{ route('employees.index') }}"
                   class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
                    <div>
                        <p class="text-gray-500">Kode Pegawai</p>
                        <p class="font-semibold text-gray-900 mt-0.5">{{ $employee->employee_code }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Status</p>
                        <p class="mt-0.5">
                            @if ($employee->status === 'aktif')
                                <span class="px-2.5 py-0.5 rounded bg-green-100 text-green-800 text-xs font-medium">Aktif</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded bg-gray-100 text-gray-600 text-xs font-medium">Nonaktif</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500">Nama Lengkap</p>
                        <p class="font-semibold text-gray-900 mt-0.5">{{ $employee->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Jabatan</p>
                        <p class="font-semibold text-gray-900 mt-0.5">{{ $employee->position }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Divisi / Bagian</p>
                        <p class="font-semibold text-gray-900 mt-0.5">{{ $employee->department ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Tanggal Masuk</p>
                        <p class="font-semibold text-gray-900 mt-0.5">{{ $employee->join_date?->format('d M Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">No. Telepon</p>
                        <p class="font-semibold text-gray-900 mt-0.5">{{ $employee->phone ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Email</p>
                        <p class="font-semibold text-gray-900 mt-0.5">{{ $employee->email ?: '-' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-gray-500">Alamat</p>
                        <p class="font-semibold text-gray-900 mt-0.5">{{ $employee->address ?: '-' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-gray-500">Keterangan</p>
                        <p class="font-semibold text-gray-900 mt-0.5">{{ $employee->notes ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
