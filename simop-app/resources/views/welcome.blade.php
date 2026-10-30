<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SIMOP - Sistem Informasi Manajemen Operasional. Platform terpadu untuk mengelola proyek, survey, produksi, invoice, dan dokumen operasional.">
    <title>SIMOP – Sistem Informasi Manajemen Operasional</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }

        body { margin: 0; padding: 0; }

        .hero-bg {
            background: linear-gradient(145deg, #060d1f 0%, #0d1b35 25%, #101e3f 55%, #090f1f 100%);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .bg-mesh {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 70% 50% at 15% 10%, rgba(37,99,235,0.16) 0%, transparent 60%),
                radial-gradient(ellipse 50% 60% at 85% 80%, rgba(99,102,241,0.13) 0%, transparent 60%),
                radial-gradient(ellipse 40% 40% at 50% 40%, rgba(14,165,233,0.07) 0%, transparent 55%);
        }

        .bg-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(148,163,184,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(148,163,184,0.04) 1px, transparent 1px);
            background-size: 56px 56px;
        }

        .glow-ring {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(59,130,246,0.08);
            animation: ring-pulse 8s ease-in-out infinite;
        }

        .glow-ring-1 {
            width: 700px; height: 700px;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
        }

        .glow-ring-2 {
            width: 1000px; height: 1000px;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -3s;
        }

        @keyframes ring-pulse {
            0%, 100% { opacity: 0.5; transform: translate(-50%, -50%) scale(1); }
            50% { opacity: 1; transform: translate(-50%, -50%) scale(1.03); }
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(37,99,235,0.12);
            border: 1px solid rgba(37,99,235,0.25);
            border-radius: 999px;
            padding: 0.35rem 1rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: #60a5fa;
            letter-spacing: 0.02em;
        }

        .badge-dot {
            width: 6px; height: 6px;
            background: #3b82f6;
            border-radius: 50%;
            box-shadow: 0 0 8px #3b82f6;
            animation: blink 2s ease-in-out infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .gradient-text {
            background: linear-gradient(135deg, #ffffff 20%, #93c5fd 65%, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .feat-card {
            background: rgba(8, 18, 40, 0.65);
            border: 1px solid rgba(51,65,85,0.5);
            border-radius: 18px;
            padding: 1.5rem;
            backdrop-filter: blur(12px);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feat-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 18px;
            opacity: 0;
            background: linear-gradient(135deg, rgba(37,99,235,0.05), transparent);
            transition: opacity 0.3s ease;
        }

        .feat-card:hover {
            border-color: rgba(59,130,246,0.35);
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.4), 0 0 0 1px rgba(59,130,246,0.1);
        }

        .feat-card:hover::before { opacity: 1; }

        .feat-icon {
            width: 44px; height: 44px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 12px;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            font-weight: 600;
            font-size: 0.9375rem;
            padding: 0.8rem 2rem;
            border-radius: 14px;
            border: none;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 4px 22px rgba(37,99,235,0.4), 0 1px 0 rgba(255,255,255,0.1) inset;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            box-shadow: 0 6px 30px rgba(37,99,235,0.55);
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.12);
            color: #cbd5e1;
            font-weight: 600;
            font-size: 0.9375rem;
            padding: 0.8rem 2rem;
            border-radius: 14px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            backdrop-filter: blur(8px);
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.22);
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }

        .stat-card {
            background: rgba(8,18,40,0.7);
            border: 1px solid rgba(51,65,85,0.45);
            border-radius: 14px;
            padding: 1.25rem 1rem;
            text-align: center;
            backdrop-filter: blur(12px);
            transition: border-color 0.3s ease;
        }

        .stat-card:hover {
            border-color: rgba(59,130,246,0.3);
        }

        .brand-logo-lg {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.375rem; font-weight: 900; color: white;
            box-shadow: 0 4px 20px rgba(37,99,235,0.45);
            letter-spacing: -0.5px;
        }

        nav {
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(148,163,184,0.06);
        }
    </style>
</head>
<body>
    <div class="hero-bg">
        <div class="bg-mesh"></div>
        <div class="bg-grid"></div>
        <div class="glow-ring glow-ring-1"></div>
        <div class="glow-ring glow-ring-2"></div>

        <nav class="flex items-center justify-between px-6 py-4 sm:px-10 lg:px-16">
            <div class="flex items-center gap-3">
                <div class="brand-logo-lg">S</div>
                <div>
                    <span class="text-base font-bold text-white tracking-wide">SIMOP</span>
                    <p class="text-xs text-slate-500 leading-none mt-0.5">Manajemen Operasional</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="btn-secondary px-5 py-2.5 text-sm">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="btn-primary px-5 py-2.5 text-sm">
                    Daftar
                </a>
            </div>
        </nav>

        <div class="relative z-10 mx-auto max-w-6xl px-6 pb-20 pt-16 text-center sm:px-10 lg:px-16 lg:pt-24">
            <div class="mb-8">
                <span class="badge">
                    <span class="badge-dot"></span>
                    Platform Manajemen Operasional Terpadu
                </span>
            </div>

            <h1 class="mx-auto max-w-4xl text-4xl font-extrabold leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                <span class="gradient-text">Kelola Operasional</span><br>
                <span class="text-white">Bisnis Lebih Efisien</span>
            </h1>

            <p class="mx-auto mt-6 max-w-2xl text-base leading-relaxed text-slate-400 sm:text-lg">
                SIMOP membantu tim Anda mengelola seluruh alur kerja — mulai dari
                <span class="text-slate-200 font-medium">pemesanan</span>,
                <span class="text-slate-200 font-medium">survey</span>,
                <span class="text-slate-200 font-medium">produksi</span>, hingga
                <span class="text-slate-200 font-medium">invoice</span> dalam satu platform terintegrasi.
            </p>

            <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Masuk ke Dashboard
                </a>
                <a href="{{ route('register') }}" class="btn-secondary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Daftar Akun
                </a>
            </div>

            <div class="mt-16 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="stat-card">
                    <p class="text-2xl font-extrabold text-white">4+</p>
                    <p class="mt-1 text-xs text-slate-500">Tipe Pengguna</p>
                </div>
                <div class="stat-card">
                    <p class="text-2xl font-extrabold text-white">10+</p>
                    <p class="mt-1 text-xs text-slate-500">Modul Fitur</p>
                </div>
                <div class="stat-card">
                    <p class="text-2xl font-extrabold text-white">100%</p>
                    <p class="mt-1 text-xs text-slate-500">Berbasis Web</p>
                </div>
                <div class="stat-card">
                    <p class="text-2xl font-extrabold text-white">Real&#8209;time</p>
                    <p class="mt-1 text-xs text-slate-500">Tracking</p>
                </div>
            </div>
        </div>

        <div class="relative z-10 mx-auto max-w-6xl px-6 pb-24 sm:px-10 lg:px-16">
            <div class="mb-12 text-center">
                <p class="text-xs font-bold uppercase tracking-widest text-blue-400 mb-3">Fitur Unggulan</p>
                <h2 class="text-2xl font-bold text-white sm:text-3xl">
                    Semua yang Anda Butuhkan
                </h2>
                <p class="mt-2 text-sm text-slate-400 max-w-md mx-auto">Dirancang untuk memudahkan setiap tahap operasional bisnis Anda.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="feat-card">
                    <div class="feat-icon bg-blue-500/15">📋</div>
                    <h3 class="font-semibold text-white">Order &amp; RFQ</h3>
                    <p class="mt-1.5 text-sm text-slate-400 leading-relaxed">Kelola pemesanan dan permintaan penawaran dari klien dengan mudah.</p>
                </div>
                <div class="feat-card">
                    <div class="feat-icon bg-emerald-500/15">🗺️</div>
                    <h3 class="font-semibold text-white">Survey &amp; Work Estimate</h3>
                    <p class="mt-1.5 text-sm text-slate-400 leading-relaxed">Jadwalkan survey lapangan dan buat estimasi pekerjaan secara akurat.</p>
                </div>
                <div class="feat-card">
                    <div class="feat-icon bg-purple-500/15">✅</div>
                    <h3 class="font-semibold text-white">Approval &amp; Persetujuan</h3>
                    <p class="mt-1.5 text-sm text-slate-400 leading-relaxed">Alur persetujuan bertingkat dari direktur hingga eksekusi lapangan.</p>
                </div>
                <div class="feat-card">
                    <div class="feat-icon bg-amber-500/15">🏗️</div>
                    <h3 class="font-semibold text-white">Produksi &amp; Instalasi</h3>
                    <p class="mt-1.5 text-sm text-slate-400 leading-relaxed">Pantau progres produksi dan instalasi secara real-time di lapangan.</p>
                </div>
                <div class="feat-card">
                    <div class="feat-icon bg-sky-500/15">📄</div>
                    <h3 class="font-semibold text-white">Dokumen &amp; Invoice</h3>
                    <p class="mt-1.5 text-sm text-slate-400 leading-relaxed">Kelola seluruh dokumen proyek dan tagihan klien dalam satu tempat.</p>
                </div>
                <div class="feat-card">
                    <div class="feat-icon bg-rose-500/15">⭐</div>
                    <h3 class="font-semibold text-white">Rating &amp; Laporan</h3>
                    <p class="mt-1.5 text-sm text-slate-400 leading-relaxed">Ukur kepuasan klien dan hasilkan laporan operasional yang komprehensif.</p>
                </div>
            </div>
        </div>

        <div class="relative z-10 border-t border-slate-800/70 px-6 py-6 text-center">
            <p class="text-xs text-slate-700">
                &copy; {{ date('Y') }} SIMOP &middot; Sistem Informasi Manajemen Operasional &middot; All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
