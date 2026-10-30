@extends('layouts.app')

@section('title', 'Edit Pengguna - SIMOP')
@section('page-title', 'Edit Pengguna')

@section('content')
    <div class="space-y-6">
        <div>
            <a
                href="{{ route('admin.users.show', $user) }}"
                class="text-sm font-medium text-blue-600 hover:text-blue-700"
            >
                ← Kembali ke Detail Pengguna
            </a>

            <h1 class="mt-3 text-2xl font-bold text-slate-900">
                Edit Pengguna
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Perbarui informasi akun {{ $user->name }}.
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                <h2 class="font-semibold text-red-800">
                    Data belum dapat diperbarui
                </h2>

                <ul class="mt-3 list-disc space-y-1 pl-5 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (auth()->id() === $user->id)
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                <p class="text-sm font-semibold text-amber-900">
                    Anda sedang mengedit akun sendiri.
                </p>

                <p class="mt-1 text-sm leading-6 text-amber-700">
                    Pastikan perubahan tidak menyebabkan akun Anda kehilangan akses Admin.
                </p>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('admin.users.update', $user) }}"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        Informasi Akun
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Perbarui identitas dan role pengguna.
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
                            value="{{ old('name', $user->name) }}"
                            required
                            maxlength="255"
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
                            value="{{ old('email', $user->email) }}"
                            required
                            maxlength="255"
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
                            @foreach ($roles as $role)
                                <option
                                    value="{{ $role->id }}"
                                    @selected((string) old('role_id', $user->role_id) === (string) $role->id)
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
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        Ubah Password
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Kosongkan kedua kolom jika password tidak ingin diubah.
                    </p>
                </div>

                <div class="mt-6 grid gap-6 md:grid-cols-2">
                    <div>
                        <label
                            for="password"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Password Baru
                        </label>

                        <input
                            id="password"
                            type="password"
                            name="password"
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
                            Konfirmasi Password Baru
                        </label>

                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            minlength="8"
                            autocomplete="new-password"
                            placeholder="Ulangi password baru"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a
                    href="{{ route('admin.users.show', $user) }}"
                    class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection