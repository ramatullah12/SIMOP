<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Manajemen & Verifikasi Pengguna
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola akun pengguna dan verifikasi pendaftaran baru (Admin, Direktur, Petugas Lapangan, dan Klien).
                </p>
            </div>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md font-medium text-sm text-white hover:bg-blue-700 shadow-sm transition">
                + Tambah Pengguna
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6">
            @if (session('success'))
                <div class="rounded-lg bg-green-50 border border-green-300 text-green-800 px-4 py-3 text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg bg-red-50 border border-red-300 text-red-800 px-4 py-3 text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if ($pendingVerificationCount > 0)
                <div class="rounded-xl bg-amber-50 border border-amber-300 p-4 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-start sm:items-center gap-3">
                        <div class="p-2 bg-amber-100 rounded-lg text-amber-700 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-amber-900 text-sm">Pemberitahuan Verifikasi Pengguna Baru</h4>
                            <p class="text-xs text-amber-700 mt-0.5">
                                Terdapat <strong class="underline">{{ $pendingVerificationCount }} akun pengguna baru</strong> yang belum diverifikasi oleh Administrator. Pengguna belum dapat masuk ke sistem sampai Anda memverifikasinya.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('admin.users.index', ['verification_status' => 'unverified']) }}"
                           class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-md text-xs font-semibold shadow-sm transition">
                            Lihat Yang Belum Diverifikasi
                        </a>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
                    <div class="sm:col-span-5">
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Cari Nama / Email</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Ketik nama atau alamat email..."
                               class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Peran Pengguna</label>
                        <select name="role_id" class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Peran</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" @selected((string) $roleId === (string) $role->id)>
                                    {{ $role->name === 'Client' ? 'Klien' : $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Status Verifikasi</label>
                        <select name="verification_status" class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="unverified" @selected($verificationStatus === 'unverified')>Menunggu Verifikasi (Pending)</option>
                            <option value="verified" @selected($verificationStatus === 'verified')>Sudah Terverifikasi</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2 flex items-center gap-2">
                        <button type="submit" class="w-full px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-medium hover:bg-gray-900 transition">
                            Saring
                        </button>
                        @if ($search || $roleId || $verificationStatus)
                            <a href="{{ route('admin.users.index') }}" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200 transition" title="Atur Ulang">
                                Reset
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
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Status Verifikasi</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Terdaftar</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($users as $item)
                                <tr class="{{ ! $item->isVerified() ? 'bg-amber-50/50 hover:bg-amber-50' : 'hover:bg-gray-50/80' }} transition">
                                    <td class="px-4 py-3 font-semibold text-gray-900">
                                        <div class="flex items-center gap-2">
                                            <span>{{ $item->name }}</span>
                                            @if ($item->id === auth()->id())
                                                <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 text-xs font-medium">Akun Anda</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $item->email }}</td>
                                    <td class="px-4 py-3">
                                        @if ($item->role)
                                            <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-800 text-xs font-medium">
                                                {{ $item->role->name === 'Client' ? 'Klien' : $item->role->name }}
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded bg-rose-50 text-rose-700 text-xs font-medium border border-rose-200">
                                                Belum Ditentukan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($item->isVerified())
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-200">
                                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>Terverifikasi</span>
                                            </div>
                                        @else
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold border border-amber-300 animate-pulse">
                                                <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>Menunggu Verifikasi</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 text-xs">
                                        {{ $item->created_at?->format('d/m/Y H:i') ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center flex-wrap gap-1.5">
                                            @if (! $item->isVerified())
                                                <form method="POST" action="{{ route('admin.users.verify', $item) }}"
                                                      data-confirm-title="Konfirmasi Verifikasi Pengguna"
                                                      data-confirm-detail="{{ $item->name }} ({{ $item->email }})"
                                                      data-confirm-message="Apakah Anda yakin ingin memverifikasi dan mengaktifkan akun pengguna ini? Pengguna akan dapat masuk ke sistem."
                                                      data-confirm-btn="Ya, Verifikasi Akun"
                                                      data-confirm-type="success">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-600 text-white rounded text-xs font-semibold hover:bg-emerald-700 shadow-sm transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        Verifikasi
                                                    </button>
                                                </form>
                                            @else
                                                @if ($item->id !== auth()->id())
                                                    <form method="POST" action="{{ route('admin.users.unverify', $item) }}"
                                                          data-confirm-title="Konfirmasi Pembatalan Verifikasi"
                                                          data-confirm-detail="{{ $item->name }} ({{ $item->email }})"
                                                          data-confirm-message="Apakah Anda yakin ingin membatalkan verifikasi akun pengguna ini? Pengguna tidak akan dapat masuk ke sistem sampai diverifikasi kembali."
                                                          data-confirm-btn="Ya, Batalkan Verifikasi"
                                                          data-confirm-type="warning">
                                                        @csrf
                                                        <button type="submit" class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded text-xs font-medium hover:bg-amber-200 transition" title="Nonaktifkan verifikasi">
                                                            Batal Verif
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif

                                            <a href="{{ route('admin.users.show', $item) }}"
                                               class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium hover:bg-gray-200 transition">
                                                Rincian
                                            </a>
                                            <a href="{{ route('admin.users.edit', $item) }}"
                                               class="px-2.5 py-1 bg-blue-600 text-white rounded text-xs font-medium hover:bg-blue-700 transition">
                                                Ubah
                                            </a>
                                            @if ($item->id !== auth()->id())
                                                <form method="POST" action="{{ route('admin.users.destroy', $item) }}"
                                                      data-confirm-title="Hapus Data Pengguna"
                                                      data-confirm-detail="{{ $item->name }} ({{ $item->email }} - {{ ($item->role?->name === 'Client' ? 'Klien' : $item->role?->name) ?? '-' }})"
                                                      data-confirm-message="Akun pengguna ini akan dihapus secara permanen dan tidak dapat lagi masuk ke sistem.">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-2.5 py-1 bg-red-600 text-white rounded text-xs font-medium hover:bg-red-700 transition">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        Tidak ada data pengguna yang sesuai dengan kriteria penyaringan.
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
