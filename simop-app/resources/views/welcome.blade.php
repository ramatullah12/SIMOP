<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SIMOP - Sistem Informasi Manajemen Operasional. Platform terpadu untuk mengelola proyek, survey, produksi, invoice, dan dokumen operasional.">
    <title>SIMOP – Sistem Informasi Manajemen Operasional</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }

        .hero-bg {
            background: linear-gradient(145deg, #0f172a 0%, #1e293b 35%, #0f2044 65%, #0c1a3a 100%);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        /* Glow orbs */
        .orb { position: absolute; border-radius: 50%; filter: blur(90px); pointer-events: none; }
        .orb-1 { width: 600px; height: 600px; background: radial-gradient(circle, rgba(37,99,235,0.3), transparent);
            top: -150px; left: -150px; animation: pulse-orb 8s ease-in-out infinite; }
        .orb-2 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(99,102,241,0.2), transparent);
            bottom: -100px; right: -80px; animation: pulse-orb 10s ease-in-out infinite reverse; }
        .orb-3 { width: 300px; height: 300px; background: radial-gradient(circle, rgba(14,165,233,0.15), transparent);
            top: 40%; left: 60%; animation: pulse-orb 12s ease-in-out infinite; }

        @keyframes pulse-orb {
            0%, 100% { transform: scale(1) translate(0, 0); }
            50% { transform: scale(1.1) translate(-10px, -15px); }
        }

        /* Dot pattern */
        .dot-pattern {
            position: absolute; inset: 0;
            background-image: radial-gradient(circle, rgba(148,163,184,0.06) 1px, transparent 1px);
            background-size: 36px 36px;
        }

        /* Badge */
        .badge {
            display: inline-flex; align-items: center; gap: 0.5rem;
            background: rgba(37,99,235,0.15); border: 1px solid rgba(37,99,235,0.3);
            border-radius: 999px; padding: 0.375rem 1rem;
            font-size: 0.8125rem; font-weight: 500; color: #93c5fd;
        }
        .badge-dot { width: 6px; height: 6px; background: #3b82f6; border-radius: 50%;
            box-shadow: 0 0 8px #3b82f6; animation: blink 2s ease-in-out infinite; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #ffffff 30%, #93c5fd 70%, #6366f1);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Feature cards */
        .feat-card {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(51, 65, 85, 0.6);
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            backdrop-filter: blur(12px);
            transition: all 0.25s ease;
        }
        .feat-card:hover {
            border-color: rgba(59, 130, 246, 0.4);
            background: rgba(15, 23, 42, 0.9);
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        .feat-icon {
            width: 40px; height: 40px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 10px;
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
        }

        /* Buttons */
        .btn-primary {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white; font-weight: 600; font-size: 0.9375rem;
            padding: 0.75rem 2rem; border-radius: 12px; border: none;
            text-decoration: none; cursor: pointer;
            box-shadow: 0 4px 20px rgba(37,99,235,0.35);
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            box-shadow: 0 6px 28px rgba(37,99,235,0.5);
            transform: translateY(-2px);
            color: white; text-decoration: none;
        }

        .btn-secondary {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
            background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15);
            color: #e2e8f0; font-weight: 600; font-size: 0.9375rem;
            padding: 0.75rem 2rem; border-radius: 12px;
            text-decoration: none; cursor: pointer;
            transition: all 0.2s ease;
            backdrop-filter: blur(8px);
        }
        .btn-secondary:hover {
            background: rgba(255,255,255,0.12); border-color: rgba(255,255,255,0.25);
            color: white; text-decoration: none; transform: translateY(-2px);
        }

        /* Stat cards */
        .stat-card {
            background: rgba(15,23,42,0.8);
            border: 1px solid rgba(51,65,85,0.5);
            border-radius: 12px; padding: 1rem 1.25rem;
            text-align: center;
        }

        /* Logo brand */
        .brand-logo-lg {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; font-weight: 900; color: white;
            box-shadow: 0 4px 20px rgba(37,99,235,0.4);
        }
    </style>
</head>
<body>
    <div class="hero-bg">
        <!-- Decorative background -->
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="dot-pattern"></div>

        <!-- Navbar -->
        <nav class="relative z-10 flex items-center justify-between px-6 py-5 sm:px-10 lg:px-16">
            <div class="flex items-center gap-3">
                <div class="brand-logo-lg">S</div>
                <div>
                    <span class="text-lg font-bold text-white">SIMOP</span>
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

        <!-- Hero Section -->
        <div class="relative z-10 mx-auto max-w-6xl px-6 pb-20 pt-12 text-center sm:px-10 lg:px-16 lg:pt-20">
            <!-- Badge -->
            <div class="mb-8">
                <span class="badge">
                    <span class="badge-dot"></span>
                    Platform Manajemen Operasional Terpadu
                </span>
            </div>

            <!-- Headline -->
            <h1 class="mx-auto max-w-4xl text-4xl font-extrabold leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                <span class="gradient-text">Kelola Operasional</span><br>
                <span class="text-white">Bisnis dengan Lebih Efisien</span>
            </h1>

            <p class="mx-auto mt-6 max-w-2xl text-base leading-relaxed text-slate-400 sm:text-lg">
                SIMOP membantu tim Anda mengelola seluruh alur kerja operasional — mulai dari
                <span class="text-slate-200 font-medium">pemesanan</span>,
                <span class="text-slate-200 font-medium">survey</span>,
                <span class="text-slate-200 font-medium">produksi</span>, hingga
                <span class="text-slate-200 font-medium">invoice</span> — dalam satu platform terintegrasi.
            </p>

            <!-- CTA Buttons -->
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

            <!-- Stats -->
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
                    <p class="text-2xl font-extrabold text-white">Real‑time</p>
                    <p class="mt-1 text-xs text-slate-500">Tracking</p>
                </div>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="relative z-10 mx-auto max-w-6xl px-6 pb-24 sm:px-10 lg:px-16">
            <div class="mb-10 text-center">
                <p class="text-xs font-semibold uppercase tracking-widest text-blue-400">Fitur Unggulan</p>
                <h2 class="mt-2 text-2xl font-bold text-white sm:text-3xl">
                    Semua yang Anda Butuhkan
                </h2>
                <p class="mt-2 text-sm text-slate-400">Dirancang untuk memudahkan setiap tahap operasional bisnis Anda.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="feat-card">
                    <div class="feat-icon bg-blue-500/15">📋</div>
                    <h3 class="font-semibold text-white">Order & RFQ</h3>
                    <p class="mt-1 text-sm text-slate-400">Kelola pemesanan dan permintaan penawaran dari klien dengan mudah.</p>
                </div>
                <div class="feat-card">
                    <div class="feat-icon bg-emerald-500/15">🗺️</div>
                    <h3 class="font-semibold text-white">Survey & Work Estimate</h3>
                    <p class="mt-1 text-sm text-slate-400">Jadwalkan survey lapangan dan buat estimasi pekerjaan secara akurat.</p>
                </div>
                <div class="feat-card">
                    <div class="feat-icon bg-purple-500/15">✅</div>
                    <h3 class="font-semibold text-white">Approval & Persetujuan</h3>
                    <p class="mt-1 text-sm text-slate-400">Alur persetujuan bertingkat dari direktur hingga eksekusi lapangan.</p>
                </div>
                <div class="feat-card">
                    <div class="feat-icon bg-amber-500/15">🏗️</div>
                    <h3 class="font-semibold text-white">Produksi & Instalasi</h3>
                    <p class="mt-1 text-sm text-slate-400">Pantau progres produksi dan instalasi secara real-time di lapangan.</p>
                </div>
                <div class="feat-card">
                    <div class="feat-icon bg-sky-500/15">📄</div>
                    <h3 class="font-semibold text-white">Dokumen & Invoice</h3>
                    <p class="mt-1 text-sm text-slate-400">Kelola seluruh dokumen proyek dan tagihan klien dalam satu tempat.</p>
                </div>
                <div class="feat-card">
                    <div class="feat-icon bg-rose-500/15">⭐</div>
                    <h3 class="font-semibold text-white">Rating & Laporan</h3>
                    <p class="mt-1 text-sm text-slate-400">Ukur kepuasan klien dan hasilkan laporan operasional yang komprehensif.</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="relative z-10 border-t border-slate-800 px-6 py-6 text-center">
            <p class="text-xs text-slate-600">
                &copy; {{ date('Y') }} SIMOP &middot; Sistem Informasi Manajemen Operasional &middot; All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
