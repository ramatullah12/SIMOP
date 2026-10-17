
@extends('layouts.app')

@section('title', 'Dashboard Client | SIMOP')
@section('page-title', 'Dashboard Client')

@section('content')
<div class="rounded-xl bg-white p-8 shadow-sm">
    <h1 class="text-2xl font-bold text-slate-900">
        Dashboard Client SIMOP
    </h1>

    <p class="mt-3 text-slate-600">
        Selamat datang, {{ auth()->user()->name }}.
    </p>

    <a href="{{ route('client.orders.index') }}"
       class="mt-6 inline-block rounded-lg bg-blue-600 px-5 py-3 text-white">
        Buka Order & RFQ
    </a>
</div>
@endsection
