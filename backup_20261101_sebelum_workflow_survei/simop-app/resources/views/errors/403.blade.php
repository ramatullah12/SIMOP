<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Tidak Diizinkan - SIMOP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center px-6">
    <div class="w-full max-w-lg text-center">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-blue-100">
            <svg
                class="h-10 w-10 text-blue-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.8"
                    d="M12 9v2m0 4h.01M10.29 3.86l-8.1 14a2 2 0 001.73 3h16.16a2 2 0 001.73-3l-8.1-14a2 2 0 00-3.42 0z"
                />
            </svg>
        </div>

        <p class="mt-6 text-sm font-semibold uppercase tracking-widest text-blue-600">
            Error 403
        </p>

        <h1 class="mt-2 text-3xl font-bold text-slate-900">
            Akses Tidak Diizinkan
        </h1>

        <p class="mx-auto mt-4 max-w-md text-sm leading-6 text-slate-500">
            Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.
            Silakan kembali ke dashboard atau hubungi administrator jika Anda
            merasa seharusnya memiliki akses.
        </p>

        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
            <a
                href="{{ route('dashboard') }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
            >
                Kembali ke Dashboard
            </a>

            <button
                type="button"
                onclick="history.back()"
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            >
                Kembali
            </button>
        </div>

        <p class="mt-10 text-xs text-slate-400">
            SIMOP · Sistem Informasi Manajemen Operasional
        </p>
    </div>
</body>
</html>