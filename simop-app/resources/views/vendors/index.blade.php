@extends('layouts.app')

@section('title', 'Mitra Kerja - SIMOP')
@section('page-title', 'Mitra Kerja')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Mitra Kerja
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Kelola data mitra kerja (penyedia barang/jasa) yang mendukung kebutuhan operasional proyek.
                </p>
            </div>

            <a
                href="{{ route('admin.vendors.create') }}"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
            >
                + Tambah Mitra Kerja
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-medium text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <form
                method="GET"
                action="{{ route('admin.vendors.index') }}"
                class="grid gap-4 lg:grid-cols-[1fr_220px_auto]"
            >
                <div>
                    <label
                        for="search"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    >
                        Cari Mitra Kerja
                    </label>

                    <input
                        id="search"
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Kode, nama, narahubung, telepon, email..."
                        class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label
                        for="status"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    >
                        Status
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">Semua Status</option>
                        <option
                            value="active"
                            @selected($status === 'active')
                        >
                            Aktif
                        </option>
                        <option
                            value="inactive"
                            @selected($status === 'inactive')
                        >
                            Tidak Aktif
                        </option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button
                        type="submit"
                        class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800"
                    >
                        Saring
                    </button>

                    @if ($search !== '' || $status)
                        <a
                            href="{{ route('admin.vendors.index') }}"
                            class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            Atur Ulang
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            @if ($vendors->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Mitra Kerja
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Kontak
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Jenis Layanan
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Status
                                </th>

                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($vendors as $vendor)
                                <tr class="hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <p class="font-semibold text-slate-900">
                                            {{ $vendor->name }}
                                        </p>

                                        <p class="mt-1 text-xs font-medium text-blue-600">
                                            {{ $vendor->vendor_code }}
                                        </p>
                                    </td>

                                    <td class="px-6 py-4">
                                        <p class="text-sm font-medium text-slate-700">
                                            {{ $vendor->contact_person ?: '-' }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $vendor->phone ?: '-' }}
                                        </p>

                                        @if ($vendor->email)
                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ $vendor->email }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-700">
                                        {{ $vendor->service_type ?: '-' }}
                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($vendor->status === 'active')
                                            <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                                Tidak Aktif
                                            </span>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a
                                                href="{{ route('admin.vendors.show', $vendor) }}"
                                                class="text-sm font-semibold text-blue-600 hover:text-blue-700"
                                            >
                                                Detail
                                            </a>

                                            <a
                                                href="{{ route('admin.vendors.edit', $vendor) }}"
                                                class="text-sm font-semibold text-amber-600 hover:text-amber-700"
                                            >
                                                Ubah
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($vendors->hasPages())
                    <div class="border-t border-slate-200 px-6 py-4">
                        {{ $vendors->links() }}
                    </div>
                @endif
            @else
                <div class="px-6 py-14 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-blue-50 text-xl text-blue-600">
                        M
                    </div>

                    <h2 class="mt-4 text-lg font-bold text-slate-800">
                        Belum Ada Mitra Kerja
                    </h2>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                        Tambahkan mitra kerja untuk menyimpan informasi penyedia barang atau jasa yang digunakan dalam kegiatan operasional.
                    </p>

                    <a
                        href="{{ route('admin.vendors.create') }}"
                        class="mt-5 inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        + Tambah Mitra Kerja
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
