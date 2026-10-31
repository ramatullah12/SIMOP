<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Order — {{ $order->order_number }}
            </h2>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('tracking.show', $order) }}"
                   class="px-4 py-2 rounded-md bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700">
                    Lihat Status Pekerjaan
                </a>

                @if ($order->survey)
                    <a href="{{ route('surveys.show', $order->survey) }}"
                       class="px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                        Detail Survey / SPS
                    </a>
                @elseif (in_array(auth()->user()->role?->slug, ['admin', 'client']))
                    <a href="{{ route('surveys.create', ['order_id' => $order->id]) }}"
                       class="px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                        + Ajukan Survey
                    </a>
                @endif

                <a href="{{ route('orders.index') }}"
                   class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 text-sm">
                    Kembali
                </a>

                @if(auth()->user()->role?->slug === 'admin' || (auth()->user()->role?->slug === 'client' && in_array($order->status, ['masuk', 'menunggu_survey', 'ditolak'])))
                    <a href="{{ route('orders.edit', $order) }}"
                       class="px-4 py-2 rounded-md bg-blue-600 text-white text-sm hover:bg-blue-700">
                        Edit
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-gray-500">Nomor Order</p>
                        <p class="font-semibold text-gray-900 mt-1">{{ $order->order_number }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Status</p>
                        <p class="font-semibold text-gray-900 mt-1">{{ strtoupper(str_replace('_', ' ', $order->status)) }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Nama Perusahaan</p>
                        <p class="font-semibold text-gray-900 mt-1">{{ $order->company_name }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Contact Person</p>
                        <p class="font-semibold text-gray-900 mt-1">{{ $order->contact_person }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Telepon</p>
                        <p class="font-semibold text-gray-900 mt-1">{{ $order->phone }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Email</p>
                        <p class="font-semibold text-gray-900 mt-1">{{ $order->email ?: '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Nama Event</p>
                        <p class="font-semibold text-gray-900 mt-1">{{ $order->event_name }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Lokasi Event</p>
                        <p class="font-semibold text-gray-900 mt-1">{{ $order->event_location }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Tanggal Event</p>
                        <p class="font-semibold text-gray-900 mt-1">{{ $order->event_date ? $order->event_date->format('d M Y') : '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Status Survey & SPS</p>
                        <p class="font-semibold text-blue-700 mt-1">
                            @if ($order->survey)
                                {{ strtoupper(str_replace('_', ' ', $order->survey->status)) }}
                                {{ $order->survey->sps_number ? '(' . $order->survey->sps_number . ')' : '' }}
                            @else
                                Belum ada pengajuan survey
                            @endif
                        </p>
                    </div>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Deskripsi Kebutuhan Pekerjaan</p>
                    <p class="mt-2 text-gray-900 whitespace-pre-line">{{ $order->description }}</p>
                </div>

                @if($order->admin_notes)
                    <div>
                        <p class="text-sm text-gray-500">Catatan Admin</p>
                        <p class="mt-2 text-gray-900 whitespace-pre-line">{{ $order->admin_notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
