<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-bold text-white">Buat Akun Baru</h2>
        <p class="mt-1 text-sm text-slate-400">Daftarkan diri Anda untuk mengakses SIMOP</p>
    </div>

    <div class="mb-5 flex gap-3 rounded-xl bg-amber-500/10 border border-amber-500/20 px-4 py-3">
        <svg class="w-4 h-4 text-amber-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-xs text-amber-300 leading-relaxed">
            Setelah mendaftar, akun Anda perlu diverifikasi oleh administrator sebelum dapat digunakan.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="auth-label">Nama Lengkap</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="Nama lengkap Anda"
                class="auth-input"
            >
            @error('name')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="auth-label">Alamat Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                placeholder="contoh@email.com"
                class="auth-input"
            >
            @error('email')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="auth-label">Kata Sandi</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Minimal 8 karakter"
                class="auth-input"
            >
            @error('password')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="auth-label">Konfirmasi Kata Sandi</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Ulangi kata sandi Anda"
                class="auth-input"
            >
            @error('password_confirmation')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="auth-btn-primary w-full mt-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Daftar Sekarang
        </button>
    </form>

    <div class="my-6 border-t auth-divider"></div>

    <p class="text-center text-sm text-slate-500">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-medium text-blue-400 hover:text-blue-300 transition-colors ml-1">
            Masuk di sini
        </a>
    </p>
</x-guest-layout>
