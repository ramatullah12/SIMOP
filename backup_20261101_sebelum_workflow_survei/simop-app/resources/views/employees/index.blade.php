<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Data Pegawai
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $role === 'admin' ? 'Kelola data pegawai operasional perusahaan.' : 'Daftar data pegawai operasional perusahaan.' }}
                </p>
            </div>
            @if ($role === 'admin')
                <a href="{{ route('employees.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md font-medium text-sm text-white hover:bg-blue-700">
                    Tambah Pegawai
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6">
            @if (session('success'))
                <div class="rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <form method="GET" action="{{ route('employees.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari Pegawai</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Kode, nama, jabatan, atau divisi..."
                               class="w-full rounded-md border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full rounded-md border-gray-300 text-sm">
                            <option value="">Semua Status</option>
                            <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                            <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-medium hover:bg-gray-900">
                            Saring
                        </button>
                        @if ($search || $status)
                            <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200">
                                Atur Ulang
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Kode</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama Pegawai</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Jabatan & Divisi</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Kontak</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal Masuk</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($employees as $emp)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $emp->employee_code }}</td>
                                    <td class="px-4 py-3 font-semibold text-gray-900">{{ $emp->name }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900">{{ $emp->position }}</p>
                                        <p class="text-xs text-gray-500">{{ $emp->department ?: '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="text-gray-800">{{ $emp->phone ?: '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $emp->email ?: '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $emp->join_date?->format('d/m/Y') ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($emp->status === 'aktif')
                                            <span class="px-2.5 py-1 rounded bg-green-100 text-green-800 text-xs font-medium">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded bg-gray-100 text-gray-600 text-xs font-medium">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('employees.show', $emp) }}"
                                               class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium hover:bg-gray-200">
                                                Rincian
                                            </a>
                                            @if ($role === 'admin')
                                                <a href="{{ route('employees.edit', $emp) }}"
                                                   class="px-2.5 py-1 bg-blue-600 text-white rounded text-xs font-medium hover:bg-blue-700">
                                                    Ubah
                                                </a>
                                                <form method="POST" action="{{ route('employees.destroy', $emp) }}"
                                                      data-confirm-title="Hapus Data Pegawai"
                                                      data-confirm-detail="{{ $emp->employee_code }} - {{ $emp->name }} ({{ $emp->position }})"
                                                      data-confirm-message="Data pegawai ini akan dihapus secara permanen dari daftar.">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-2.5 py-1 bg-red-600 text-white rounded text-xs font-medium hover:bg-red-700">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                        Belum ada data pegawai.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($employees->hasPages())
                    <div class="mt-4">
                        {{ $employees->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
