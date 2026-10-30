@extends('layouts.app')

@section('title', 'Buat Invoice - SIMOP')
@section('page-title', 'Buat Invoice')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <a href="{{ route('invoices.index') }}"
               class="text-sm font-medium text-blue-600 hover:text-blue-700">
                ← Kembali ke Invoice
            </a>

            <h1 class="mt-3 text-2xl font-bold text-slate-900">
                Buat Invoice
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Invoice hanya dapat dibuat untuk Project yang BAPP-nya sudah disetujui.
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                <p class="font-semibold text-red-700">
                    Data belum dapat disimpan.
                </p>

                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($projects->isEmpty())
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-6">
                <h2 class="font-semibold text-amber-800">
                    Belum ada Project yang dapat dibuatkan Invoice
                </h2>

                <p class="mt-2 text-sm text-amber-700">
                    Pastikan Production sudah selesai, BAPP sudah dibuat dan status BAPP sudah Approved.
                </p>

                <a href="{{ route('invoices.index') }}"
                   class="mt-4 inline-flex rounded-lg border border-amber-300 bg-white px-4 py-2 text-sm font-semibold text-amber-700 hover:bg-amber-100">
                    Kembali
                </a>
            </div>
        @else
            <form action="{{ route('invoices.store') }}"
                  method="POST"
                  class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

                @csrf

                <div class="space-y-6 p-6">
                    <div>
                        <label for="project_id"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Project
                        </label>

                        <select name="project_id"
                                id="project_id"
                                required
                                class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">

                            <option value="">
                                Pilih Project
                            </option>

                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}"
                                    @selected(old('project_id') == $project->id)>

                                    {{ $project->project_number }}
                                    -
                                    {{ $project->title }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs text-slate-500">
                            Hanya Project dengan BAPP Approved dan belum memiliki Invoice yang ditampilkan.
                        </p>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="invoice_date"
                                   class="mb-2 block text-sm font-semibold text-slate-700">
                                Tanggal Invoice
                            </label>

                            <input type="date"
                                   name="invoice_date"
                                   id="invoice_date"
                                   value="{{ old('invoice_date', now()->format('Y-m-d')) }}"
                                   required
                                   class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="due_date"
                                   class="mb-2 block text-sm font-semibold text-slate-700">
                                Jatuh Tempo
                            </label>

                            <input type="date"
                                   name="due_date"
                                   id="due_date"
                                   value="{{ old('due_date') }}"
                                   class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="subtotal"
                                   class="mb-2 block text-sm font-semibold text-slate-700">
                                Subtotal
                            </label>

                            <div class="flex rounded-lg shadow-sm">
                                <span class="inline-flex items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 px-4 text-sm text-slate-500">
                                    Rp
                                </span>

                                <input type="number"
                                       name="subtotal"
                                       id="subtotal"
                                       value="{{ old('subtotal', 0) }}"
                                       min="0"
                                       step="0.01"
                                       required
                                       class="w-full rounded-r-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label for="tax_amount"
                                   class="mb-2 block text-sm font-semibold text-slate-700">
                                Pajak
                            </label>

                            <div class="flex rounded-lg shadow-sm">
                                <span class="inline-flex items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 px-4 text-sm text-slate-500">
                                    Rp
                                </span>

                                <input type="number"
                                       name="tax_amount"
                                       id="tax_amount"
                                       value="{{ old('tax_amount', 0) }}"
                                       min="0"
                                       step="0.01"
                                       class="w-full rounded-r-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-5">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-slate-600">
                                Total Invoice
                            </span>

                            <span id="totalPreview"
                                  class="text-xl font-bold text-slate-900">
                                Rp 0
                            </span>
                        </div>

                        <p class="mt-2 text-xs text-slate-500">
                            Total dihitung dari Subtotal + Pajak.
                        </p>
                    </div>

                    <div>
                        <label for="notes"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Catatan
                        </label>

                        <textarea name="notes"
                                  id="notes"
                                  rows="5"
                                  class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Masukkan catatan Invoice jika diperlukan">{{ old('notes') }}</textarea>
                    </div>

                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                        <p class="text-sm font-semibold text-blue-800">
                            Status awal Invoice: Draft
                        </p>

                        <p class="mt-1 text-sm text-blue-700">
                            Client belum dapat melihat Invoice selama status masih Draft. Admin harus menerbitkan Invoice terlebih dahulu.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 sm:flex-row sm:justify-end">
                    <a href="{{ route('invoices.index') }}"
                       class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                        Batal
                    </a>

                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                        Simpan Invoice
                    </button>
                </div>
            </form>
        @endif
    </div>

    <script>
        const subtotalInput = document.getElementById('subtotal');
        const taxInput = document.getElementById('tax_amount');
        const totalPreview = document.getElementById('totalPreview');

        function updateTotal() {
            const subtotal = parseFloat(subtotalInput?.value || 0);
            const tax = parseFloat(taxInput?.value || 0);
            const total = subtotal + tax;

            totalPreview.textContent = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(total);
        }

        subtotalInput?.addEventListener('input', updateTotal);
        taxInput?.addEventListener('input', updateTotal);

        updateTotal();
    </script>
@endsection