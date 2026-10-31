@extends('layouts.app')

@section('title', 'Tambah Mitra Kerja - SIMOP')
@section('page-title', 'Tambah Mitra Kerja')

@section('content')
    <div class="space-y-6">
        <div>
            <a
                href="{{ route('admin.vendors.index') }}"
                class="text-sm font-medium text-blue-600 hover:text-blue-700"
            >
                ← Kembali ke Mitra Kerja
            </a>

            <h1 class="mt-3 text-2xl font-bold text-slate-900">
                Tambah Mitra Kerja
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Tambahkan data mitra kerja baru untuk kebutuhan operasional SIMOP.
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
            action="{{ route('admin.vendors.store') }}"
            class="space-y-6"
        >
            @csrf

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        Informasi Mitra Kerja
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Masukkan informasi utama mitra kerja.
                    </p>
                </div>

                <div class="mt-6 grid gap-6 md:grid-cols-2">
                    <div class="md:col-span-2">
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
                            value="{{ old('name') }}"
                            required
                            maxlength="255"
                            placeholder="Contoh: PT Sumber Peralatan Indonesia"
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
                            value="{{ old('contact_person') }}"
                            maxlength="255"
                            placeholder="Nama penanggung jawab mitra kerja"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >

                        @error('contact_person')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
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
                            value="{{ old('service_type') }}"
                            maxlength="255"
                            placeholder="Contoh: Percetakan, Tata Suara, Transportasi"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >

                        @error('service_type')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
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
                            value="{{ old('phone') }}"
                            maxlength="30"
                            placeholder="Contoh: 081234567890"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >

                        @error('phone')
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
                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            maxlength="255"
                            placeholder="mitra@perusahaan.co.id"
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
                            for="address"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Alamat
                        </label>

                        <textarea
                            id="address"
                            name="address"
                            rows="4"
                            placeholder="Alamat lengkap mitra kerja"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >{{ old('address') }}</textarea>

                        @error('address')
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
                        Status & Catatan
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Tentukan status mitra kerja dan tambahkan catatan jika diperlukan.
                    </p>
                </div>

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
                                @selected(old('status', 'active') === 'active')
                            >
                                Aktif
                            </option>

                            <option
                                value="inactive"
                                @selected(old('status') === 'inactive')
                            >
                                Tidak Aktif
                            </option>
                        </select>

                        @error('status')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
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
                            placeholder="Catatan tambahan mengenai mitra kerja"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >{{ old('notes') }}</textarea>

                        @error('notes')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a
                    href="{{ route('admin.vendors.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    Simpan Mitra Kerja
                </button>
            </div>
        </form>
    </div>
@endsection
