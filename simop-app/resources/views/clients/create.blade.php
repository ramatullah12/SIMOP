
@extends('layouts.app')

@section('title', 'Tambah Client | SIMOP')
@section('page-title', 'Tambah Client')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Tambah Client</h1>
        <p class="mt-2 text-sm text-slate-500">Data perusahaan dan akun login akan dibuat sekaligus.</p>
    </div>

    @if ($errors->any())
        <div class="rounded-lg bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.clients.store') }}"
          class="space-y-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium">Nama Pengguna</label>
                <input name="name" value="{{ old('name') }}" required
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium">Nama Perusahaan</label>
                <input name="company_name" value="{{ old('company_name') }}" required
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium">Contact Person</label>
                <input name="contact_person" value="{{ old('contact_person') }}" required
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium">Email Login dan Kontak</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium">Nomor Telepon</label>
                <input name="phone" value="{{ old('phone') }}"
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium">Jenis Perusahaan</label>
                <input name="company_type" value="{{ old('company_type') }}"
                       placeholder="Contoh: Swasta, Pemerintah"
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium">Alamat Perusahaan</label>
                <input name="address" value="{{ old('address') }}"
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium">Password Login</label>
                <input type="password" name="password" required
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium">Catatan</label>
                <textarea name="notes" rows="3"
                          class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
            <a href="{{ route('admin.clients.index') }}"
               class="rounded-lg border border-slate-300 px-5 py-3 text-sm font-semibold">Batal</a>
            <button type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                Simpan Client
            </button>
        </div>
    </form>
</div>
@endsection
