@extends('layouts.app')

@section('title', 'Ubah Mitra Kerja - SIMOP')
@section('page-title', 'Ubah Mitra Kerja')

@section('content')
    <div class="space-y-6">
        <div>
            <a
                href="{{ route('admin.vendors.show', $vendor) }}"
                class="text-sm font-medium text-blue-600 hover:text-blue-700"
            >
                ← Kembali ke Detail Mitra Kerja
            </a>

            <h1 class="mt-3 text-2xl font-bold text-slate-900">
                Ubah Mitra Kerja
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Perbarui informasi {{ $vendor->name }}.
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

        <form
            method="POST"
            action="{{ route('admin.vendors.update', $vendor) }}"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">
                    Informasi Mitra Kerja
                </h2>

                <div class="mt-6 grid gap-6 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Kode Mitra Kerja
                        </label>

                        <input
                            type="text"
                            value="{{ $vendor->vendor_code }}"
                            disabled
                            class="w-full cursor-not-allowed rounded-lg border-slate-300 bg-slate-100 text-sm text-slate-500"
                        >

                        <p class="mt-2 text-xs text-slate-400">
                            Kode mitra kerja dibuat otomatis dan tidak dapat diubah.
                        </p>
                    </div>

                    <div>
                        <label
                            for="name"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Nama Mitra Kerja
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name', $vendor->name) }}"
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
                            for="contact_person"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Penanggung Jawab / Narahubung
                        </label>

                        <input
                            id="contact_person"
                            type="text"
                            name="contact_person"
                            value="{{ old('contact_person', $vendor->contact_person) }}"
                            maxlength="255"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <div>
                        <label
                            for="service_type"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Jenis Layanan
                        </label>

                        <input
                            id="service_type"
                            type="text"
                            name="service_type"
                            value="{{ old('service_type', $vendor->service_type) }}"
                            maxlength="255"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <div>
                        <label
                            for="phone"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Nomor Telepon
                        </label>

                        <input
                            id="phone"
                            type="text"
                            name="phone"
                            value="{{ old('phone', $vendor->phone) }}"
                            maxlength="30"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <div>
                        <label
                            for="email"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Email
                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email', $vendor->email) }}"
                            maxlength="255"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label
                            for="address"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Alamat
                        </label>

                        <textarea
                            id="address"
                            name="address"
                            rows="4"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >{{ old('address', $vendor->address) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">
                    Status & Catatan
                </h2>

                <div class="mt-6 grid gap-6">
                    <div>
                        <label
                            for="status"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Status
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="status"
                            name="status"
                            required
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option
                                value="active"
                                @selected(old('status', $vendor->status) === 'active')
                            >
                                Aktif
                            </option>

                            <option
                                value="inactive"
                                @selected(old('status', $vendor->status) === 'inactive')
                            >
                                Tidak Aktif
                            </option>
                        </select>
                    </div>

                    <div>
                        <label
                            for="notes"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Catatan
                        </label>

                        <textarea
                            id="notes"
                            name="notes"
                            rows="4"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >{{ old('notes', $vendor->notes) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a
                    href="{{ route('admin.vendors.show', $vendor) }}"
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
