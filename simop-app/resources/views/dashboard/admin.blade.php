
@extends('layouts.app')

@section('title', 'Dashboard Admin | SIMOP')
@section('page-title', 'Dashboard Admin')

@section('content')
    <div class="space-y-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Dashboard Admin</h1>
            <p class="mt-2 text-slate-500">Ringkasan operasional perusahaan.</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['Total Client', '0'],
                ['Order Baru', '0'],
                ['Project Aktif', '0'],
                ['Approval Tertunda', '0'],
            ] as [$label, $value])
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-slate-500">{{ $label }}</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
                <h3 class="text-lg font-bold">Ringkasan Nilai Order</h3>
                <p class="mt-2 text-sm text-slate-500">Grafik akan dihubungkan dengan data order.</p>
                <div class="mt-6 flex h-56 items-center justify-center rounded-lg bg-slate-50 text-sm text-slate-400">
                    Grafik Nilai Order
                </div>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold">Aktivitas Terbaru</h3>
                <p class="mt-3 text-sm text-slate-500">Aktivitas sistem akan ditampilkan di sini.</p>
            </section>
        </div>
    </div>
@endsection
