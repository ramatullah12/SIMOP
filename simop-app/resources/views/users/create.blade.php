@extends('layouts.app')

@section('title', 'Tambah Pengguna - SIMOP')
@section('page-title', 'Tambah Pengguna')

@section('content')
    <div class="space-y-6">
        <div>
            <a
                href="{{ route('admin.users.index') }}"
                class="text-sm font-medium text-blue-600 hover:text-blue-700"
            >
                ← Kembali ke Pengguna
            </a>

            <h1 class="mt-3 text-2xl font-bold text-slate-900">
                Tambah Pengguna
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Buat akun baru dan tentukan hak akses pengguna SIMOP.
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                <h2 class="font-semibold text-red-800">
                    Data belum dapat disimpan
                </h2>

                <ul class="mt-3 list-disc space-y-1 pl-5 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('admin.users.store') }}"
            class="space-y-6"
        >
            @csrf

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        Informasi Akun
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Masukkan identitas dasar pengguna.
                    </p>
                </div>

                <div class="mt-6 grid gap-6 md:grid-cols-2">
                    <div>
                        <label
                            for="name"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Nama Pengguna
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            maxlength="255"
                            autocomplete="name"
                            placeholder="Masukkan nama pengguna"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >

                        @error('name')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="email"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Email
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            maxlength="255"
                            autocomplete="email"
                            placeholder="contoh@simop.test"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >

                        @error('email')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label
                            for="role_id"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Role
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="role_id"
                            name="role_id"
                            required
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">
                                Pilih Role
                            </option>

                            @foreach ($roles as $role)
                                <option
                                    value="{{ $role->id }}"
                                    @selected((string) old('role_id') === (string) $role->id)
                                >
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('role_id')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Role menentukan menu dan fitur yang dapat diakses oleh pengguna.
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        Password
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Buat password awal untuk akun pengguna.
                    </p>
                </div>

                <div class="mt-6 grid gap-6 md:grid-cols-2">
                    <div>
                        <label
                            for="password"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Password
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            minlength="8"
                            autocomplete="new-password"
                            placeholder="Minimal 8 karakter"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >

                        @error('password')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="password_confirmation"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Konfirmasi Password
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            minlength="8"
                            autocomplete="new-password"
                            placeholder="Ulangi password"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <div class="flex gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-700">
                        i
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-blue-900">
                            Informasi Akun
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-blue-700">
                            Akun yang dibuat oleh Admin akan langsung dianggap terverifikasi sehingga dapat login menggunakan email dan password yang dibuat.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a
                    href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
@endsection