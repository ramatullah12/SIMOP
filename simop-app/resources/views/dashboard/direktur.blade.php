
@extends('layouts.app')

@section('title', 'Dashboard Direktur | SIMOP')
@section('page-title', 'Dashboard Direktur')

@section('content')
<div class="space-y-6">

    <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800">
            Selamat Datang, Direktur!
        </h2>
        <p class="mt-2 text-gray-600">
            Selamat datang di Sistem Informasi Manajemen Operasional (SIMOP).
            Silakan tinjau dan berikan persetujuan terhadap Work Estimate
            yang diajukan oleh Admin.
        </p>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800">
                Approval Work Estimate
            </h3>
            <p class="mt-2 text-sm text-gray-600">
                Periksa estimasi biaya dan pekerjaan sebelum memberikan keputusan.
            </p>
            <a href="{{ route('approvals.index') }}"
               class="mt-4 inline-block rounded-lg bg-blue-600 px-4 py-2
                      text-sm font-semibold text-white hover:bg-blue-700">
                Buka Approval
            </a>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800">
                Status Persetujuan
            </h3>
            <p class="mt-2 text-sm text-gray-600">
                Lihat daftar pengajuan Work Estimate dan status persetujuannya.
            </p>
            <a href="{{ route('approvals.index') }}"
               class="mt-4 inline-block rounded-lg bg-gray-800 px-4 py-2
                      text-sm font-semibold text-white hover:bg-gray-900">
                Lihat Pengajuan
            </a>
        </div>
    </div>

</div>
@endsection
