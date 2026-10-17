
@extends('layouts.app')

@section('title', 'Buat Estimasi | SIMOP')
@section('page-title', 'Buat Estimasi Biaya')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">

    <div>
        <a href="{{ route('admin.work-estimates.index') }}"
           class="text-sm font-medium text-blue-600 hover:text-blue-800">
            &larr; Kembali ke Daftar Estimasi
        </a>
        <h2 class="mt-3 text-2xl font-bold text-slate-900">
            Buat Estimasi Biaya
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Isi informasi pekerjaan dan rincian biaya.
        </p>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.work-estimates.store') }}"
          method="POST"
          class="space-y-6">
        @csrf

        <div class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900">Informasi Pekerjaan</h3>

            <div>
                <label for="title" class="mb-2 block text-sm font-semibold text-slate-700">
                    Judul Estimasi *
                </label>
                <input id="title" name="title" type="text" required
                       value="{{ old('title') }}"
                       placeholder="Contoh: Estimasi Instalasi Peralatan"
                       class="w-full rounded-lg border border-slate-300 px-4 py-3">
            </div>

            <div>
                <label for="order_id" class="mb-2 block text-sm font-semibold text-slate-700">
                    Order & RFQ *
                </label>
                <select id="order_id" name="order_id" required
                        class="w-full rounded-lg border border-slate-300 px-4 py-3">
                    <option value="">-- Pilih Order --</option>
                    @foreach ($orders as $order)
                        <option value="{{ $order->id }}"
                            @selected(old('order_id') == $order->id)>
                            {{ $order->order_number }} — {{ $order->company_name }}
                        </option>
                    @endforeach
                </select>
                @if ($orders->isEmpty())
                    <p class="mt-2 text-sm text-amber-600">
                        Belum ada Order. Buat Order terlebih dahulu.
                    </p>
                @endif
            </div>

            <div>
                <label for="survey_id" class="mb-2 block text-sm font-semibold text-slate-700">
                    Survey Terkait
                </label>
                <select id="survey_id" name="survey_id"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3">
                    <option value="">-- Tanpa Survey --</option>
                    @foreach ($surveys as $survey)
                        <option value="{{ $survey->id }}"
                            @selected(old('survey_id') == $survey->id)>
                            {{ $survey->order->order_number ?? 'Order #'.$survey->order_id }}
                            — {{ $survey->location }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="description" class="mb-2 block text-sm font-semibold text-slate-700">
                    Deskripsi Pekerjaan
                </label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Jelaskan ruang lingkup pekerjaan..."
                          class="w-full rounded-lg border border-slate-300 px-4 py-3">{{ old('description') }}</textarea>
            </div>

            <div>
                <label for="tax_rate" class="mb-2 block text-sm font-semibold text-slate-700">
                    Pajak (%)
                </label>
                <input id="tax_rate" name="tax_rate" type="number"
                       min="0" max="100" step="0.01" required
                       value="{{ old('tax_rate', '0') }}"
                       class="w-full rounded-lg border border-slate-300 px-4 py-3">
                <p class="mt-1 text-xs text-slate-500">
                    Isi sesuai ketentuan pajak yang berlaku. Gunakan 0 jika tidak diterapkan.
                </p>
            </div>
        </div>

        <div class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Rincian Item Pekerjaan</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Tambahkan item, kuantitas, satuan, dan harga satuan.
                    </p>
                </div>
                <button type="button" id="add-item"
                        class="rounded-lg border border-blue-600 px-4 py-2 text-sm font-semibold text-blue-600 hover:bg-blue-50">
                    + Tambah Item
                </button>
            </div>

            <div id="items-container" class="space-y-5">
                @foreach (old('items', [['item_name' => '', 'description' => '', 'quantity' => '1', 'unit' => 'unit', 'unit_price' => '0']]) as $i => $item)
                    <div class="estimate-item rounded-lg border border-slate-200 p-4">
                        <div class="mb-4 flex items-center justify-between">
                            <h4 class="font-semibold text-slate-800 item-title">
                                Item {{ $i + 1 }}
                            </h4>
                            <button type="button"
                                    class="remove-item text-sm font-medium text-red-600 hover:text-red-800">
                                Hapus
                            </button>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">
                                    Nama Item *
                                </label>
                                <input name="items[{{ $i }}][item_name]" required
                                       value="{{ $item['item_name'] ?? '' }}"
                                       class="w-full rounded-lg border border-slate-300 px-3 py-2"
                                       placeholder="Contoh: Material instalasi">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">
                                    Deskripsi
                                </label>
                                <input name="items[{{ $i }}][description]"
                                       value="{{ $item['description'] ?? '' }}"
                                       class="w-full rounded-lg border border-slate-300 px-3 py-2"
                                       placeholder="Keterangan item">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">
                                    Kuantitas *
                                </label>
                                <input type="number" min="0.01" step="0.01"
                                       name="items[{{ $i }}][quantity]" required
                                       value="{{ $item['quantity'] ?? '1' }}"
                                       class="item-quantity w-full rounded-lg border border-slate-300 px-3 py-2">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">
                                    Satuan *
                                </label>
                                <input name="items[{{ $i }}][unit]" required
                                       value="{{ $item['unit'] ?? 'unit' }}"
                                       class="w-full rounded-lg border border-slate-300 px-3 py-2"
                                       placeholder="unit, meter, hari">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">
                                    Harga Satuan (Rp) *
                                </label>
                                <input type="number" min="0" step="0.01"
                                       name="items[{{ $i }}][unit_price]" required
                                       value="{{ $item['unit_price'] ?? '0' }}"
                                       class="item-price w-full rounded-lg border border-slate-300 px-3 py-2">
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
                    <span class="text-slate-600">Subtotal</span>
                    <strong id="subtotal">Rp0</strong>
                </div>
                <div class="flex justify-between py-2 text-sm">
                    <span class="text-slate-600">Pajak</span>
                    <strong id="tax-amount">Rp0</strong>
                </div>
                <div class="mt-2 flex justify-between border-t border-slate-200 pt-4">
                    <span class="font-bold text-slate-900">Total Estimasi</span>
                    <strong id="grand-total" class="text-xl text-blue-700">Rp0</strong>
                </div>
                <p class="mt-2 text-xs text-slate-500">
                    Perhitungan ini merupakan pratinjau. Total akhir dihitung ulang oleh server.
                </p>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <label for="notes" class="mb-2 block text-sm font-semibold text-slate-700">
                Catatan Tambahan
            </label>
            <textarea id="notes" name="notes" rows="3"
                      class="w-full rounded-lg border border-slate-300 px-4 py-3"
                      placeholder="Catatan untuk estimasi ini...">{{ old('notes') }}</textarea>
        </div>

        <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">
            <a href="{{ route('admin.work-estimates.index') }}"
               class="rounded-lg border border-slate-300 px-5 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Batal
            </a>
            <button type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                Simpan Estimasi
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('items-container');
    const addButton = document.getElementById('add-item');
    const taxInput = document.getElementById('tax_rate');

    const rupiah = value => 'Rp' + new Intl.NumberFormat('id-ID', {
        maximumFractionDigits: 2
    }).format(value);

    function renumberItems() {
        container.querySelectorAll('.estimate-item').forEach((card, index) => {
            card.querySelector('.item-title').textContent = 'Item ' + (index + 1);
            card.querySelectorAll('[name]').forEach(input => {
                input.name = input.name.replace(/items\[\d+\]/, 'items[' + index + ']');
            });
        });
    }

    function calculateTotals() {
        let subtotal = 0;

        container.querySelectorAll('.estimate-item').forEach(card => {
            const quantity = parseFloat(card.querySelector('.item-quantity').value) || 0;
            const price = parseFloat(card.querySelector('.item-price').value) || 0;
            const total = quantity * price;

            card.querySelector('.item-total').textContent = rupiah(total);
            subtotal += total;
        });

        const taxRate = parseFloat(taxInput.value) || 0;
        const tax = subtotal * taxRate / 100;

        document.getElementById('subtotal').textContent = rupiah(subtotal);
        document.getElementById('tax-amount').textContent = rupiah(tax);
        document.getElementById('grand-total').textContent = rupiah(subtotal + tax);
    }

    addButton.addEventListener('click', function () {
        const index = container.querySelectorAll('.estimate-item').length;
        const card = document.createElement('div');

        card.className = 'estimate-item rounded-lg border border-slate-200 p-4';
        card.innerHTML = `
            <div class="mb-4 flex items-center justify-between">
                <h4 class="font-semibold text-slate-800 item-title">Item ${index + 1}</h4>
                <button type="button" class="remove-item text-sm font-medium text-red-600">Hapus</button>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium">Nama Item *</label>
                    <input name="items[${index}][item_name]" required class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Nama pekerjaan">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium">Deskripsi</label>
                    <input name="items[${index}][description]" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Keterangan item">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium">Kuantitas *</label>
                    <input type="number" min="0.01" step="0.01" name="items[${index}][quantity]" required value="1" class="item-quantity w-full rounded-lg border border-slate-300 px-3 py-2">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium">Satuan *</label>
                    <input name="items[${index}][unit]" required value="unit" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium">Harga Satuan (Rp) *</label>
                    <input type="number" min="0" step="0.01" name="items[${index}][unit_price]" required value="0" class="item-price w-full rounded-lg border border-slate-300 px-3 py-2">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium">Total Item</label>
                    <div class="item-total rounded-lg bg-slate-50 px-3 py-2 font-semibold">Rp0</div>
                </div>
            </div>
        `;

        container.appendChild(card);
        calculateTotals();
    });

    container.addEventListener('click', function (event) {
        const button = event.target.closest('.remove-item');
        if (!button) return;

        if (container.querySelectorAll('.estimate-item').length <= 1) {
            alert('Minimal harus ada satu item pekerjaan.');
            return;
        }

        button.closest('.estimate-item').remove();
        renumberItems();
        calculateTotals();
    });

    container.addEventListener('input', function (event) {
        if (event.target.matches('.item-quantity, .item-price')) {
            calculateTotals();
        }
    });

    taxInput.addEventListener('input', calculateTotals);

    calculateTotals();
});
</script>
@endsection

