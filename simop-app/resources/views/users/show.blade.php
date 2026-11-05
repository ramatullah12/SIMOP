<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Rincian Pengguna: {{ $user->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    Informasi akun pengguna dan status verifikasi sistem.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.edit', $user) }}"
                   class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 shadow-sm transition">
                    Ubah Pengguna
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200 transition">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto space-y-6">
            @if (session('success'))
                <div class="rounded-lg bg-green-50 border border-green-300 text-green-800 px-4 py-3 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg bg-red-50 border border-red-300 text-red-800 px-4 py-3 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Panel Status Verifikasi --}}
            <div class="rounded-xl border p-5 shadow-sm {{ $user->isVerified() ? 'bg-emerald-50/50 border-emerald-200' : 'bg-amber-50/70 border-amber-300' }}">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="p-2.5 rounded-xl shrink-0 {{ $user->isVerified() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            @if ($user->isVerified())
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-base {{ $user->isVerified() ? 'text-emerald-900' : 'text-amber-900' }}">
                                    {{ $user->isVerified() ? 'Akun Telah Terverifikasi' : 'Menunggu Verifikasi Administrator' }}
                                </h3>
                            </div>
                            <p class="text-xs mt-1 {{ $user->isVerified() ? 'text-emerald-700' : 'text-amber-700' }}">
                                @if ($user->isVerified())
                                    Akun ini aktif dan dapat masuk ke sistem. Waktu verifikasi: {{ $user->email_verified_at?->format('d/m/Y H:i') ?? '-' }}.
                                @else
                                    Akun ini didaftarkan oleh pengguna namun belum diverifikasi oleh administrator, sehingga belum dapat masuk ke aplikasi.
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        @if (! $user->isVerified())
                            <form method="POST" action="{{ route('admin.users.verify', $user) }}"
                                  data-confirm-title="Konfirmasi Verifikasi Pengguna"
                                  data-confirm-detail="{{ $user->name }} ({{ $user->email }})"
                                  data-confirm-message="Apakah Anda yakin ingin memverifikasi dan mengaktifkan akun pengguna ini? Pengguna akan dapat masuk ke sistem."
                                  data-confirm-btn="Ya, Verifikasi Akun"
                                  data-confirm-type="success">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-sm font-semibold shadow-sm transition inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Verifikasi & Aktifkan
                                </button>
                            </form>
                        @else
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.unverify', $user) }}"
                                      data-confirm-title="Konfirmasi Pembatalan Verifikasi"
                                      data-confirm-detail="{{ $user->name }} ({{ $user->email }})"
                                      data-confirm-message="Apakah Anda yakin ingin membatalkan verifikasi akun pengguna ini? Pengguna tidak akan dapat masuk ke sistem sampai diverifikasi kembali."
                                      data-confirm-btn="Ya, Batalkan Verifikasi"
                                      data-confirm-type="warning">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-1.5 bg-amber-200 hover:bg-amber-300 text-amber-900 rounded-md text-xs font-semibold transition">
                                        Batalkan Verifikasi
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm space-y-4">
                <h4 class="text-sm font-bold uppercase tracking-wider text-gray-500 border-b pb-2">Informasi Akun</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Nama Lengkap</p>
                        <p class="font-semibold text-gray-900 mt-0.5 text-base">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Alamat Email</p>
                        <p class="font-semibold text-gray-900 mt-0.5 text-base">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Peran Pengguna</p>
                        <div class="mt-1">
                            @if ($user->role)
                                <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-800 text-xs font-medium">
                                    {{ $user->role->name === 'Client' ? 'Klien' : $user->role->name }}
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded bg-rose-50 text-rose-700 text-xs font-medium border border-rose-200">
                                    Belum Ditentukan
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Tanggal Terdaftar</p>
                        <p class="font-semibold text-gray-900 mt-0.5">{{ $user->created_at?->format('d/m/Y H:i:s') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Status Verifikasi</p>
                        <p class="font-semibold {{ $user->isVerified() ? 'text-emerald-700' : 'text-amber-700' }} mt-0.5">
                            {{ $user->isVerified() ? 'Terverifikasi' : 'Menunggu Verifikasi Administrator' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Waktu Verifikasi</p>
                        <p class="font-semibold text-gray-900 mt-0.5">{{ $user->email_verified_at?->format('d/m/Y H:i:s') ?? 'Belum diverifikasi' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
