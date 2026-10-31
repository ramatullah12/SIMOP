<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ auth()->user()->role?->slug === 'client' ? 'Order & Pengajuan Survey Saya' : 'Manajemen Order & Pengajuan Survey' }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Daftar pengajuan event/pekerjaan beserta status alur survey dan pengerjaan.
                </p>
            </div>

            @if(auth()->user()->role?->slug === 'client')
                <a href="{{ route('orders.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    + Buat Order & Ajukan Survey
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">No. Order</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Perusahaan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Event & Lokasi</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal Event</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Status Survey (SPS)</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Status Order</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($orders as $order)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $order->order_number }}</td>
                                    <td class="px-4 py-3">{{ $order->company_name }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900">{{ $order->event_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $order->event_location }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $order->event_date ? $order->event_date->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($order->survey)
                                            <a href="{{ route('surveys.show', $order->survey) }}" class="text-blue-600 hover:underline font-medium text-xs">
                                                {{ $order->survey->sps_number ?: strtoupper(str_replace('_', ' ', $order->survey->status)) }}
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400">Belum ada</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            {{ strtoupper(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('orders.show', $order) }}"
                                               class="text-blue-600 hover:underline font-medium">
                                                Detail
                                            </a>
                                            <a href="{{ route('tracking.show', $order) }}"
                                               class="text-emerald-600 hover:underline font-semibold text-xs">
                                                Lacak Status
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                        Belum ada data order.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
