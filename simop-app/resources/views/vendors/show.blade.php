@extends('layouts.app')

@section('title', 'Detail Mitra Kerja - SIMOP')
@section('page-title', 'Detail Mitra Kerja')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <a
                    href="{{ route('admin.vendors.index') }}"
                    class="text-sm font-medium text-blue-600 hover:text-blue-700"
                >
                    ← Kembali ke Mitra Kerja
                </a>

                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl font-bold text-slate-900">
                        {{ $vendor->name }}
                    </h1>

                    @if ($vendor->status === 'active')
                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                            Aktif
                        </span>
                    @else
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                            Tidak Aktif
                        </span>
                    @endif
                </div>

                <p class="mt-1 text-sm font-semibold text-blue-600">
                    {{ $vendor->vendor_code }}
                </p>
            </div>

            <a
                href="{{ route('admin.vendors.edit', $vendor) }}"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
            >
                Ubah Mitra Kerja
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
                    Informasi Mitra Kerja
                </h2>

                <div class="mt-6 grid gap-6 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Kode Mitra Kerja
                        </p>

                        <p class="mt-2 text-sm font-semibold text-slate-800">
                            {{ $vendor->vendor_code }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Nama Mitra Kerja
                        </p>

                        <p class="mt-2 text-sm font-semibold text-slate-800">
                            {{ $vendor->name }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Penanggung Jawab / Narahubung
                        </p>

                        <p class="mt-2 text-sm font-semibold text-slate-800">
                            {{ $vendor->contact_person ?: '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Jenis Layanan
                        </p>

                        <p class="mt-2 text-sm font-semibold text-slate-800">
                            {{ $vendor->service_type ?: '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Nomor Telepon
                        </p>

                        <p class="mt-2 text-sm font-semibold text-slate-800">
                            {{ $vendor->phone ?: '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Email
                        </p>

                        <p class="mt-2 break-all text-sm font-semibold text-slate-800">
                            {{ $vendor->email ?: '-' }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Alamat
                        </p>

                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $vendor->address ?: '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-900">
                        Status Mitra Kerja
                    </h2>

                    <div class="mt-5">
                        @if ($vendor->status === 'active')
                            <span class="inline-flex rounded-full bg-green-100 px-4 py-2 text-sm font-semibold text-green-700">
                                Mitra Kerja Aktif
                            </span>
                        @else
                            <span class="inline-flex rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600">
                                Mitra Kerja Tidak Aktif
                            </span>
                        @endif
                    </div>

                    <p class="mt-4 text-sm leading-6 text-slate-500">
                        Mitra kerja aktif dapat digunakan untuk mendukung kebutuhan operasional SIMOP.
                    </p>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-900">
                        Informasi Data
                    </h2>

                    <div class="mt-5 space-y-4">
                        <div>
                            <p class="text-xs font-medium text-slate-400">
                                Dibuat
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-700">
                                {{ $vendor->created_at?->format('d/m/Y H:i') ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-slate-400">
                                Terakhir Diperbarui
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-700">
                                {{ $vendor->updated_at?->format('d/m/Y H:i') ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">
                Catatan
            </h2>

            <div class="mt-4 rounded-xl bg-slate-50 p-4">
                <p class="whitespace-pre-line text-sm leading-6 text-slate-700">{{ $vendor->notes ?: 'Tidak ada catatan.' }}</p>
            </div>
        </div>
    </div>
@endsection
