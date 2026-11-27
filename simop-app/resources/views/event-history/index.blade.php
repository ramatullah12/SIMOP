<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Riwayat Event</h2>
                <p class="text-sm text-slate-500 mt-0.5">
                    @if ($role === 'petugas-lapangan')
                        Daftar event yang pernah Anda tangani, mulai dari survei lapangan hingga pelaksanaan proyek.
                    @else
                        Arsip seluruh event yang pernah dikerjakan perusahaan, mulai dari survei hingga proyek selesai.
                    @endif
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Ringkasan --}}
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
            @php
                $cards = [
                    ['Total Event', $stats['total'], 'text-slate-900', 'Event yang pernah dikerjakan'],
                    ['Event Selesai', $stats['completed'], 'text-emerald-600', 'Proyek telah rampung'],
                    ['Survei Selesai', $stats['surveys_done'], 'text-blue-600', $role === 'petugas-lapangan' ? 'Survei yang Anda laksanakan' : 'Survei lapangan tuntas'],
                    ['Rata-rata Penilaian', $stats['avg_rating'] > 0 ? $stats['avg_rating'] . ' / 5' : '-', 'text-amber-500', 'Kepuasan pelanggan'],
                ];
            @endphp
            @foreach ($cards as [$label, $value, $color, $hint])
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ $label }}</span>
                    <div class="text-2xl font-extrabold mt-1.5 {{ $color }}">{{ $value }}</div>
                    <span class="text-xs text-slate-500">{{ $hint }}</span>
                </div>
            @endforeach
        </div>

        {{-- Filter --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <form method="GET" action="{{ route('event-history.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
                <div class="sm:col-span-5">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Cari Event</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Nama event, lokasi, pelanggan, atau no. pesanan..."
                           class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Tahun</label>
                    <select name="year" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Tahun</option>
                        @foreach ($years as $y)
                            <option value="{{ $y }}" @selected((string) $year === (string) $y)>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Status</label>
                    <select name="status" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="selesai" @selected($status === 'selesai')>Selesai</option>
                        <option value="berjalan" @selected($status === 'berjalan')>Masih Berjalan</option>
                    </select>
                </div>
                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-sm font-semibold transition">Saring</button>
                    @if ($search || $year || $status)
                        <a href="{{ route('event-history.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold transition">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Daftar Event --}}
        <div class="space-y-4">
            @forelse ($events as $event)
                @php
                    $survey = $event->survey;
                    $project = $event->project;
                    $production = $project?->planning?->production;
                    $isDone = $event->status === 'selesai' || $project?->status === 'completed';
                    $isCancelled = $project?->status === 'cancelled';
                    $projectLabels = [
                        'planning' => 'Perencanaan',
                        'ongoing' => 'Berjalan',
                        'in_progress' => 'Berjalan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ];
                    $progress = $isDone ? 100 : (int) ($project?->progress ?? 0);
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition overflow-hidden">
                    <div class="flex flex-col lg:flex-row">
                        {{-- Tanggal --}}
                        <div class="lg:w-36 shrink-0 flex lg:flex-col items-center justify-center gap-2 lg:gap-0 px-5 py-4 border-b lg:border-b-0 lg:border-r border-slate-100
                                    {{ $isDone ? 'bg-emerald-50' : ($isCancelled ? 'bg-rose-50' : 'bg-blue-50') }}">
                            @php $date = $event->event_date ?? $event->created_at; @endphp
                            <span class="text-3xl font-extrabold {{ $isDone ? 'text-emerald-700' : ($isCancelled ? 'text-rose-700' : 'text-blue-700') }}">{{ $date->format('d') }}</span>
                            <span class="text-xs font-bold uppercase text-slate-500">{{ $date->translatedFormat('M Y') }}</span>
                            <span class="lg:mt-2 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold
                                         {{ $isDone ? 'bg-emerald-600 text-white' : ($isCancelled ? 'bg-rose-600 text-white' : 'bg-blue-600 text-white') }}">
                                {{ $isDone ? 'SELESAI' : ($isCancelled ? 'DIBATALKAN' : 'BERJALAN') }}
                            </span>
                        </div>

                        {{-- Konten --}}
                        <div class="flex-1 p-5 space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                                <div>
                                    <span class="text-[11px] font-mono font-bold text-blue-600">{{ $event->order_number }}</span>
                                    <h3 class="text-base font-extrabold text-slate-900">{{ $event->event_name }}</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        {{ $event->company_name }} &bull; {{ $event->event_location }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('tracking.show', $event) }}"
                                       class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-bold transition">
                                        Lihat Alur Lengkap
                                    </a>
                                    @if ($survey && $survey->status === \App\Models\Survey::STATUS_DONE && ($role !== 'petugas-lapangan' || $survey->assigned_to === auth()->id()))
                                        <a href="{{ route('surveys.pdf.result', $survey) }}" target="_blank"
                                           class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-bold transition">
                                            PDF Hasil Survei
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                                {{-- Survei --}}
                                <div class="rounded-xl bg-slate-50 border border-slate-200 p-3">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Survei Lapangan</span>
                                    @if ($survey)
                                        <p class="font-bold text-slate-800 mt-1">{{ $survey->sps_number ?: 'Survei #' . $survey->id }}</p>
                                        <p class="text-slate-500">Petugas: <span class="font-semibold text-slate-700">{{ $survey->assignedUser?->name ?? '-' }}</span></p>
                                        <p class="text-slate-500">Selesai: <span class="font-semibold text-slate-700">{{ $survey->completed_at?->format('d M Y') ?? '-' }}</span></p>
                                        <span class="inline-block mt-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $survey->status_color }}">{{ $survey->status_label }}</span>
                                    @else
                                        <p class="text-slate-400 italic mt-1">Tidak ada data survei</p>
                                    @endif
                                </div>

                                {{-- Proyek --}}
                                <div class="rounded-xl bg-slate-50 border border-slate-200 p-3">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Proyek & Pelaksanaan</span>
                                    @if ($project)
                                        <p class="font-bold text-slate-800 mt-1">{{ $project->project_number ?? $project->title }}</p>
                                        <div class="mt-2">
                                            <div class="flex justify-between text-[10px] font-bold text-slate-500 mb-0.5">
                                                <span>{{ $projectLabels[$project->status] ?? ucfirst($project->status) }}</span>
                                                <span>{{ $progress }}%</span>
                                            </div>
                                            <div class="h-1.5 rounded-full bg-slate-200 overflow-hidden">
                                                <div class="h-full rounded-full {{ $isDone ? 'bg-emerald-500' : 'bg-blue-500' }}" style="width: {{ $progress }}%"></div>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-slate-400 italic mt-1">Proyek belum dibuat</p>
                                    @endif
                                </div>

                                {{-- Penilaian --}}
                                <div class="rounded-xl bg-slate-50 border border-slate-200 p-3">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Penilaian Pelanggan</span>
                                    @if ($project?->rating)
                                        <div class="flex items-center gap-0.5 mt-1 text-base">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <span class="{{ $i <= $project->rating->rating ? 'text-amber-400' : 'text-slate-300' }}">&#9733;</span>
                                            @endfor
                                            <span class="ml-1 text-xs font-bold text-slate-700">{{ $project->rating->rating }}/5</span>
                                        </div>
                                        @if ($project->rating->review)
                                            <p class="text-slate-500 italic mt-1 line-clamp-2">"{{ $project->rating->review }}"</p>
                                        @endif
                                    @else
                                        <p class="text-slate-400 italic mt-1">Belum ada penilaian</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-sm">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-slate-800 text-sm">Belum Ada Riwayat Event</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                        Event akan muncul di sini setelah survei lapangan selesai atau proyek mulai dikerjakan.
                    </p>
                </div>
            @endforelse
        </div>

        @if ($events->hasPages())
            <div>{{ $events->links() }}</div>
        @endif
    </div>
</x-app-layout>
