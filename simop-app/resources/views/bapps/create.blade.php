@extends('layouts.app')

@section('title', 'Buat BAPP | SIMOP')
@section('page-title', 'Buat BAPP')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    <div>
        <a href="{{ route('bapps.index') }}"
           class="text-sm font-semibold text-blue-600 hover:text-blue-800">
            ← Kembali
        </a>

        <h2 class="mt-3 text-2xl font-bold text-slate-900">
            Buat BAPP
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Buat Berita Acara Penyelesaian Pekerjaan dari Produksi & Instalasi yang telah selesai 100%.
        </p>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
            <ul class="list-inside list-disc text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($productions->isEmpty())
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-6">
            <h3 class="font-semibold text-amber-800">
                Belum ada pekerjaan yang dapat dibuatkan BAPP
            </h3>

            <p class="mt-2 text-sm text-amber-700">
                Produksi & Instalasi harus berstatus Selesai dengan progress 100% dan belum memiliki BAPP.
            </p>

            <a href="{{ route('productions.index') }}"
               class="mt-4 inline-block text-sm font-semibold text-amber-800 hover:underline">
                Lihat Produksi & Instalasi
            </a>
        </div>
    @else
        <form method="POST"
              action="{{ route('bapps.store') }}"
              class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

            @csrf

            <div class="space-y-6">

                <div>
                    <label for="production_id"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Produksi & Instalasi
                    </label>

                    <select id="production_id"
                            name="production_id"
                            required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                        <option value="">
                            Pilih pekerjaan
                        </option>

                        @foreach ($productions as $production)
                            <option value="{{ $production->id }}"
                                @selected(old('production_id') == $production->id)>
                                {{ $production->production_number }}
                                - {{ $production->title }}
                                - {{ $production->planning->project->project_number ?? '-' }}
                            </option>
                        @endforeach
                    </select>

                    @error('production_id')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="completion_date"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Tanggal Penyelesaian Pekerjaan
                    </label>

                    <input type="date"
                           id="completion_date"
                           name="completion_date"
                           value="{{ old('completion_date') }}"
                           required
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                    @error('completion_date')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="location"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Lokasi
                    </label>

                    <input type="text"
                           id="location"
                           name="location"
                           value="{{ old('location') }}"
                           placeholder="Lokasi penyelesaian pekerjaan"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                    @error('location')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="work_result"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Hasil Pekerjaan
                    </label>

                    <textarea id="work_result"
                              name="work_result"
                              rows="5"
                              placeholder="Jelaskan hasil pekerjaan..."
                              class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('work_result') }}</textarea>

                    @error('work_result')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="notes"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Catatan
                    </label>

                    <textarea id="notes"
                              name="notes"
                              rows="4"
                              placeholder="Catatan tambahan..."
                              class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>

                    @error('notes')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <p class="text-sm text-blue-700">
                        BAPP akan dibuat dengan status
                        <span class="font-semibold">Draft</span>.
                        Setelah selesai diperiksa, BAPP dapat diajukan dan kemudian disetujui.
                    </p>
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-200 pt-5">
                    <a href="{{ route('bapps.index') }}"
                       class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Batal
                    </a>

                    <button type="submit"
                            class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                        Buat BAPP
                    </button>
                </div>

            </div>
        </form>
    @endif

</div>
@endsection