<x-guest-layout>
    <div class="text-center">
        <!-- Icon success -->
        <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-green-500/15 border border-green-500/20">
            <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <h2 class="text-xl font-bold text-white">Pendaftaran Berhasil!</h2>

        @if(session('registered_name'))
            <p class="mt-2 text-sm text-slate-300">
                Halo, <span class="font-semibold text-blue-400">{{ session('registered_name') }}</span>!
            </p>
        @endif

        <p class="mt-3 text-sm text-slate-400 leading-relaxed">
            Akun Anda telah berhasil didaftarkan. Namun, Anda perlu menunggu verifikasi dari
            <span class="text-white font-medium">administrator</span> sebelum dapat menggunakan aplikasi SIMOP.
        </p>
    </div>

    <!-- Separator -->
    <div class="my-6 border-t auth-divider"></div>

    <!-- Langkah selanjutnya -->
    <div class="space-y-3">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Langkah Selanjutnya</p>

        <div class="flex items-start gap-3 rounded-lg bg-slate-800/50 border border-slate-700/50 px-4 py-3">
            <div class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-600 text-[10px] font-bold text-white">1</div>
            <p class="text-sm text-slate-300">
                Administrator akan meninjau dan memverifikasi akun Anda.
            </p>
        </div>

        <div class="flex items-start gap-3 rounded-lg bg-slate-800/50 border border-slate-700/50 px-4 py-3">
            <div class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-600 text-[10px] font-bold text-white">2</div>
            <p class="text-sm text-slate-300">
                Setelah diverifikasi, Anda dapat login menggunakan email dan password yang telah didaftarkan.
            </p>
        </div>

        <div class="flex items-start gap-3 rounded-lg bg-slate-800/50 border border-slate-700/50 px-4 py-3">
            <div class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-600 text-[10px] font-bold text-white">3</div>
            <p class="text-sm text-slate-300">
                Hubungi administrator jika akun Anda belum aktif dalam 1×24 jam.
            </p>
        </div>
    </div>

    <div class="my-6 border-t auth-divider"></div>

    <!-- Info kontak / tombol kembali -->
    <div class="text-center">
        <a href="{{ route('login') }}" class="auth-btn-primary inline-flex">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Ke Halaman Login
        </a>
    </div>
</x-guest-layout>
