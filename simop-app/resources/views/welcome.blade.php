
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMOP - Sistem Informasi Manajemen Operasional</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100">
    <div class="flex min-h-screen items-center justify-center px-6">
        <div class="w-full max-w-lg rounded-2xl bg-white p-10 text-center shadow-lg">
            <h1 class="text-4xl font-bold text-blue-700">SIMOP</h1>
            <p class="mt-3 text-gray-600">
                Sistem Informasi Manajemen Operasional
            </p>
            <p class="mt-6 text-sm text-gray-500">
                Silakan login untuk mengakses dashboard sesuai hak akses Anda.
            </p>

            <div class="mt-8 flex justify-center gap-4">
                <a href="{{ route('login') }}"
                   class="rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-700">
                    Login
                </a>
                <a href="{{ route('register') }}"
                   class="rounded-lg border border-blue-600 px-6 py-3 font-semibold text-blue-600 hover:bg-blue-50">
                    Register
                </a>
            </div>
        </div>
    </div>
</body>
</html>
