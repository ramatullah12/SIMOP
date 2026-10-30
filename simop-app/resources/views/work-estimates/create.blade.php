@extends('layouts.app')

@section('title', 'Buat Work Estimate | SIMOP')
@section('page-title', 'Buat Work Estimate')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <a
                href="{{ route('admin.work-estimates.index') }}"
                class="text-sm font-medium text-blue-600 hover:text-blue-800"
            >
                &larr; Kembali ke Daftar Work Estimate
            </a>

            <h2 class="mt-3 text-2xl font-bold text-slate-900">
                Buat Work Estimate
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Buat estimasi biaya berdasarkan Survey yang telah selesai.
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold">
                    Terdapat data yang perlu diperbaiki:
                </p>

                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($surveys->isEmpty())
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                <div class="flex gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 font-bold text-amber-700">
                        !
                    </div>

                    <div>
                        <p class="font-semibold text-amber-800">
                            Belum Ada Survey yang Dapat Diproses
                        </p>

                        <p class="mt-1 text-sm leading-6 text-amber-700">
                            Work Estimate hanya dapat dibuat dari Survey yang sudah selesai dan belum memiliki Work Estimate.
                        </p>

                        <a
                            href="{{ route('admin.surveys.index') }}"
                            class="mt-3 inline-flex text-sm font-semibold text-amber-800 underline hover:text-amber-900"
                        >
                            Lihat Daftar Survey
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <form
            action="{{ route('admin.work-estimates.store') }}"
            method="POST"
            class="space-y-6"
        >
            @csrf

            <div class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">
                        Informasi Pekerjaan
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Pilih Order dan Survey yang menjadi dasar pembuatan Work Estimate.
                    </p>
                </div>

                <div>
                    <label
                        for="order_id"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Order & RFQ <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="order_id"
                        name="order_id"
                        required
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                        <option value="">
                            -- Pilih Order --
                        </option>

                        @foreach ($orders as $order)
                            <option
                                value="{{ $order->id }}"
                                @selected(old('order_id') == $order->id)
                            >
                                {{ $order->order_number }} — {{ $order->company_name }}
                            </option>
                        @endforeach
                    </select>

                    @if ($orders->isEmpty())
                        <p class="mt-2 text-sm font-medium text-amber-600">
                            Belum ada Order dengan Survey selesai yang dapat diproses.
                        </p>
                    @endif
                </div>

                <div>
                    <label
                        for="survey_id"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Survey <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="survey_id"
                        name="survey_id"
                        required
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                        <option value="">
                            -- Pilih Order terlebih dahulu --
                        </option>

                        @foreach ($surveys as $survey)
                            <option
                                value="{{ $survey->id }}"
                                data-order-id="{{ $survey->order_id }}"
                                @selected(old('survey_id') == $survey->id)
                            >
                                {{ $survey->order?->order_number ?? 'Order #'.$survey->order_id }}
                                — {{ $survey->location }}
                                @if ($survey->scheduled_at)
                                    — {{ $survey->scheduled_at->format('d/m/Y') }}
                                @endif
                            </option>
                        @endforeach
                    </select>

                    <p class="mt-2 text-xs text-slate-500">
                        Hanya Survey berstatus Selesai dan belum memiliki Work Estimate yang dapat dipilih.
                    </p>
                </div>

                <div
                    id="survey-info"
                    class="hidden rounded-xl border border-green-200 bg-green-50 p-4"
                >
                    <p class="text-sm font-semibold text-green-800">
                        Survey Selesai
                    </p>

                    <div class="mt-3 grid gap-3 text-sm sm:grid-cols-2">
                        <div>
                            <span class="text-green-700">
                                Lokasi:
                            </span>

                            <p
                                id="survey-location"
                                class="mt-1 font-semibold text-green-900"
                            >
                                -
                            </p>
                        </div>

                        <div>
                            <span class="text-green-700">
                                Jadwal:
                            </span>

                            <p
                                id="survey-date"
                                class="mt-1 font-semibold text-green-900"
                            >
                                -
                            </p>
                        </div>

                        <div>
                            <span class="text-green-700">
                                Petugas:
                            </span>

                            <p
                                id="survey-officer"
                                class="mt-1 font-semibold text-green-900"
                            >
                                -
                            </p>
                        </div>

                        <div>
                            <span class="text-green-700">
                                Status:
                            </span>

                            <p class="mt-1 font-semibold text-green-900">
                                Selesai
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <label
                        for="title"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Judul Work Estimate <span class="text-red-500">*</span>
                    </label>

                    <input
                        id="title"
                        name="title"
                        type="text"
                        required
                        value="{{ old('title') }}"
                        placeholder="Contoh: Work Estimate Instalasi Peralatan"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                </div>

                <div>
                    <label
                        for="description"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Deskripsi Pekerjaan
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Jelaskan ruang lingkup pekerjaan..."
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >{{ old('description') }}</textarea>
                </div>

                <div>
                    <label
                        for="tax_rate"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Pajak (%) <span class="text-red-500">*</span>
                    </label>

                    <input
                        id="tax_rate"
                        name="tax_rate"
                        type="number"
                        min="0"
                        max="100"
                        step="0.01"
                        required
                        value="{{ old('tax_rate', '0') }}"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >

                    <p class="mt-2 text-xs text-slate-500">
                        Isi sesuai ketentuan pajak yang berlaku. Gunakan 0 jika tidak diterapkan.
                    </p>
                </div>
            </div>

            <div class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Rincian Item Pekerjaan
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Tambahkan item pekerjaan, kuantitas, satuan, dan harga satuan.
                        </p>
                    </div>

                    <button
                        type="button"
                        id="add-item"
                        class="rounded-lg border border-blue-600 px-4 py-2 text-sm font-semibold text-blue-600 transition hover:bg-blue-50"
                    >
                        + Tambah Item
                    </button>
                </div>

                <div id="items-container" class="space-y-5">
                    @foreach (
                        old(
                            'items',
                            [
                                [
                                    'item_name' => '',
                                    'description' => '',
                                    'quantity' => '1',
                                    'unit' => 'unit',
                                    'unit_price' => '0',
                                ]
                            ]
                        ) as $i => $item
                    )
                        <div class="estimate-item rounded-lg border border-slate-200 p-4">
                            <div class="mb-4 flex items-center justify-between">
                                <h4 class="item-title font-semibold text-slate-800">
                                    Item {{ $i + 1 }}
                                </h4>

                                <button
                                    type="button"
                                    class="remove-item text-sm font-medium text-red-600 hover:text-red-800"
                                >
                                    Hapus
                                </button>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-slate-700">
                                        Nama Item <span class="text-red-500">*</span>
                                    </label>

                                    <input
                                        name="items[{{ $i }}][item_name]"
                                        required
                                        value="{{ $item['item_name'] ?? '' }}"
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        placeholder="Contoh: Material instalasi"
                                    >
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-slate-700">
                                        Deskripsi
                                    </label>

                                    <input
                                        name="items[{{ $i }}][description]"
                                        value="{{ $item['description'] ?? '' }}"
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        placeholder="Keterangan item"
                                    >
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-slate-700">
                                        Kuantitas <span class="text-red-500">*</span>
                                    </label>

                                    <input
                                        type="number"
                                        min="0.01"
                                        step="0.01"
                                        name="items[{{ $i }}][quantity]"
                                        required
                                        value="{{ $item['quantity'] ?? '1' }}"
                                        class="item-quantity w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                    >
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-slate-700">
                                        Satuan <span class="text-red-500">*</span>
                                    </label>

                                    <input
                                        name="items[{{ $i }}][unit]"
                                        required
                                        value="{{ $item['unit'] ?? 'unit' }}"
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        placeholder="unit, meter, hari"
                                    >
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-slate-700">
                                        Harga Satuan (Rp) <span class="text-red-500">*</span>
                                    </label>

                                    <input
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        name="items[{{ $i }}][unit_price]"
                                        required
                                        value="{{ $item['unit_price'] ?? '0' }}"
                                        class="item-price w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                    >
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-slate-700">
                                        Total Item
                                    </label>

                                    <div class="item-total rounded-lg bg-slate-50 px-3 py-2 font-semibold text-slate-800">
                                        Rp0
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="rounded-lg bg-slate-50 p-5">
                    <div class="flex justify-between py-2 text-sm">
                        <span class="text-slate-600">
                            Subtotal
                        </span>

                        <strong id="subtotal">
                            Rp0
                        </strong>
                    </div>

                    <div class="flex justify-between py-2 text-sm">
                        <span class="text-slate-600">
                            Pajak
                        </span>

                        <strong id="tax-amount">
                            Rp0
                        </strong>
                    </div>

                    <div class="mt-2 flex justify-between border-t border-slate-200 pt-4">
                        <span class="font-bold text-slate-900">
                            Total Estimasi
                        </span>

                        <strong
                            id="grand-total"
                            class="text-xl text-blue-700"
                        >
                            Rp0
                        </strong>
                    </div>

                    <p class="mt-2 text-xs text-slate-500">
                        Perhitungan ini merupakan pratinjau. Total akhir akan dihitung ulang oleh server.
                    </p>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <label
                    for="notes"
                    class="mb-2 block text-sm font-semibold text-slate-700"
                >
                    Catatan Tambahan
                </label>

                <textarea
                    id="notes"
                    name="notes"
                    rows="4"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    placeholder="Catatan untuk Work Estimate ini..."
                >{{ old('notes') }}</textarea>
            </div>

            <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">
                <a
                    href="{{ route('admin.work-estimates.index') }}"
                    class="rounded-lg border border-slate-300 px-5 py-3 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    @disabled($surveys->isEmpty())
                    class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-slate-300"
                >
                    Simpan Work Estimate
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('items-container');
            const addButton = document.getElementById('add-item');
            const taxInput = document.getElementById('tax_rate');
            const orderSelect = document.getElementById('order_id');
            const surveySelect = document.getElementById('survey_id');
            const surveyInfo = document.getElementById('survey-info');
            const surveyLocation = document.getElementById('survey-location');
            const surveyDate = document.getElementById('survey-date');
            const surveyOfficer = document.getElementById('survey-officer');

            const surveys = @json(
                $surveys->map(fn ($survey) => [
                    'id' => $survey->id,
                    'order_id' => $survey->order_id,
                    'label' => ($survey->order?->order_number ?? 'Order #'.$survey->order_id)
                        .' — '.$survey->location
                        .($survey->scheduled_at ? ' — '.$survey->scheduled_at->format('d/m/Y') : ''),
                    'location' => $survey->location,
                    'date' => $survey->scheduled_at?->format('d/m/Y H:i') ?? 'Belum dijadwalkan',
                    'officer' => $survey->assignedUser?->name ?? 'Belum ditugaskan',
                ])->values()
            );

            const oldSurveyId = @json((string) old('survey_id', ''));

            const rupiah = value => {
                return 'Rp' + new Intl.NumberFormat('id-ID', {
                    maximumFractionDigits: 2
                }).format(value);
            };

            function updateSurveyInfo() {
                const selectedSurvey = surveys.find(
                    survey => String(survey.id) === String(surveySelect.value)
                );

                if (!selectedSurvey) {
                    surveyInfo.classList.add('hidden');
                    surveyLocation.textContent = '-';
                    surveyDate.textContent = '-';
                    surveyOfficer.textContent = '-';
                    return;
                }

                surveyLocation.textContent = selectedSurvey.location;
                surveyDate.textContent = selectedSurvey.date;
                surveyOfficer.textContent = selectedSurvey.officer;

                surveyInfo.classList.remove('hidden');
            }

            function filterSurveys() {
                const orderId = String(orderSelect.value);
                const currentSurveyId = String(surveySelect.value);

                surveySelect.innerHTML = '';

                const placeholder = document.createElement('option');
                placeholder.value = '';

                if (!orderId) {
                    placeholder.textContent = '-- Pilih Order terlebih dahulu --';
                    surveySelect.appendChild(placeholder);
                    surveySelect.disabled = true;
                    surveyInfo.classList.add('hidden');
                    return;
                }

                const availableSurveys = surveys.filter(
                    survey => String(survey.order_id) === orderId
                );

                placeholder.textContent = availableSurveys.length
                    ? '-- Pilih Survey --'
                    : '-- Tidak ada Survey tersedia --';

                surveySelect.appendChild(placeholder);

                availableSurveys.forEach(survey => {
                    const option = document.createElement('option');

                    option.value = survey.id;
                    option.textContent = survey.label;

                    if (
                        String(survey.id) === currentSurveyId
                        || String(survey.id) === oldSurveyId
                    ) {
                        option.selected = true;
                    }

                    surveySelect.appendChild(option);
                });

                surveySelect.disabled = availableSurveys.length === 0;

                updateSurveyInfo();
            }

            function renumberItems() {
                container.querySelectorAll('.estimate-item').forEach((card, index) => {
                    card.querySelector('.item-title').textContent = 'Item ' + (index + 1);

                    card.querySelectorAll('[name]').forEach(input => {
                        input.name = input.name.replace(
                            /items\[\d+\]/,
                            'items[' + index + ']'
                        );
                    });
                });
            }

            function calculateTotals() {
                let subtotal = 0;

                container.querySelectorAll('.estimate-item').forEach(card => {
                    const quantityInput = card.querySelector('.item-quantity');
                    const priceInput = card.querySelector('.item-price');
                    const totalElement = card.querySelector('.item-total');

                    const quantity = parseFloat(quantityInput.value) || 0;
                    const price = parseFloat(priceInput.value) || 0;
                    const total = quantity * price;

                    totalElement.textContent = rupiah(total);
                    subtotal += total;
                });

                const taxRate = parseFloat(taxInput.value) || 0;
                const tax = subtotal * taxRate / 100;

                document.getElementById('subtotal').textContent = rupiah(subtotal);
                document.getElementById('tax-amount').textContent = rupiah(tax);
                document.getElementById('grand-total').textContent = rupiah(
                    subtotal + tax
                );
            }

            addButton.addEventListener('click', function () {
                const index = container.querySelectorAll('.estimate-item').length;
                const card = document.createElement('div');

                card.className = 'estimate-item rounded-lg border border-slate-200 p-4';

                card.innerHTML = `
                    <div class="mb-4 flex items-center justify-between">
                        <h4 class="item-title font-semibold text-slate-800">
                            Item ${index + 1}
                        </h4>

                        <button
                            type="button"
                            class="remove-item text-sm font-medium text-red-600 hover:text-red-800"
                        >
                            Hapus
                        </button>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">
                                Nama Item <span class="text-red-500">*</span>
                            </label>

                            <input
                                name="items[${index}][item_name]"
                                required
                                class="w-full rounded-lg border border-slate-300 px-3 py-2"
                                placeholder="Contoh: Material instalasi"
                            >
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">
                                Deskripsi
                            </label>

                            <input
                                name="items[${index}][description]"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2"
                                placeholder="Keterangan item"
                            >
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">
                                Kuantitas <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="number"
                                min="0.01"
                                step="0.01"
                                name="items[${index}][quantity]"
                                required
                                value="1"
                                class="item-quantity w-full rounded-lg border border-slate-300 px-3 py-2"
                            >
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">
                                Satuan <span class="text-red-500">*</span>
                            </label>

                            <input
                                name="items[${index}][unit]"
                                required
                                value="unit"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2"
                                placeholder="unit, meter, hari"
                            >
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">
                                Harga Satuan (Rp) <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="number"
                                min="0"
                                step="0.01"
                                name="items[${index}][unit_price]"
                                required
                                value="0"
                                class="item-price w-full rounded-lg border border-slate-300 px-3 py-2"
                            >
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">
                                Total Item
                            </label>

                            <div class="item-total rounded-lg bg-slate-50 px-3 py-2 font-semibold text-slate-800">
                                Rp0
                            </div>
                        </div>
                    </div>
                `;

                container.appendChild(card);

                calculateTotals();
            });

            container.addEventListener('click', function (event) {
                const button = event.target.closest('.remove-item');

                if (!button) {
                    return;
                }

                const items = container.querySelectorAll('.estimate-item');

                if (items.length <= 1) {
                    alert('Minimal harus ada satu item pekerjaan.');
                    return;
                }

                button.closest('.estimate-item').remove();

                renumberItems();
                calculateTotals();
            });

            container.addEventListener('input', function (event) {
                if (
                    event.target.matches(
                        '.item-quantity, .item-price'
                    )
                ) {
                    calculateTotals();
                }
            });

            orderSelect.addEventListener('change', function () {
                surveySelect.value = '';
                filterSurveys();
            });

            surveySelect.addEventListener(
                'change',
                updateSurveyInfo
            );

            taxInput.addEventListener(
                'input',
                calculateTotals
            );

            filterSurveys();
            calculateTotals();
        });
    </script>
@endsection