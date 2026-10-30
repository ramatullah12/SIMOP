<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIMOP') – Sistem Informasi Manajemen Operasional</title>


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>

        *, *::before, *::after { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }
        body { font-family: 'Inter', sans-serif; background: #f4f6fb; color: #1e293b; }

        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }

        .app-shell { display: flex; min-height: 100vh; }

        .sidebar {
            width: 260px; min-height: 100vh; flex-shrink: 0;
            background: linear-gradient(180deg, #0c1526 0%, #111e38 50%, #0f1c34 100%);
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; bottom: 0;
            z-index: 50; transition: transform 0.3s ease;
            box-shadow: 4px 0 28px rgba(0,0,0,0.2);
        }
        .sidebar-brand {
            display: flex; align-items: center; gap: 12px;
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            text-decoration: none;
        }
        .sidebar-logo {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.125rem; font-weight: 800; color: white;
            box-shadow: 0 4px 14px rgba(59,130,246,0.45); flex-shrink: 0;
        }
        .sidebar-brand-text { flex: 1; min-width: 0; }
        .sidebar-brand-text h1 { color: white; font-size: 1.0625rem; font-weight: 700; letter-spacing: 0.02em; margin: 0; }
        .sidebar-brand-text p { color: #4a5f80; font-size: 0.6875rem; margin: 2px 0 0; }

        .sidebar-role { margin: 0; padding: 10px 20px; border-bottom: 1px solid rgba(255,255,255,0.04); }
        .sidebar-role-label { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.1em; color: #3d5270; text-transform: uppercase; }
        .sidebar-role-name { font-size: 0.8125rem; font-weight: 600; color: #7aa0cc; margin-top: 3px; }

        .sidebar-nav { flex: 1; padding: 12px 10px; overflow-y: auto; }
        .sidebar-nav-label { font-size: 0.625rem; font-weight: 700; letter-spacing: 0.1em; color: #2e4060; text-transform: uppercase; padding: 4px 10px 8px; }

        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 8px;
            font-size: 0.8125rem; font-weight: 500;
            color: #7090b8; text-decoration: none;
            transition: all 0.18s ease; margin-bottom: 2px; white-space: nowrap;
        }
        .nav-item:hover { background: rgba(255,255,255,0.06); color: #c8daf4; }
        .nav-item.active {
            background: linear-gradient(135deg, rgba(59,130,246,0.22), rgba(37,99,235,0.12));
            color: #7ec8ff; border: 1px solid rgba(59,130,246,0.22); font-weight: 600;
        }
        .nav-item.disabled { color: #1f3050; cursor: not-allowed; }
        .nav-icon { width: 18px; height: 18px; flex-shrink: 0; opacity: 0.8; }
        .nav-badge { margin-left: auto; font-size: 0.6rem; background: rgba(30,50,80,0.8); color: #3d5880; padding: 2px 7px; border-radius: 99px; font-weight: 600; }

        .sidebar-footer { padding: 10px; border-top: 1px solid rgba(255,255,255,0.05); }
        .sidebar-footer-btn {
            display: flex; align-items: center; gap: 10px;
            width: 100%; padding: 9px 12px; border-radius: 8px;
            background: none; border: none; cursor: pointer;
            font-size: 0.8125rem; font-weight: 500; color: #e05252;
            transition: all 0.15s ease; text-align: left;
        }
        .sidebar-footer-btn:hover { background: rgba(239,68,68,0.1); color: #fb8080; }

        .main-area { flex: 1; display: flex; flex-direction: column; min-width: 0; margin-left: 260px; transition: margin-left 0.3s ease; }

        .app-header {
            position: sticky; top: 0; z-index: 40;
            background: rgba(248,250,254,0.92);
            backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226,232,240,0.8);
            padding: 0 28px;
            height: 60px;
            display: flex; align-items: center; justify-content: space-between; gap: 16px;
            box-shadow: 0 1px 0 rgba(0,0,0,0.04), 0 4px 24px rgba(15,23,42,0.06);
        }
        .header-left { min-width: 0; display: flex; align-items: center; gap: 14px; }
        .header-divider { width: 1px; height: 20px; background: #e2e8f0; flex-shrink: 0; }
        .header-breadcrumb { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #2563eb; }
        .header-title { font-size: 1.0625rem; font-weight: 700; color: #0f172a; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .header-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
        .header-user-chip {
            display: flex; align-items: center; gap: 9px;
            background: white; border: 1px solid #e8edf4;
            border-radius: 12px; padding: 6px 12px 6px 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            cursor: default;
        }
        .header-avatar {
            width: 30px; height: 30px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; font-weight: 700; color: white; flex-shrink: 0;
        }
        .header-user-name { font-size: 0.8rem; font-weight: 600; color: #0f172a; white-space: nowrap; }
        .header-user-role { font-size: 0.65rem; color: #94a3b8; white-space: nowrap; }

        .mobile-toggle {
            display: none; background: none; border: none;
            cursor: pointer; padding: 6px; border-radius: 8px;
            color: #64748b; transition: background 0.15s;
        }
        .mobile-toggle:hover { background: #eef2f8; }

        .page-content { flex: 1; padding: 28px; overflow-x: hidden; }

        .flash-success {
            display: flex; align-items: center; gap: 10px;
            background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
            border: 1px solid #86efac;
            border-radius: 12px; padding: 12px 16px;
            font-size: 0.875rem; color: #15803d; margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(22,163,74,0.08);
        }
        .flash-error {
            display: flex; align-items: center; gap: 10px;
            background: linear-gradient(135deg, #fef2f2, #fff1f1);
            border: 1px solid #fca5a5;
            border-radius: 12px; padding: 12px 16px;
            font-size: 0.875rem; color: #dc2626; margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(220,38,38,0.08);
        }

        .app-footer {
            background: rgba(248,250,254,0.8); border-top: 1px solid #e8edf4;
            padding: 10px 28px; font-size: 0.72rem; color: #b0bec5; text-align: center;
            letter-spacing: 0.02em;
        }

        .sidebar-overlay {
            display: none; position: fixed; inset: 0; z-index: 49;
            background: rgba(0,0,0,0.45); backdrop-filter: blur(3px);
        }

        @media (max-width: 1023px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-area { margin-left: 0; }
            .mobile-toggle { display: flex; }
            .sidebar-overlay.open { display: block; }
            .header-user-chip { display: none; }
        }

        .card {
            background: white; border-radius: 16px;
            border: 1px solid #eaeff6;
            box-shadow: 0 1px 4px rgba(15,23,42,0.05), 0 8px 24px rgba(15,23,42,0.04);
            transition: box-shadow 0.2s ease;
        }
        .card:hover { box-shadow: 0 2px 8px rgba(15,23,42,0.07), 0 12px 32px rgba(15,23,42,0.06); }
        .card-header {
            padding: 20px 22px 16px; border-bottom: 1px solid #f1f5fb;
            font-weight: 700; font-size: 0.9375rem; color: #0f172a;
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-body { padding: 22px; }

        .badge { display: inline-flex; align-items: center; gap: 4px; font-size: 0.7rem; font-weight: 700; padding: 3px 10px; border-radius: 999px; letter-spacing: 0.02em; }
        .badge-blue { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-green { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .badge-amber { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .badge-red { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-purple { background: #faf5ff; color: #7c3aed; border: 1px solid #e9d5ff; }
        .badge-slate { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }
        .badge-orange { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
        .badge-emerald { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }

        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; font-size: 0.8125rem; font-weight: 600; border-radius: 10px; cursor: pointer; transition: all 0.18s ease; text-decoration: none; border: none; }
        .btn-sm { padding: 6px 14px; font-size: 0.75rem; border-radius: 8px; }
        .btn-md { padding: 9px 18px; }
        .btn-lg { padding: 11px 22px; font-size: 0.875rem; }
        .btn-primary { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; box-shadow: 0 2px 10px rgba(37,99,235,0.3); }
        .btn-primary:hover { background: linear-gradient(135deg, #1d4ed8, #1e40af); box-shadow: 0 4px 18px rgba(37,99,235,0.45); transform: translateY(-1px); color: white; }
        .btn-primary:active { transform: translateY(0); }
        .btn-secondary { background: white; color: #374151; border: 1px solid #d9e1ec; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
        .btn-secondary:hover { background: #f8fafc; border-color: #b8c5d6; color: #1f2937; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
        .btn-danger { background: #fff1f2; color: #dc2626; border: 1px solid #fecdd3; }
        .btn-danger:hover { background: #ffe4e6; border-color: #fca5a5; }
        .btn-success { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .btn-success:hover { background: #dcfce7; }

        .form-label { display: block; font-size: 0.8125rem; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-input {
            width: 100%; padding: 9px 12px;
            border: 1px solid #d1d9e6; border-radius: 10px;
            font-size: 0.875rem; color: #111827;
            background: #fafbfd; transition: all 0.15s;
            font-family: 'Inter', sans-serif;
        }
        .form-input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.12); background: white; }
        .form-input[readonly], .form-input[disabled] { background: #f1f5f9; color: #6b7280; cursor: not-allowed; }
        .form-error { font-size: 0.75rem; color: #dc2626; margin-top: 4px; }

        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th {
            padding: 11px 16px; text-align: left; font-size: 0.65rem;
            font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase;
            color: #64748b; background: #f8fafd; border-bottom: 1px solid #e8edf4;
            white-space: nowrap;
        }
        .data-table td {
            padding: 14px 16px; font-size: 0.875rem; color: #1e293b;
            border-bottom: 1px solid #f1f5fb; vertical-align: middle;
            transition: background 0.12s;
        }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tbody tr:hover td { background: #f6f9ff; }
        .data-table-empty { padding: 56px 24px; text-align: center; color: #94a3b8; font-size: 0.875rem; }

        .page-header { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 24px; }
        .page-header-title { font-size: 1.45rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.02em; }
        .page-header-sub { font-size: 0.8125rem; color: #94a3b8; margin: 5px 0 0; }

        .stat-card {
            background: white; border-radius: 16px; border: 1px solid #eaeff6;
            padding: 22px; box-shadow: 0 1px 4px rgba(15,23,42,0.05), 0 6px 20px rgba(15,23,42,0.04);
            position: relative; overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(15,23,42,0.08), 0 12px 32px rgba(15,23,42,0.06); }
        .stat-card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .stat-card-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
        .stat-card-trend { font-size: 0.7rem; font-weight: 700; padding: 3px 8px; border-radius: 999px; }
        .stat-card-value { font-size: 2rem; font-weight: 800; color: #0f172a; line-height: 1; letter-spacing: -0.03em; }
        .stat-card-label { font-size: 0.8rem; font-weight: 500; color: #64748b; margin-top: 6px; }
        .stat-card-accent { position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; border-radius: 50%; opacity: 0.06; }
    </style>
</head>

<body class="h-full">
    @php
        $user = auth()->user();
        $role = $user?->role?->slug ?? '';

        $navIcons = [
            'dashboard'                 => '<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
            'admin.orders.index'        => '<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
            'client.orders.index'       => '<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
            'admin.clients.index'       => '<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
            'admin.surveys.index'       => '<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>',
            'admin.work-estimates.index'=> '<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>',
            'approvals.index'           => '<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'projects.index'            => '<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>',
            'plannings.index'           => '<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
            'productions.index'         => '<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
            'bapps.index'               => '<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
            'documents.index'           => '<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>',
            'invoices.index'            => '<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
            'admin.ratings.index'       => '<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>',
            'client.ratings.index'      => '<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>',
            'admin.vendors.index'       => '<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>',
            'admin.reports.index'       => '<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
            'direktur.reports.index'    => '<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
            'admin.users.index'         => '<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
            'client.tracking.index'     => '<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        ];

        $menus = [
            'admin' => [
                ['Dashboard', 'dashboard'],
                ['Order & RFQ', 'admin.orders.index'],
                ['Client', 'admin.clients.index'],
                ['Survey', 'admin.surveys.index'],
                ['Work Estimate', 'admin.work-estimates.index'],
                ['Approval', 'approvals.index'],
                ['Project', 'projects.index'],
                ['Planning', 'plannings.index'],
                ['Produksi & Instalasi', 'productions.index'],
                ['BAPP', 'bapps.index'],
                ['Dokumen', 'documents.index'],
                ['Invoice', 'invoices.index'],
                ['Rating', 'admin.ratings.index'],
                ['Vendor', 'admin.vendors.index'],
                ['Laporan', 'admin.reports.index'],
                ['Pengguna', 'admin.users.index'],
            ],
            'direktur' => [
                ['Dashboard', 'dashboard'],
                ['Survey', 'admin.surveys.index'],
                ['Work Estimate', 'admin.work-estimates.index'],
                ['Approval', 'approvals.index'],
                ['Project', 'projects.index'],
                ['Laporan', 'direktur.reports.index'],
            ],
            'petugas-lapangan' => [
                ['Dashboard', 'dashboard'],
                ['Survey', 'admin.surveys.index'],
                ['Produksi & Instalasi', 'productions.index'],
                ['BAPP', 'bapps.index'],
                ['Project', 'projects.index'],
                ['Dokumen', 'documents.index'],
            ],
            'client' => [
                ['Dashboard', 'dashboard'],
                ['Order & RFQ', 'client.orders.index'],
                ['Tracking', 'client.tracking.index'],
                ['Dokumen', 'documents.index'],
                ['Invoice', 'invoices.index'],
                ['Rating', 'client.ratings.index'],
            ],
        ];

        $currentMenus = $menus[$role] ?? [];

        $roleLabels = [
            'admin'            => 'Administrator',
            'direktur'         => 'Direktur',
            'petugas-lapangan' => 'Petugas Lapangan',
            'client'           => 'Client',
        ];
        $roleLabel = $roleLabels[$role] ?? 'Pengguna';

        $patterns = [
            'dashboard'                  => ['dashboard'],
            'admin.orders.index'         => ['admin.orders.*', 'orders.show'],
            'client.orders.index'        => ['client.orders.*', 'orders.show'],
            'admin.clients.index'        => ['admin.clients.*'],
            'admin.surveys.index'        => ['admin.surveys.*'],
            'admin.work-estimates.index' => ['admin.work-estimates.*'],
            'approvals.index'            => ['approvals.*'],
            'projects.index'             => ['projects.*'],
            'plannings.index'            => ['plannings.*'],
            'productions.index'          => ['productions.*'],
            'bapps.index'                => ['bapps.*'],
            'documents.index'            => ['documents.*'],
            'client.tracking.index'      => ['client.tracking.*'],
            'invoices.index'             => ['invoices.*'],
            'admin.ratings.index'        => ['admin.ratings.*', 'ratings.show'],
            'client.ratings.index'       => ['client.ratings.*', 'ratings.show'],
            'admin.vendors.index'        => ['admin.vendors.*'],
            'admin.reports.index'        => ['admin.reports.*'],
            'direktur.reports.index'     => ['direktur.reports.*'],
            'admin.users.index'          => ['admin.users.*'],
        ];
    @endphp

    <div class="app-shell">
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

        <aside class="sidebar" id="sidebar">
            <a href="{{ route('dashboard') }}" class="sidebar-brand">
                <div class="sidebar-logo">S</div>
                <div class="sidebar-brand-text">
                    <h1>SIMOP</h1>
                    <p>Manajemen Operasional</p>
                </div>
            </a>

            <div class="sidebar-role">
                <p class="sidebar-role-label">Login Sebagai</p>
                <p class="sidebar-role-name">{{ $roleLabel }}</p>
            </div>

            <nav class="sidebar-nav">
                <p class="sidebar-nav-label">Menu Utama</p>

                @foreach ($currentMenus as [$label, $routeName])
                    @php
                        $routeExists = \Illuminate\Support\Facades\Route::has($routeName);
                        $routePatterns = $patterns[$routeName] ?? [$routeName];
                        $isActive = request()->routeIs(...$routePatterns);
                        $icon = $navIcons[$routeName] ?? '<svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4" stroke-width="2"/></svg>';
                    @endphp

                    @if ($routeExists)
                        <a href="{{ route($routeName) }}"
                           class="nav-item {{ $isActive ? 'active' : '' }}"
                           @if($isActive) aria-current="page" @endif>
                            {!! $icon !!}
                            <span>{{ $label }}</span>
                        </a>
                    @else
                        <div class="nav-item disabled">
                            {!! $icon !!}
                            <span>{{ $label }}</span>
                            <span class="nav-badge">Segera</span>
                        </div>
                    @endif
                @endforeach
            </nav>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-footer-btn">
                        <svg style="width:18px;height:18px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Keluar dari Akun
                    </button>
                </form>
            </div>
        </aside>

        <div class="main-area">
            <header class="app-header">
                <div class="header-left">
                    <button class="mobile-toggle" onclick="openSidebar()" aria-label="Buka menu">
                        <svg style="width:20px;height:20px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div class="header-divider"></div>
                    <div>
                        <p class="header-breadcrumb">SIMOP &middot; Manajemen Operasional</p>
                        <h2 class="header-title">@yield('page-title', 'Dashboard')</h2>
                    </div>
                </div>
                <div class="header-right">
                    <div class="header-user-chip">
                        <div class="header-avatar" title="{{ $user?->name ?? 'Pengguna' }}">
                            {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <p class="header-user-name">{{ $user?->name ?? 'Pengguna' }}</p>
                            <p class="header-user-role">{{ $roleLabel }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <main class="page-content">
                @if (session('success'))
                    <div class="flash-success">
                        <svg style="width:16px;height:16px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="flash-error">
                        <svg style="width:16px;height:16px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>

            <footer class="app-footer">
                SIMOP &copy; {{ date('Y') }} &middot; Sistem Informasi Manajemen Operasional
            </footer>
        </div>
    </div>

    <script>
        function openSidebar() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('sidebarOverlay').classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('open');
            document.body.style.overflow = '';
        }
    </script>
</body>
</html>