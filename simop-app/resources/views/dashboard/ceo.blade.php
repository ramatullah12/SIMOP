
@extends('layouts.app')

@section('title', 'Dashboard CEO | SIMOP')
@section('page-title', 'Dashboard CEO')

@section('content')
<div class="space-y-6">

    <div class="rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-900">
            Dashboard CEO SIMOP
        </h1>

        <p class="mt-3 text-slate-600">
            Selamat datang, {{ auth()->user()->name }}.
        </p>

        <p class="mt-2 text-sm text-slate-500">
            Kelola pemantauan operasional dan tinjau persetujuan
            Work Estimate sesuai kewenangan CEO.
        </p>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">
                Approval Work Estimate
            </h2>

            <p class="mt-2 text-sm text-slate-600">
                Tinjau pengajuan estimasi biaya dan pekerjaan,
                kemudian berikan keputusan persetujuan.
            </p>

            <a href="{{ route('approvals.index') }}"
               class="mt-5 inline-block rounded-lg bg-blue-600 px-5 py-3
                      font-semibold text-white hover:bg-blue-700">
                Buka Approval
            </a>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">
                Pemantauan Operasional
            </h2>

            <p class="mt-2 text-sm text-slate-600">
                Gunakan menu yang tersedia untuk meninjau proses
                persetujuan dan status pengajuan.
            </p>

            <a href="{{ route('approvals.index') }}"
               class="mt-5 inline-block rounded-lg bg-slate-800 px-5 py-3
                      font-semibold text-white hover:bg-slate-900">
                Lihat Pengajuan
            </a>
        </div>

    </div>
</div>
@endsection
