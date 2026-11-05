<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight sm:text-2xl">
                    {{ $role === 'client' ? 'Daftar Pesanan Saya' : 'Manajemen Seluruh Pesanan Pelanggan' }}
                </h2>
                <p class="text-xs text-slate-500 mt-1 sm:text-sm">
                    @if ($role === 'client')
                        Kelola data pesanan kegiatan dan pantau kemajuan survei lokasi serta proyek Anda.
                    @else
                        Daftar seluruh pesanan kegiatan dari semua pelanggan, lengkap dengan status survei dan pelaksanaan.
                    @endif
                </p>
            </div>

            @if (in_array($role, ['admin', 'client'], true))
                <div>
                    <a href="{{ route('orders.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 shadow-md hover:shadow-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        + Buat Pesanan Baru
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 shadow-sm flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="flex-1 text-sm text-emerald-800">
                        <p class="font-bold">Berhasil</p>
                        <p class="mt-0.5 text-xs text-emerald-700">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            {{-- Filter & Search Bar --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
                <form method="GET" action="{{ route('orders.index') }}" class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-2.5 flex-1 min-w-[280px]">
                        {{-- Search Input --}}
                        <div class="relative flex-1 min-w-[200px]">
                            <input type="text" name="search" value="{{ $search }}"
                                   placeholder="Cari no. pesanan, perusahaan, kegiatan..."
                                   class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border-slate-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm text-slate-800">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Status Filter --}}
                        <select name="status" onchange="this.form.submit()"
                                class="rounded-xl border-slate-300 text-xs font-semibold py-2 px-3 focus:ring-blue-500 focus:border-blue-500 shadow-sm text-slate-700">
                            <option value="">Semua Status Pesanan</option>
                            @foreach ($statuses as $k => $v)
                                <option value="{{ $k }}" @selected($statusFilter === $k)>{{ $v }}</option>
                            @endforeach
                        </select>

                        {{-- Client Filter (Khusus Admin/Direktur) --}}
                        @if ($role !== 'client' && $clients->isNotEmpty())
                            <select name="client_id" onchange="this.form.submit()"
                                    class="rounded-xl border-slate-300 text-xs font-semibold py-2 px-3 focus:ring-blue-500 focus:border-blue-500 shadow-sm text-slate-700 max-w-[220px]">
                                <option value="">Semua Pelanggan</option>
                                @foreach ($clients as $c)
                                    <option value="{{ $c->id }}" @selected((string)$clientIdFilter === (string)$c->id)>
                                        {{ $c->name }} ({{ $c->email }})
                                    </option>
                                @endforeach
                            </select>
                        @endif

                        <button type="submit"
                                class="px-3.5 py-2 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition">
                            Cari
                        </button>

                        @if ($search || $statusFilter || $clientIdFilter)
                            <a href="{{ route('orders.index') }}"
                               class="text-xs text-rose-600 hover:text-rose-800 font-semibold underline">
                                Reset
                            </a>
                        @endif
                    </div>

                    <div class="text-xs text-slate-500">
                        Total: <strong class="text-slate-800">{{ $orders->total() }}</strong> pesanan
                    </div>
                </form>
            </div>

            {{-- Tabel Data Pesanan --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">No. Pesanan</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Pelanggan & PIC</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kegiatan & Lokasi</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tgl Kegiatan</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status Survei (SPS)</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status Pesanan</th>
                                <th class="px-5 py-3.5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    {{-- No. Pesanan --}}
                                    <td class="px-5 py-4 align-top">
                                        <div class="font-bold font-mono text-slate-900 text-xs tracking-wide">
                                            {{ $order->order_number }}
                                        </div>
                                        <span class="text-[11px] text-slate-400 block mt-0.5">
                                            {{ $order->created_at->format('d/m/Y, H:i') }}
                                        </span>
                                    </td>

                                    {{-- Pelanggan & PIC --}}
                                    <td class="px-5 py-4 align-top">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-800 font-extrabold text-xs flex items-center justify-center flex-shrink-0">
                                                {{ strtoupper(substr($order->company_name ?: ($order->client?->name ?? 'P'), 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 text-sm">{{ $order->company_name }}</p>
                                                <p class="text-xs text-slate-500 mt-0.5">
                                                    PIC: <span class="font-medium text-slate-700">{{ $order->contact_person }}</span>
                                                    @if ($order->phone)
                                                        &bull; {{ $order->phone }}
                                                    @endif
                                                </p>
                                                @if ($role !== 'client' && $order->client)
                                                    <span class="inline-block mt-0.5 text-[10px] text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200">
                                                        Akun: {{ $order->client->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kegiatan & Lokasi --}}
                                    <td class="px-5 py-4 align-top max-w-xs">
                                        <p class="font-bold text-slate-800 text-xs">{{ $order->event_name }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5 line-clamp-1" title="{{ $order->event_location }}">
                                            📍 {{ $order->event_location }}
                                        </p>
                                    </td>

                                    {{-- Tanggal Kegiatan --}}
                                    <td class="px-5 py-4 align-top text-xs text-slate-700">
                                        {{ $order->event_date ? $order->event_date->format('d M Y') : '-' }}
                                    </td>

                                    {{-- Status Survei (SPS) --}}
                                    <td class="px-5 py-4 align-top">
                                        @if ($order->survey)
                                            <a href="{{ route('surveys.show', $order->survey) }}"
                                               class="inline-flex flex-col text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                                <span>{{ $order->survey->sps_number ?: 'Survei #' . $order->survey->id }}</span>
                                                <span class="text-[11px] font-normal text-slate-500">
                                                    {{ $order->survey->status_label }}
                                                </span>
                                            </a>
                                        @else
                                            <div class="space-y-1">
                                                <span class="text-xs text-slate-400 italic block">Belum ada survei</span>
                                                @if ($role === 'admin')
                                                    <a href="{{ route('surveys.create', ['order_id' => $order->id]) }}"
                                                       class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:text-blue-800 font-bold bg-blue-50 hover:bg-blue-100 px-2 py-0.5 rounded-lg border border-blue-200 transition">
                                                        + Buat Survei
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Status Pesanan --}}
                                    <td class="px-5 py-4 align-top">
                                        @php
                                            $statusColors = [
                                                'masuk' => 'bg-blue-100 text-blue-800',
                                                'diproses' => 'bg-amber-100 text-amber-800',
                                                'menunggu_survey' => 'bg-indigo-100 text-indigo-800',
                                                'survey_selesai' => 'bg-teal-100 text-teal-800',
                                                'disetujui' => 'bg-emerald-100 text-emerald-800',
                                                'ditolak' => 'bg-rose-100 text-rose-800',
                                                'selesai' => 'bg-green-100 text-green-900',
                                            ];
                                            $badgeColor = $statusColors[$order->status] ?? 'bg-slate-100 text-slate-700';
                                        @endphp
                                        <div class="space-y-1">
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $badgeColor }}">
                                                {{ strtoupper(str_replace('_', ' ', $order->status)) }}
                                            </span>
                                            @if ($role === 'admin' && $order->status === 'masuk')
                                                <span class="block text-[10px] font-semibold text-blue-700">
                                                    âš¡ Perlu Verifikasi
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-5 py-4 align-top text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            @if ($role === 'admin' && $order->status === 'masuk')
                                                <a href="{{ route('orders.show', $order) }}"
                                                   class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 shadow-sm transition inline-flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Verifikasi
                                                </a>
                                            @else
                                                <a href="{{ route('orders.show', $order) }}"
                                                   class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg text-xs font-semibold hover:bg-slate-200 transition">
                                                    Rincian
                                                </a>
                                            @endif

                                            <a href="{{ route('tracking.show', $order) }}"
                                               class="px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-bold hover:bg-emerald-100 transition">
                                                Lacak
                                            </a>

                                            @if ($role === 'admin' || ($role === 'client' && in_array($order->status, ['masuk', 'menunggu_survey', 'ditolak'])))
                                                <a href="{{ route('orders.edit', $order) }}"
                                                   class="px-3 py-1.5 bg-slate-800 text-white rounded-lg text-xs font-bold hover:bg-slate-900 shadow-sm transition">
                                                    Ubah
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-12 text-center">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <h4 class="font-bold text-slate-800 text-sm">Tidak Ada Data Pesanan</h4>
                                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                            Belum ada data pesanan yang tercatat sesuai kriteria pencarian atau filter yang dipilih.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($orders->hasPages())
                    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
