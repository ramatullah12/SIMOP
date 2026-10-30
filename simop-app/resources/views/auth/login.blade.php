<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-bold text-white">Masuk ke Akun</h2>
        <p class="mt-1 text-sm text-slate-400">Selamat datang kembali di SIMOP</p>
    </div>

    @if (session('status'))
        <div class="mb-5 rounded-xl bg-blue-500/10 border border-blue-500/20 px-4 py-3 text-sm text-blue-300">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="auth-label">Alamat Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="contoh@email.com"
                class="auth-input"
            >
            @error('email')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="auth-label" style="margin-bottom:0">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-blue-400 hover:text-blue-300 transition-colors">
                        Lupa password?
                    </a>
                @endif
            </div>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                class="auth-input"
            >
            @error('password')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-2">
            <input id="remember_me" type="checkbox" name="remember" class="auth-check">
            <label for="remember_me" class="text-sm text-slate-400 cursor-pointer select-none">
                Ingat saya
            </label>
        </div>

        <button type="submit" class="auth-btn-primary w-full mt-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Masuk
        </button>
    </form>

    <div class="my-6 border-t auth-divider"></div>

    <p class="text-center text-sm text-slate-500">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-medium text-blue-400 hover:text-blue-300 transition-colors ml-1">
            Daftar Sekarang
        </a>
    </p>
</x-guest-layout>
