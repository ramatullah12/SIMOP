
@extends('layouts.app')

@section('title', 'Detail Order | SIMOP')
@section('page-title', 'Detail Order')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">

    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <p class="text-sm text-slate-500">{{ $order->order_number }}</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">
                {{ $order->event_name }}
            </h1>
        </div>

        <a href="{{ route($role === 'admin' ? 'admin.orders.index' : 'client.orders.index') }}"
           class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-center text-sm font-semibold hover:bg-slate-50">
            Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="rounded-lg bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="text-lg font-bold">Informasi Pekerjaan</h2>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <p class="text-sm text-slate-500">Nama Perusahaan</p>
                    <p class="mt-1 font-medium">{{ $order->company_name }}</p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Contact Person</p>
                    <p class="mt-1 font-medium">{{ $order->contact_person }}</p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Nomor Telepon</p>
                    <p class="mt-1 font-medium">{{ $order->phone }}</p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Email</p>
                    <p class="mt-1 font-medium">{{ $order->email }}</p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Tanggal Event</p>
                    <p class="mt-1 font-medium">{{ $order->event_date->format('d/m/Y') }}</p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Lokasi Event</p>
                    <p class="mt-1 font-medium">{{ $order->event_location }}</p>
                </div>

                <div class="sm:col-span-2">
                    <p class="text-sm text-slate-500">Deskripsi Kebutuhan</p>
                    <p class="mt-1 whitespace-pre-line">{{ $order->description }}</p>
                </div>

                <div class="sm:col-span-2">
                    <p class="text-sm text-slate-500">Estimasi Anggaran</p>
                    <p class="mt-1 font-semibold">
                        {{ $order->estimated_budget !== null
                            ? 'Rp ' . number_format((float) $order->estimated_budget, 0, ',', '.')
                            : 'Belum ditentukan' }}
                    </p>
                </div>
            </div>
        </section>

        <section class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold">Status Order</h2>

            <div>
                <p class="text-sm text-slate-500">Status Saat Ini</p>
                <span class="mt-2 inline-flex rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-700">
                    {{ str($order->status)->replace('_', ' ')->title() }}
                </span>
            </div>

            <div>
                <p class="text-sm text-slate-500">Tanggal Pengajuan</p>
                <p class="mt-1 font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>

            @if ($role === 'admin')
                <div>
                    <p class="text-sm text-slate-500">Client</p>
                    <p class="mt-1 font-medium">{{ $order->client->name ?? '-' }}</p>
                </div>

                <form method="POST"
                      action="{{ route('admin.orders.status', $order) }}"
                      class="space-y-4 border-t border-slate-100 pt-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="mb-2 block text-sm font-medium">Ubah Status</label>
                        <select name="status" required
                                class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            @foreach ([
                                'baru' => 'Baru',
                                'diperiksa' => 'Sedang Diperiksa',
                                'perlu_revisi' => 'Perlu Revisi',
                                'ditolak' => 'Ditolak',
                                'menunggu_survey' => 'Menunggu Survey',
                            ] as $value => $label)
                                <option value="{{ $value }}" @selected($order->status === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium">Catatan Admin</label>
                        <textarea name="admin_notes" rows="4"
                                  class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                    </div>

                    @if ($errors->any())
                        <div class="text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <button type="submit"
                            class="w-full rounded-lg bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                        Simpan Perubahan
                    </button>
                </form>
            @elseif ($order->admin_notes)
                <div class="border-t border-slate-100 pt-4">
                    <p class="text-sm font-semibold">Catatan Admin</p>
                    <p class="mt-2 whitespace-pre-line text-sm text-slate-600">{{ $order->admin_notes }}</p>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
