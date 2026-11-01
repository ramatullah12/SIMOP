<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Rincian Pengguna
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Informasi akun pengguna {{ $user->name }}.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.edit', $user) }}"
                   class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                    Ubah Pengguna
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Nama Lengkap</p>
                        <p class="font-semibold text-gray-900 mt-0.5">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Alamat Email</p>
                        <p class="font-semibold text-gray-900 mt-0.5">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Peran</p>
                        <p class="font-semibold text-gray-900 mt-0.5">{{ ($user->role?->name === 'Client' ? 'Klien' : $user->role?->name) ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Tanggal Terdaftar</p>
                        <p class="font-semibold text-gray-900 mt-0.5">{{ $user->created_at?->format('d/m/Y H:i') ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
