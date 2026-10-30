<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SIMOP') }} – Sistem Informasi Manajemen Operasional</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { font-family: 'Inter', sans-serif; }

        .auth-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 40%, #0f2044 70%, #0c1a3a 100%);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        /* Orbs dekoratif */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.18;
            animation: float 12s ease-in-out infinite;
        }
        .orb-1 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, #3b82f6, #1d4ed8);
            top: -120px; left: -120px;
            animation-delay: 0s;
        }
        .orb-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, #6366f1, #4338ca);
            bottom: -80px; right: -80px;
            animation-delay: -4s;
        }
        .orb-3 {
            width: 250px; height: 250px;
            background: radial-gradient(circle, #0ea5e9, #0284c7);
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -8s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) scale(1); }
            33% { transform: translateY(-20px) scale(1.03); }
            66% { transform: translateY(10px) scale(0.97); }
        }

        /* Grid dot pattern */
        .dot-pattern {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(148,163,184,0.07) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        /* Glassmorphism card */
        .auth-card {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(148, 163, 184, 0.12);
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255,255,255,0.04);
        }

        /* Form inputs */
        .auth-input {
            width: 100%;
            background: rgba(30, 41, 59, 0.8) !important;
            border: 1px solid rgba(71, 85, 105, 0.6) !important;
            border-radius: 10px !important;
            color: #f1f5f9 !important;
            padding: 0.625rem 0.875rem !important;
            font-size: 0.875rem !important;
            transition: all 0.2s ease;
        }
        .auth-input:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
            outline: none !important;
            background: rgba(30, 41, 59, 0.95) !important;
        }
        .auth-input::placeholder { color: #64748b !important; }

        /* Label */
        .auth-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #94a3b8;
            margin-bottom: 0.375rem;
        }

        /* Primary button */
        .auth-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1.5rem;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }
        .auth-btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
            transform: translateY(-1px);
        }
        .auth-btn-primary:active { transform: translateY(0); }

        /* Secondary / ghost button */
        .auth-btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s;
        }
        .auth-btn-ghost:hover { color: #e2e8f0; }

        /* Error message */
        .auth-error {
            margin-top: 0.375rem;
            font-size: 0.78125rem;
            color: #f87171;
        }

        /* Divider */
        .auth-divider {
            border-color: rgba(71, 85, 105, 0.4);
        }

        /* Checkbox */
        input[type="checkbox"].auth-check {
            accent-color: #3b82f6;
            width: 1rem;
            height: 1rem;
            border-radius: 4px;
        }

        /* Logo brand */
        .brand-logo {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 1.125rem; color: white;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
            flex-shrink: 0;
        }
    </style>
</head>
<body class="antialiased">
    <div class="auth-bg flex min-h-screen items-center justify-center px-4 py-10">
        <!-- Background orbs -->
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="dot-pattern"></div>

        <div class="relative z-10 w-full max-w-md">
            <!-- Branding header -->
            <div class="mb-8 flex flex-col items-center text-center">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="brand-logo group-hover:scale-105 transition-transform duration-200">S</div>
                    <div class="text-left">
                        <h1 class="text-xl font-bold text-white tracking-wide">SIMOP</h1>
                        <p class="text-xs text-slate-400 mt-0.5">Manajemen Operasional</p>
                    </div>
                </a>
            </div>

            <!-- Card -->
            <div class="auth-card px-8 py-8">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <p class="mt-8 text-center text-xs text-slate-600">
                &copy; {{ date('Y') }} SIMOP · Sistem Informasi Manajemen Operasional
            </p>
        </div>
    </div>
</body>
</html>
