
@extends('layouts.app')

@section('title', 'Detail Client | SIMOP')
@section('page-title', 'Detail Client')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <p class="text-sm text-slate-500">Profil perusahaan</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">{{ $client->company_name }}</h1>
        </div>
        <a href="{{ route('admin.clients.index') }}"
           class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-center text-sm font-semibold">Kembali</a>
    </div>

    @if (session('success'))
        <div class="rounded-lg bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-1">
            <h2 class="text-lg font-bold">Informasi Akun</h2>
            <div>
                <p class="text-sm text-slate-500">Nama Pengguna</p>
                <p class="mt-1 font-medium">{{ $client->user->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Email Login</p>
                <p class="mt-1 font-medium">{{ $client->user->email ?? $client->email }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Status</p>
                <p class="mt-1 font-semibold">{{ ucfirst($client->status) }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Tanggal Terdaftar</p>
                <p class="mt-1 font-medium">{{ $client->created_at->format('d/m/Y') }}</p>
            </div>
        </section>

        <section class="space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="text-lg font-bold">Informasi Perusahaan</h2>

            <form method="POST" action="{{ route('admin.clients.update', $client) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium">Nama Perusahaan</label>
                        <input name="company_name" value="{{ old('company_name', $client->company_name) }}" required
                               class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Contact Person</label>
                        <input name="contact_person" value="{{ old('contact_person', $client->contact_person) }}" required
                               class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email', $client->email) }}" required
                               class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Telepon</label>
                        <input name="phone" value="{{ old('phone', $client->phone) }}"
                               class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Jenis Perusahaan</label>
                        <input name="company_type" value="{{ old('company_type', $client->company_type) }}"
                               class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Status</label>
                        <select name="status" required class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="aktif" @selected($client->status === 'aktif')>Aktif</option>
                            <option value="nonaktif" @selected($client->status === 'nonaktif')>Nonaktif</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-sm font-medium">Alamat</label>
                        <input name="address" value="{{ old('address', $client->address) }}"
                               class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-sm font-medium">Catatan</label>
                        <textarea name="notes" rows="3" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $client->notes) }}</textarea>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="rounded-lg bg-red-50 p-4 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <button type="submit" class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                    Simpan Perubahan
                </button>
            </form>
        </section>
    </div>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4">
            <h2 class="text-lg font-bold">Riwayat Order</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-4">Nomor Order</th>
                        <th class="px-5 py-4">Pekerjaan</th>
                        <th class="px-5 py-4">Tanggal Event</th>
                        <th class="px-5 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($client->orders as $order)
                        <tr>
                            <td class="px-5 py-4 font-semibold">{{ $order->order_number }}</td>
                            <td class="px-5 py-4">{{ $order->event_name }}</td>
                            <td class="px-5 py-4">{{ $order->event_date->format('d/m/Y') }}</td>
                            <td class="px-5 py-4">{{ str($order->status)->replace('_', ' ')->title() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-slate-500">Belum ada riwayat order.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
