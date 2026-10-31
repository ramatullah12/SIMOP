<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Produksi & Instalasi Lapangan
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Daftar pelaksanaan pengerjaan produksi dan instalasi lapangan.
                </p>
            </div>

            @if (auth()->user()?->role?->slug === 'admin')
                <a href="{{ route('productions.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md text-white text-sm font-medium hover:bg-blue-700">
                    + Buat Production
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

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">No. Production</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Judul</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Project & Event</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Petugas Lapangan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Progress</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($productions as $production)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $production->production_number }}</td>
                                    <td class="px-4 py-3">{{ $production->title }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900">{{ $production->planning?->project?->project_number ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $production->planning?->project?->workEstimate?->order?->event_name ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3">{{ $production->assignedUser?->name ?? '-' }}</td>
                                    <td class="px-4 py-3 font-semibold">{{ $production->progress }}%</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                            {{ $production->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ strtoupper(str_replace('_', ' ', $production->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('productions.show', $production) }}"
                                               class="text-blue-600 hover:underline font-medium">
                                                Detail
                                            </a>
                                            @if (auth()->user()?->role?->slug === 'petugas-lapangan')
                                                <a href="{{ route('field-documentations.create', ['project_id' => $production->planning?->project_id]) }}"
                                                   class="text-emerald-600 hover:underline text-xs font-semibold">
                                                    + Input Dokumentasi
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                        Belum ada data production.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $productions->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
