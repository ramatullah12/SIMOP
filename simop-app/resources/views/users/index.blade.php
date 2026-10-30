@extends('layouts.app')

@section('title', 'Pengguna - SIMOP')
@section('page-title', 'Pengguna')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Pengguna
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Kelola akun dan hak akses pengguna SIMOP.
                </p>
            </div>

            <a
                href="{{ route('admin.users.create') }}"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
            >
                + Tambah Pengguna
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-medium text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <form
                method="GET"
                action="{{ route('admin.users.index') }}"
                class="grid gap-4 lg:grid-cols-[1fr_240px_auto]"
            >
                <div>
                    <label
                        for="search"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    >
                        Cari Pengguna
                    </label>

                    <input
                        id="search"
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari nama atau email..."
                        class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label
                        for="role"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    >
                        Role
                    </label>

                    <select
                        id="role"
                        name="role"
                        class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">
                            Semua Role
                        </option>

                        @foreach ($roles as $role)
                            <option
                                value="{{ $role->id }}"
                                @selected((string) $roleId === (string) $role->id)
                            >
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button
                        type="submit"
                        class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800"
                    >
                        Filter
                    </button>

                    @if ($search !== '' || $roleId)
                        <a
                            href="{{ route('admin.users.index') }}"
                            class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            @if ($users->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Pengguna
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Role
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Verifikasi
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Dibuat
                                </th>

                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($users as $user)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold uppercase text-blue-700">
                                                {{ mb_substr($user->name, 0, 1) }}
                                            </div>

                                            <div>
                                                <p class="font-semibold text-slate-900">
                                                    {{ $user->name }}
                                                </p>

                                                <p class="mt-1 text-xs text-slate-500">
                                                    {{ $user->email }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($user->role)
                                            @php
                                                $roleClass = match ($user->role->slug) {
                                                    'admin' => 'bg-blue-100 text-blue-700',
                                                    'direktur' => 'bg-purple-100 text-purple-700',
                                                    'petugas-lapangan' => 'bg-amber-100 text-amber-700',
                                                    'client' => 'bg-emerald-100 text-emerald-700',
                                                    default => 'bg-slate-100 text-slate-700',
                                                };
                                            @endphp

                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $roleClass }}">
                                                {{ $user->role->name }}
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                                Role Belum Diatur
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex flex-col items-start gap-2">
                                            @if ($user->email_verified_at)
                                                <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                                    Terverifikasi
                                                </span>
                                            @else
                                                <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                                    Belum Terverifikasi
                                                </span>
                                            @endif

                                            @if (auth()->id() !== $user->id)
                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.users.verification', $user) }}"
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    @if ($user->email_verified_at)
                                                        <button
                                                            type="submit"
                                                            class="text-xs font-semibold text-red-600 transition hover:text-red-700"
                                                        >
                                                            Batalkan Verifikasi
                                                        </button>
                                                    @else
                                                        <button
                                                            type="submit"
                                                            class="text-xs font-semibold text-blue-600 transition hover:text-blue-700"
                                                        >
                                                            Verifikasi
                                                        </button>
                                                    @endif
                                                </form>
                                            @else
                                                <span class="text-xs text-slate-400">
                                                    Akun saat ini
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                        {{ $user->created_at?->format('d/m/Y H:i') ?? '-' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a
                                                href="{{ route('admin.users.show', $user) }}"
                                                class="text-sm font-semibold text-blue-600 hover:text-blue-700"
                                            >
                                                Detail
                                            </a>

                                            <a
                                                href="{{ route('admin.users.edit', $user) }}"
                                                class="text-sm font-semibold text-amber-600 hover:text-amber-700"
                                            >
                                                Edit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="border-t border-slate-200 px-6 py-4">
                        {{ $users->links() }}
                    </div>
                @endif
            @else
                <div class="px-6 py-14 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-blue-50 text-xl font-bold text-blue-600">
                        P
                    </div>

                    <h2 class="mt-4 text-lg font-bold text-slate-800">
                        Pengguna Tidak Ditemukan
                    </h2>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                        Belum ada pengguna yang sesuai dengan pencarian atau filter yang dipilih.
                    </p>

                    @if ($search !== '' || $roleId)
                        <a
                            href="{{ route('admin.users.index') }}"
                            class="mt-5 inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            Hapus Filter
                        </a>
                    @else
                        <a
                            href="{{ route('admin.users.create') }}"
                            class="mt-5 inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                        >
                            + Tambah Pengguna
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection