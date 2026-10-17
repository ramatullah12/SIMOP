
@extends('layouts.app')

@section('title', 'Dashboard Client | SIMOP')
@section('page-title', 'Dashboard Client')

@section('content')
<div class="rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
    <h1 class="text-2xl font-bold text-slate-900">
        Dashboard Client SIMOP
    </h1>

    <p class="mt-3 text-slate-600">
        Selamat datang, {{ auth()->user()->name }}.
    </p>

    <p class="mt-2 text-sm text-slate-500">
        Akun Client berhasil login. Selanjutnya Anda dapat mengakses fitur Order & RFQ.
    </p>

    <a href="{{ route('client.orders.index') }}"
       class="mt-6 inline-block rounded-lg bg-blue-600 px-5 py-3 font-semibold text-white hover:bg-blue-700">
        Buka Order & RFQ
    </a>
</div>
@endsection
