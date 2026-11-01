<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SIMOP') }} – Sistem Informasi Manajemen Operasional</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }

        body { margin: 0; padding: 0; }

        .auth-bg {
            background: linear-gradient(135deg, #060d1f 0%, #0d1b35 30%, #121f3d 60%, #0b1628 100%);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .auth-mesh {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 10%, rgba(37,99,235,0.14) 0%, transparent 55%),
                radial-gradient(ellipse 60% 50% at 80% 80%, rgba(99,102,241,0.12) 0%, transparent 55%),
                radial-gradient(ellipse 50% 40% at 50% 50%, rgba(14,165,233,0.07) 0%, transparent 60%);
        }

        .auth-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(148,163,184,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(148,163,184,0.04) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        .auth-particles {
            position: absolute;
            inset: 0;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            border-radius: 50%;
            background: rgba(148,163,184,0.3);
            animation: drift linear infinite;
        }

        @keyframes drift {
            0% { transform: translateY(100vh) translateX(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100px) translateX(30px); opacity: 0; }
        }

        .auth-card {
            background: rgba(10, 18, 40, 0.78);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(148, 163, 184, 0.1);
            border-radius: 24px;
            box-shadow:
                0 32px 80px rgba(0,0,0,0.6),
                0 0 0 1px rgba(255,255,255,0.03),
                inset 0 1px 0 rgba(255,255,255,0.06);
        }

        .auth-input {
            width: 100%;
            background: rgba(15, 28, 55, 0.7) !important;
            border: 1px solid rgba(71, 85, 105, 0.5) !important;
            border-radius: 12px !important;
            color: #f1f5f9 !important;
            padding: 0.7rem 1rem !important;
            font-size: 0.875rem !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
            outline: none;
        }

        .auth-input:focus {
            border-color: rgba(59,130,246,0.8) !important;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.12), 0 0 20px rgba(59,130,246,0.08) !important;
            background: rgba(15, 28, 55, 0.95) !important;
        }

        .auth-input::placeholder { color: #475569 !important; }

        .auth-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #94a3b8;
            margin-bottom: 0.4rem;
            letter-spacing: 0.01em;
        }

        .auth-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.7rem 1.5rem;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 18px rgba(37,99,235,0.35), 0 1px 0 rgba(255,255,255,0.1) inset;
            letter-spacing: 0.01em;
            text-decoration: none;
        }

        .auth-btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            box-shadow: 0 6px 24px rgba(37,99,235,0.5), 0 1px 0 rgba(255,255,255,0.1) inset;
            transform: translateY(-1px);
            color: white;
        }

        .auth-btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 2px 10px rgba(37,99,235,0.3);
        }

        .auth-btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #64748b;
            text-decoration: none;
            transition: color 0.2s;
        }

        .auth-btn-ghost:hover { color: #cbd5e1; }

        .auth-error {
            margin-top: 0.375rem;
            font-size: 0.78125rem;
            color: #f87171;
        }

        .auth-divider {
            border-color: rgba(71,85,105,0.3);
        }

        input[type="checkbox"].auth-check {
            accent-color: #3b82f6;
            width: 1rem;
            height: 1rem;
        }

        .brand-logo {
            width: 46px;
            height: 46px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.125rem;
            color: white;
            box-shadow: 0 4px 18px rgba(37,99,235,0.45);
            flex-shrink: 0;
            letter-spacing: -0.5px;
        }

        .brand-tagline {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(37,99,235,0.1);
            border: 1px solid rgba(37,99,235,0.2);
            border-radius: 999px;
            padding: 0.2rem 0.75rem;
            font-size: 0.7rem;
            font-weight: 600;
            color: #60a5fa;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-top: 0.5rem;
        }

        .brand-dot {
            width: 5px;
            height: 5px;
            background: #3b82f6;
            border-radius: 50%;
            box-shadow: 0 0 6px #3b82f6;
            animation: pulse-dot 2s ease-in-out infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }

        .step-number {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            flex-shrink: 0;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            font-size: 0.65rem;
            font-weight: 700;
            color: white;
        }
    </style>
</head>
<body class="antialiased">
    <div class="auth-bg flex min-h-screen items-center justify-center px-4 py-12">
        <div class="auth-mesh"></div>
        <div class="auth-grid"></div>

        <div class="auth-particles">
            <div class="particle" style="left:15%;animation-duration:18s;animation-delay:0s;"></div>
            <div class="particle" style="left:30%;animation-duration:22s;animation-delay:-5s;"></div>
            <div class="particle" style="left:55%;animation-duration:16s;animation-delay:-10s;"></div>
            <div class="particle" style="left:70%;animation-duration:20s;animation-delay:-3s;"></div>
            <div class="particle" style="left:85%;animation-duration:24s;animation-delay:-8s;"></div>
        </div>

        <div class="relative z-10 w-full max-w-md">
            <div class="mb-8 flex flex-col items-center text-center">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="brand-logo group-hover:scale-105 transition-transform duration-200">S</div>
                    <div class="text-left">
                        <h1 class="text-xl font-bold text-white tracking-wide">SIMOP</h1>
                        <p class="text-xs text-slate-500 mt-0.5">Sistem Informasi Manajemen</p>
                    </div>
                </a>
                <div class="brand-tagline mt-3">
                    <span class="brand-dot"></span>
                    Platform Operasional Terpadu
                </div>
            </div>

            <div class="auth-card px-8 py-8">
                {{ $slot }}
            </div>

            <p class="mt-6 text-center text-xs text-slate-700">
                &copy; {{ date('Y') }} SIMOP &middot; Semua hak dilindungi
            </p>
        </div>
    </div>
</body>
</html>
