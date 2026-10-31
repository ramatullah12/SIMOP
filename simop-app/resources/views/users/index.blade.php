<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Manajemen Pengguna
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola akun pengguna sistem (Admin, Direktur, Petugas Lapangan, dan Klien).
                </p>
            </div>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md font-medium text-sm text-white hover:bg-blue-700">
                Tambah Pengguna
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6">
            @if (session('success'))
                <div class="rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari Nama / Email</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Ketik nama atau alamat email..."
                               class="w-full rounded-md border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Peran</label>
                        <select name="role_id" class="w-full rounded-md border-gray-300 text-sm">
                            <option value="">Semua Peran</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" @selected((string) $roleId === (string) $role->id)>
                                    {{ $role->name === 'Client' ? 'Klien' : $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-medium hover:bg-gray-900">
                            Saring
                        </button>
                        @if ($search || $roleId)
                            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200">
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
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama Pengguna</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Email</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Peran</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Terdaftar</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($users as $item)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-gray-900">
                                        {{ $item->name }}
                                        @if ($item->id === auth()->id())
                                            <span class="ml-1.5 px-2 py-0.5 rounded bg-blue-50 text-blue-700 text-xs font-medium">Akun Anda</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $item->email }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2.5 py-1 rounded bg-gray-100 text-gray-800 text-xs font-medium">
                                            {{ ($item->role?->name === 'Client' ? 'Klien' : $item->role?->name) ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">
                                        {{ $item->created_at?->format('d/m/Y') ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.users.show', $item) }}"
                                               class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium hover:bg-gray-200">
                                                Rincian
                                            </a>
                                            <a href="{{ route('admin.users.edit', $item) }}"
                                               class="px-2.5 py-1 bg-blue-600 text-white rounded text-xs font-medium hover:bg-blue-700">
                                                Ubah
                                            </a>
                                            @if ($item->id !== auth()->id())
                                                <form method="POST" action="{{ route('admin.users.destroy', $item) }}"
                                                      data-confirm-title="Hapus Data Pengguna"
                                                      data-confirm-detail="{{ $item->name }} ({{ $item->email }} - {{ ($item->role?->name === 'Client' ? 'Klien' : $item->role?->name) ?? '-' }})"
                                                      data-confirm-message="Akun pengguna ini akan dihapus secara permanen dan tidak dapat lagi masuk ke sistem.">
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
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                        Tidak ada data pengguna yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
