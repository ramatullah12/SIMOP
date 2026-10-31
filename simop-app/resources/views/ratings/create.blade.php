<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Beri Rating & Ulasan Pekerjaan
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if ($projects->isEmpty())
                    <div class="text-center py-6 space-y-3">
                        <p class="text-gray-600">Belum ada project selesai yang dapat diberikan rating saat ini.</p>
                        <a href="{{ route('ratings.index') }}" class="inline-flex px-4 py-2 bg-gray-200 rounded-md text-gray-700 text-sm font-medium">
                            Kembali
                        </a>
                    </div>
                @else
                    <form method="POST" action="{{ route('ratings.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Project yang Telah Selesai</label>
                            <select name="project_id" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Pilih Project --</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}"
                                        @selected(old('project_id', $selectedProject?->id) == $project->id)>
                                        {{ $project->project_number }} - {{ $project->title }} ({{ $project->workEstimate?->order?->event_name ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Rating (1 - 5 Bintang)</label>
                            <select name="rating" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="5" @selected(old('rating', 5) == 5)>★★★★★ (5 - Sangat Puas)</option>
                                <option value="4" @selected(old('rating') == 4)>★★★★☆ (4 - Puas)</option>
                                <option value="3" @selected(old('rating') == 3)>★★★☆☆ (3 - Cukup)</option>
                                <option value="2" @selected(old('rating') == 2)>★★☆☆☆ (2 - Kurang)</option>
                                <option value="1" @selected(old('rating') == 1)>★☆☆☆☆ (1 - Sangat Kurang)</option>
                            </select>
                            @error('rating')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ulasan Hasil Pekerjaan</label>
                            <textarea name="review" rows="4" required
                                      placeholder="Tuliskan pengalaman dan ulasan Anda terhadap hasil pengerjaan proyek..."
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('review') }}</textarea>
                            @error('review')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md text-white text-sm font-medium hover:bg-blue-700">
                                Kirim Rating & Ulasan
                            </button>
                            <a href="{{ route('ratings.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-md text-gray-700 text-sm font-medium hover:bg-gray-300">
                                Batal
                            </a>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
