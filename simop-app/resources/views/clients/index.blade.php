
@extends('layouts.app')

@section('title', 'Manajemen Client | SIMOP')
@section('page-title', 'Manajemen Client')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Manajemen Client</h1>
            <p class="mt-2 text-sm text-slate-500">Kelola data perusahaan dan akun Client SIMOP.</p>
        </div>
        <a href="{{ route('admin.clients.create') }}"
           class="rounded-lg bg-blue-600 px-5 py-3 text-center text-sm font-semibold text-white hover:bg-blue-700">
            + Tambah Client
        </a>
    </div>

    @if (session('success'))
        <div class="rounded-lg bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.clients.index') }}" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari perusahaan, kontak, atau email..."
               class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
        <button class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-semibold text-white">Cari</button>
    </form>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-4">Perusahaan</th>
                        <th class="px-5 py-4">Contact Person</th>
                        <th class="px-5 py-4">Email</th>
                        <th class="px-5 py-4">Telepon</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($clients as $client)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-semibold">{{ $client->company_name }}</td>
                            <td class="px-5 py-4">{{ $client->contact_person }}</td>
                            <td class="px-5 py-4">{{ $client->email }}</td>
                            <td class="px-5 py-4">{{ $client->phone ?: '-' }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $client->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($client->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.clients.show', $client) }}"
                                   class="font-semibold text-blue-600 hover:text-blue-800">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-500">
                                Belum ada data Client. Klik Tambah Client untuk membuat data pertama.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4">{{ $clients->links() }}</div>
    </div>
</div>
@endsection
