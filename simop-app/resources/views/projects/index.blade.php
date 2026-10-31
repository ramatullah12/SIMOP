<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Daftar Project Operasional
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola project, pemantauan progress, dan alokasi stok barang proyek.
                </p>
            </div>

            @if (auth()->user()?->role?->slug === 'admin')
                <a href="{{ route('projects.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md text-white text-sm font-medium hover:bg-blue-700">
                    + Buat Project
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
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">No. Project</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Judul & Event</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Client</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Petugas Lapangan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Stok Barang Dibawa</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Progress & Status</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($projects as $project)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $project->project_number }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900">{{ $project->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $project->workEstimate?->order?->event_name ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3">{{ $project->workEstimate?->order?->company_name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $project->assignedUser?->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-xs">
                                        @if ($project->projectItems->isNotEmpty())
                                            @foreach ($project->projectItems as $pi)
                                                <div>
                                                    {{ $pi->item?->name }}: <strong>{{ $pi->quantity }} {{ $pi->item?->unit }}</strong>
                                                    <span class="{{ $pi->status === 'returned' ? 'text-emerald-600 font-semibold' : 'text-amber-600 font-semibold' }}">
                                                        ({{ $pi->status === 'returned' ? 'Kembali' : 'Dibawa' }})
                                                    </span>
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-gray-400">Belum ada</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                            {{ $project->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ strtoupper(str_replace('_', ' ', $project->status)) }} ({{ $project->progress }}%)
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('projects.show', $project) }}"
                                               class="text-blue-600 hover:underline font-medium">
                                                Detail
                                            </a>
                                            @if ($project->workEstimate?->order)
                                                <a href="{{ route('tracking.show', $project->workEstimate->order) }}"
                                                   class="text-emerald-600 hover:underline text-xs font-semibold">
                                                    Lacak Alur
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                        Belum ada data project.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
