
@extends('layouts.app')

@section('title', 'Dashboard Admin | SIMOP')
@section('page-title', 'Dashboard Admin')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-header-title">Dashboard Admin</h1>
        <p class="page-header-sub">Ringkasan operasional perusahaan hari ini.</p>
    </div>
    <span style="font-size:0.75rem;font-weight:600;color:#94a3b8;background:#f1f5f9;border:1px solid #e2e8f0;padding:6px 14px;border-radius:999px;">
        {{ now()->translatedFormat('d F Y') }}
    </span>
</div>

<div style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));margin-bottom:24px;">

    <div class="stat-card">
        <div class="stat-card-accent" style="background:#3b82f6;"></div>
        <div class="stat-card-top">
            <div class="stat-card-icon" style="background:#eff6ff;">
                <svg style="width:22px;height:22px;color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="stat-card-trend" style="background:#eff6ff;color:#2563eb;">Client</span>
        </div>
        <p class="stat-card-value">0</p>
        <p class="stat-card-label">Total Client</p>
    </div>

    <div class="stat-card">
        <div class="stat-card-accent" style="background:#f59e0b;"></div>
        <div class="stat-card-top">
            <div class="stat-card-icon" style="background:#fffbeb;">
                <svg style="width:22px;height:22px;color:#d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <span class="stat-card-trend" style="background:#fffbeb;color:#d97706;">Order</span>
        </div>
        <p class="stat-card-value">0</p>
        <p class="stat-card-label">Order Baru</p>
    </div>

    <div class="stat-card">
        <div class="stat-card-accent" style="background:#10b981;"></div>
        <div class="stat-card-top">
            <div class="stat-card-icon" style="background:#ecfdf5;">
                <svg style="width:22px;height:22px;color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <span class="stat-card-trend" style="background:#ecfdf5;color:#059669;">Aktif</span>
        </div>
        <p class="stat-card-value">0</p>
        <p class="stat-card-label">Project Aktif</p>
    </div>

    <div class="stat-card">
        <div class="stat-card-accent" style="background:#ef4444;"></div>
        <div class="stat-card-top">
            <div class="stat-card-icon" style="background:#fef2f2;">
                <svg style="width:22px;height:22px;color:#dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="stat-card-trend" style="background:#fef2f2;color:#dc2626;">Pending</span>
        </div>
        <p class="stat-card-value">0</p>
        <p class="stat-card-label">Approval Tertunda</p>
    </div>

</div>

<div style="display:grid;gap:20px;grid-template-columns:1fr 360px;">

    <div class="card">
        <div class="card-header">
            <div>
                <p style="margin:0;font-weight:700;font-size:0.9375rem;color:#0f172a;">Ringkasan Nilai Order</p>
                <p style="margin:4px 0 0;font-size:0.75rem;font-weight:400;color:#94a3b8;">Grafik nilai order per periode</p>
            </div>
            <span class="badge badge-blue">Bulan Ini</span>
        </div>
        <div class="card-body">
            <div style="height:220px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;background:linear-gradient(135deg,#f8fafd,#eef3fb);border-radius:12px;border:1px dashed #d1def5;">
                <svg style="width:36px;height:36px;color:#c7d7f0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <p style="font-size:0.8125rem;color:#a0aec0;font-weight:500;margin:0;">Grafik akan tersedia setelah ada data order</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <p style="margin:0;font-weight:700;font-size:0.9375rem;color:#0f172a;">Aktivitas Terbaru</p>
                <p style="margin:4px 0 0;font-size:0.75rem;font-weight:400;color:#94a3b8;">Log aktivitas sistem</p>
            </div>
        </div>
        <div class="card-body">
            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:180px;gap:10px;">
                <div style="width:48px;height:48px;background:#f1f5f9;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <svg style="width:24px;height:24px;color:#94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <p style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin:0;text-align:center;">Belum ada aktivitas terbaru</p>
                <p style="font-size:0.72rem;color:#b8c5d6;margin:0;text-align:center;">Aktivitas sistem akan muncul di sini</p>
            </div>
        </div>
    </div>

</div>

@endsection
