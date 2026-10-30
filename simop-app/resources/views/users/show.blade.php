@extends('layouts.app')

@section('title', 'Detail Pengguna - SIMOP')
@section('page-title', 'Detail Pengguna')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <a
                    href="{{ route('admin.users.index') }}"
                    class="text-sm font-medium text-blue-600 hover:text-blue-700"
                >
                    ← Kembali ke Pengguna
                </a>

                <div class="mt-4 flex items-center gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xl font-bold uppercase text-blue-700">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>

                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">
                            {{ $user->name }}
                        </h1>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $user->email }}
                        </p>
                    </div>
                </div>
            </div>

            <a
                href="{{ route('admin.users.edit', $user) }}"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
            >
                Edit Pengguna
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-medium text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
                <h2 class="text-lg font-bold text-slate-900">
                    Informasi Pengguna
                </h2>

                <div class="mt-6 grid gap-6 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Nama
                        </p>

                        <p class="mt-2 text-sm font-semibold text-slate-800">
                            {{ $user->name }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Email
                        </p>

                        <p class="mt-2 break-all text-sm font-semibold text-slate-800">
                            {{ $user->email }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Role
                        </p>

                        <div class="mt-2">
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
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Status Verifikasi
                        </p>

                        <div class="mt-2">
                            @if ($user->email_verified_at)
                                <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                    Terverifikasi
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                    Belum Terverifikasi
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">
                    Informasi Akun
                </h2>

                <div class="mt-5 space-y-5">
                    <div>
                        <p class="text-xs font-medium text-slate-400">
                            ID Pengguna
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-700">
                            #{{ $user->id }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-slate-400">
                            Dibuat
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-700">
                            {{ $user->created_at?->format('d/m/Y H:i') ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-slate-400">
                            Terakhir Diperbarui
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-700">
                            {{ $user->updated_at?->format('d/m/Y H:i') ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if (auth()->id() === $user->id)
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <p class="text-sm font-semibold text-blue-900">
                    Ini adalah akun yang sedang Anda gunakan.
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Perubahan email, password, atau role pada akun ini dapat memengaruhi akses Anda ke SIMOP.
                </p>
            </div>
        @endif
    </div>
@endsection
