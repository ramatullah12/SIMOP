<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Status Pekerjaan
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Pemantauan alur pengerjaan mulai dari tahap pengajuan survey hingga pekerjaan selesai.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6">
            @forelse ($orders as $order)
                @php
                    $summary = $order->tracking_summary;
                    $steps = $summary['steps'] ?? [];
                    $latestDoneLog = collect($summary['chronological_logs'] ?? [])->whereIn('state', ['done', 'active', 'rejected'])->last();
                @endphp

                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                        <div>
                            <span class="text-xs font-semibold text-blue-700 bg-blue-100 px-2.5 py-1 rounded">
                                {{ $order->order_number }}
                            </span>
                            <h3 class="font-semibold text-gray-900 text-base mt-1">
                                {{ $order->event_name }} - {{ $order->company_name }}
                            </h3>
                            <p class="text-xs text-gray-500">
                                Lokasi: {{ $order->event_location }}
                            </p>
                        </div>

                        <div>
                            <span class="inline-block text-xs font-semibold text-green-800 bg-green-100 px-3 py-1.5 rounded-full">
                                {{ $summary['current_headline'] ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                            @foreach ($steps as $step)
                                @php
                                    $isDone = $step['state'] === 'done';
                                    $isActive = $step['state'] === 'active';
                                @endphp
                                <div class="p-3 rounded-lg border text-center
                                    {{ $isDone ? 'border-green-300 bg-green-50' : ($isActive ? 'border-blue-400 bg-blue-50' : 'border-gray-200 bg-gray-50 opacity-60') }}">
                                    <div class="mx-auto w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold mb-1.5
                                        {{ $isDone ? 'bg-green-600 text-white' : ($isActive ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-700') }}">
                                        {{ $step['number'] }}
                                    </div>
                                    <p class="text-xs font-semibold text-gray-900">{{ $step['title'] }}</p>
                                    <p class="text-[11px] text-gray-500 mt-0.5">{{ $step['subtitle'] }}</p>
                                    @if ($step['time'])
                                        <p class="text-[10px] text-gray-600 mt-1">{{ $step['time'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-gray-100">
                            <div class="text-sm">
                                <p class="font-semibold text-gray-900">
                                    Status Terakhir: {{ $latestDoneLog['title'] ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-600 mt-0.5">
                                    {{ $latestDoneLog['description'] ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    Waktu: {{ $latestDoneLog['timestamp'] ?? '-' }} | Oleh: {{ $latestDoneLog['actor'] ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <a href="{{ route('tracking.show', $order) }}"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md text-white text-sm font-medium hover:bg-blue-700">
                                    Lihat Riwayat Lengkap
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg border border-gray-200 p-8 text-center text-gray-500">
                    Belum ada data pekerjaan untuk ditampilkan.
                </div>
            @endforelse

            <div>
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
