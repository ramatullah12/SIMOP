<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Penilaian & Ulasan Pekerjaan
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Penilaian dan ulasan Klien terhadap proyek/kegiatan yang telah selesai.
                </p>
            </div>

            @if (auth()->user()?->role?->slug === 'client')
                <a href="{{ route('ratings.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md text-white text-sm font-medium hover:bg-blue-700">
                    + Beri Penilaian Pekerjaan
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
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Proyek & Kegiatan</th>
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
        </div>
    </div>
</x-app-layout>
