<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Rating & Ulasan
            </h2>

            <a href="{{ route('ratings.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-md text-gray-700 text-sm font-medium hover:bg-gray-300">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-gray-500">Nomor Project</p>
                        <p class="font-semibold text-gray-900">
                            {{ $rating->project?->project_number ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Judul Project</p>
                        <p class="font-semibold text-gray-900">
                            {{ $rating->project?->title ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Client</p>
                        <p class="font-semibold text-gray-900">
                            {{ $rating->project?->workEstimate?->order?->company_name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Diberikan Oleh</p>
                        <p class="font-semibold text-gray-900">
                            {{ $rating->client?->name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Rating</p>
                        <p class="font-semibold text-amber-600 text-lg">
                            {{ str_repeat('★', (int) $rating->rating) }}{{ str_repeat('☆', 5 - (int) $rating->rating) }}
                            ({{ $rating->rating }}/5)
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Tanggal Penilaian</p>
                        <p class="font-semibold text-gray-900">
                            {{ $rating->created_at ? $rating->created_at->format('d M Y H:i') : '-' }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-gray-500">Ulasan</p>
                        <p class="font-semibold text-gray-900 whitespace-pre-line">
                            {{ $rating->review }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
