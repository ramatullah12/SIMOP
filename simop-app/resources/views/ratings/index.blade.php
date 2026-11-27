<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Penilaian &amp; Ulasan Pekerjaan
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    @if ($role === 'client')
                        Daftar otomatis dari pesanan Anda. Berikan penilaian langsung pada pesanan yang proyeknya telah selesai.
                    @else
                        Penilaian dan ulasan Klien terhadap proyek/kegiatan yang telah selesai.
                    @endif
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">
            @if (session('success'))
                <div class="rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3 text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3 text-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($role === 'client')
                {{-- Tampilan Otomatis Berdasarkan Pesanan Client --}}
                <div class="space-y-4">
                    @forelse ($clientOrders as $order)
                        @php
                            $project = $order->project;
                            $rating = $project?->rating;
                            $canRate = $project && $project->status === 'completed' && ! $rating;
                        @endphp
                        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-gray-100 pb-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-mono font-bold text-blue-600">{{ $order->order_number }}</span>
                                        @if ($project)
                                            <span class="text-xs text-gray-400">&bull;</span>
                                            <span class="text-xs font-mono text-gray-500">{{ $project->project_number }}</span>
                                        @endif
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900 mt-0.5">{{ $order->event_name }}</h3>
                                    <p class="text-xs text-gray-500">
                                        {{ $order->event_location }}
                                        @if ($order->event_date)
                                            &bull; {{ $order->event_date->translatedFormat('d F Y') }}
                                        @endif
                                    </p>
                                </div>

                                <div class="shrink-0">
                                    @if ($rating)
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            Sudah Dinilai ({{ $rating->rating }}/5)
                                        </span>
                                    @elseif ($canRate)
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                            Siap Diberi Penilaian
                                        </span>
                                    @else
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                            {{ $project ? $project->status_label : strtoupper(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if ($rating)
                                {{-- Sudah Dinilai --}}
                                <div class="rounded-lg bg-emerald-50/70 border border-emerald-200 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div class="space-y-1">
                                        <div class="text-amber-500 text-lg font-bold tracking-wide">
                                            {{ str_repeat('★', (int) $rating->rating) }}{{ str_repeat('☆', 5 - (int) $rating->rating) }}
                                            <span class="text-xs text-gray-600 font-semibold ml-1">({{ $rating->rating }}/5)</span>
                                        </div>
                                        <p class="text-sm text-gray-800">{{ $rating->review }}</p>
                                        <p class="text-xs text-gray-500">Dinilai pada {{ $rating->created_at?->translatedFormat('d F Y, H:i') }} WIB</p>
                                    </div>
                                    <a href="{{ route('ratings.show', $rating) }}"
                                       class="px-3 py-1.5 bg-white border border-gray-200 rounded-md text-xs font-semibold text-blue-600 hover:bg-gray-50 shrink-0">
                                        Lihat Detail
                                    </a>
                                </div>
                            @elseif ($canRate)
                                {{-- Form Penilaian Langsung Pada Pesanan --}}
                                <form method="POST" action="{{ route('ratings.store') }}" class="space-y-3 bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    @csrf
                                    <input type="hidden" name="project_id" value="{{ $project->id }}">

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">Nilai Kepuasan (1–5)</label>
                                            <select name="rating" required class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                                <option value="5" selected>★★★★★ (5 - Sangat Puas)</option>
                                                <option value="4">★★★★☆ (4 - Puas)</option>
                                                <option value="3">★★★☆☆ (3 - Cukup)</option>
                                                <option value="2">★★☆☆☆ (2 - Kurang)</option>
                                                <option value="1">★☆☆☆☆ (1 - Sangat Kurang)</option>
                                            </select>
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">Ulasan Pekerjaan</label>
                                            <textarea name="review" rows="2" required
                                                      placeholder="Tulis pengalaman dan ulasan Anda terhadap pelaksanaan kegiatan ini..."
                                                      class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                        </div>
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit"
                                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                            Kirim Penilaian
                                        </button>
                                    </div>
                                </form>
                            @else
                                <p class="text-xs text-gray-500 italic">
                                    Penilaian dapat diisi langsung di sini setelah proyek untuk pesanan ini berstatus <strong>Selesai</strong>.
                                </p>
                            @endif
                        </div>
                    @empty
                        <div class="bg-white rounded-xl border border-gray-200 p-10 text-center text-gray-500 text-sm">
                            Belum ada pesanan yang Anda buat. Pesanan Anda akan otomatis muncul di halaman ini untuk diberi penilaian setelah proyek selesai.
                        </div>
                    @endforelse
                </div>
            @else
                {{-- Tampilan Tabel Penilaian untuk Admin & Direktur --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Proyek &amp; Kegiatan</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Klien</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Penilaian</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Ulasan</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($ratings as $rating)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <p class="font-medium text-gray-900">{{ $rating->project?->project_number }}</p>
                                            <p class="text-xs text-gray-500">{{ $rating->project?->order?->event_name ?? $rating->project?->title }}</p>
                                        </td>
                                        <td class="px-4 py-3">{{ $rating->client?->name ?? '-' }}</td>
                                        <td class="px-4 py-3 font-semibold text-amber-600">
                                            {{ str_repeat('★', (int) $rating->rating) }}{{ str_repeat('☆', 5 - (int) $rating->rating) }} ({{ $rating->rating }}/5)
                                        </td>
                                        <td class="px-4 py-3">{{ \Illuminate\Support\Str::limit($rating->review, 80) }}</td>
                                        <td class="px-4 py-3">{{ $rating->created_at?->format('d M Y') }}</td>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('ratings.show', $rating) }}"
                                               class="text-blue-600 hover:underline font-medium">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                            Belum ada data penilaian dan ulasan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $ratings->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
