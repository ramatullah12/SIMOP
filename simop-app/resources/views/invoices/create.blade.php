@extends('layouts.app')

@section('title', 'Buat Invoice | SIMOP')
@section('page-title', 'Buat Invoice')

@section('content')
    @php
        $projectData = $projects->map(function ($project) {
            $workEstimate = $project->workEstimate;
            $order = $workEstimate?->order;
            $bapp = $project->planning?->production?->bapp;

            return [
                'id' => $project->id,
                'project_number' => $project->project_number,
                'title' => $project->title,
                'client_name' => $order?->client?->name ?? '-',
                'order_number' => $order?->order_number ?? '-',
                'estimate_number' => $workEstimate?->estimate_number ?? '-',
                'estimate_total' => (float) ($workEstimate?->total_amount ?? 0),
                'bapp_number' => $bapp?->bapp_number ?? '-',
                'bapp_status' => $bapp?->status ?? '-',
            ];
        })->values();

        $oldProjectId = (string) old('project_id', '');
    @endphp

    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <a
                href="{{ route('invoices.index') }}"
                class="text-sm font-semibold text-blue-600 hover:text-blue-800"
            >
                &larr; Kembali ke Invoice
            </a>

            <h2 class="mt-3 text-2xl font-bold text-slate-900">
                Buat Invoice
            </h2>

            <p class="mt-1 text-sm leading-6 text-slate-500">
                Invoice hanya dapat dibuat untuk Project yang sudah selesai dan BAPP telah disetujui.
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                <p class="font-semibold text-red-700">
                    Data belum dapat disimpan.
                </p>

                <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($projects->isEmpty())
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-6">
                <h3 class="font-semibold text-amber-800">
                    Belum Ada Project yang Dapat Dibuatkan Invoice
                </h3>

                <p class="mt-2 text-sm leading-6 text-amber-700">
                    Project harus sudah selesai, Produksi & Instalasi selesai 100%, BAPP berstatus Approved, dan belum memiliki Invoice.
                </p>

                <a
                    href="{{ route('invoices.index') }}"
                    class="mt-4 inline-flex rounded-lg border border-amber-300 bg-white px-4 py-2.5 text-sm font-semibold text-amber-700 hover:bg-amber-100"
                >
                    Kembali ke Invoice
                </a>
            </div>
        @else
            <form
                action="{{ route('invoices.store') }}"
                method="POST"
                class="space-y-6"
            >
                @csrf

                <div class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Pilih Project
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Pilih Project yang akan dibuatkan Invoice.
                        </p>
                    </div>

                    <div>
                        <label
                            for="project_id"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Project <span class="text-red-500">*</span>
                        </label>

                        <select
                            name="project_id"
                            id="project_id"
                            required
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >
                            <option value="">
                                -- Pilih Project --
                            </option>

                            @foreach ($projects as $project)
                                <option
                                    value="{{ $project->id }}"
                                    @selected(old('project_id') == $project->id)
                                >
                                    {{ $project->project_number }}
                                    — {{ $project->title }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs text-slate-500">
                            Hanya Project selesai dengan BAPP Approved dan belum memiliki Invoice yang ditampilkan.
                        </p>

                        @error('project_id')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div
                        id="project-info"
                        class="hidden rounded-xl border border-blue-200 bg-blue-50 p-5"
                    >
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p
                                    id="info-project-title"
                                    class="font-semibold text-blue-950"
                                >
                                    -
                                </p>

                                <p
                                    id="info-project-number"
                                    class="mt-1 text-sm text-blue-700"
                                >
                                    -
                                </p>
                            </div>

                            <span class="inline-flex w-fit rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                Project Selesai
                            </span>
                        </div>

                        <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                                    Client
                                </p>

                                <p
                                    id="info-client"
                                    class="mt-1 text-sm font-semibold text-blue-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                                    Order
                                </p>

                                <p
                                    id="info-order"
                                    class="mt-1 text-sm font-semibold text-blue-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                                    Work Estimate
                                </p>

                                <p
                                    id="info-estimate"
                                    class="mt-1 text-sm font-semibold text-blue-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                                    Nilai Work Estimate
                                </p>

                                <p
                                    id="info-estimate-total"
                                    class="mt-1 text-sm font-semibold text-blue-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                                    Nomor BAPP
                                </p>

                                <p
                                    id="info-bapp"
                                    class="mt-1 text-sm font-semibold text-blue-950"
                                >
                                    -
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                                    Status BAPP
                                </p>

                                <span
                                    id="info-bapp-status"
                                    class="mt-1 inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700"
                                >
                                    -
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Informasi Invoice
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Tentukan tanggal dan nilai Invoice.
                        </p>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label
                                for="invoice_date"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                            >
                                Tanggal Invoice <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="date"
                                name="invoice_date"
                                id="invoice_date"
                                value="{{ old('invoice_date', now()->format('Y-m-d')) }}"
                                required
                                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            >

                            @error('invoice_date')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="due_date"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                            >
                                Jatuh Tempo
                            </label>

                            <input
                                type="date"
                                name="due_date"
                                id="due_date"
                                value="{{ old('due_date') }}"
                                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            >

                            <p class="mt-2 text-xs text-slate-500">
                                Jatuh tempo tidak boleh lebih awal dari tanggal Invoice.
                            </p>

                            @error('due_date')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label
                                for="subtotal"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                            >
                                Subtotal <span class="text-red-500">*</span>
                            </label>

                            <div class="flex">
                                <span class="inline-flex items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 px-4 text-sm font-semibold text-slate-500">
                                    Rp
                                </span>

                                <input
                                    type="number"
                                    name="subtotal"
                                    id="subtotal"
                                    value="{{ old('subtotal', 0) }}"
                                    min="0"
                                    step="0.01"
                                    required
                                    class="w-full rounded-r-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                >
                            </div>

                            @error('subtotal')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="tax_amount"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                            >
                                Pajak
                            </label>

                            <div class="flex">
                                <span class="inline-flex items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 px-4 text-sm font-semibold text-slate-500">
                                    Rp
                                </span>

                                <input
                                    type="number"
                                    name="tax_amount"
                                    id="tax_amount"
                                    value="{{ old('tax_amount', 0) }}"
                                    min="0"
                                    step="0.01"
                                    class="w-full rounded-r-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                >
                            </div>

                            @error('tax_amount')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Subtotal
                                </p>

                                <p
                                    id="subtotalPreview"
                                    class="mt-1 font-semibold text-slate-700"
                                >
                                    Rp 0
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Pajak
                                </p>

                                <p
                                    id="taxPreview"
                                    class="mt-1 font-semibold text-slate-700"
                                >
                                    Rp 0
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Total Invoice
                                </p>

                                <p
                                    id="totalPreview"
                                    class="mt-1 text-xl font-bold text-slate-900"
                                >
                                    Rp 0
                                </p>
                            </div>
                        </div>

                        <p class="mt-4 text-xs text-slate-500">
                            Total Invoice dihitung dari Subtotal + Pajak. Nilai akhir tetap dihitung kembali oleh sistem saat Invoice disimpan.
                        </p>
                    </div>

                    <div>
                        <label
                            for="notes"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Catatan
                        </label>

                        <textarea
                            name="notes"
                            id="notes"
                            rows="5"
                            maxlength="5000"
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            placeholder="Masukkan catatan Invoice jika diperlukan"
                        >{{ old('notes') }}</textarea>

                        @error('notes')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                    <p class="text-sm font-semibold text-blue-800">
                        Status Awal Invoice: Draft
                    </p>

                    <p class="mt-1 text-sm leading-6 text-blue-700">
                        Client belum dapat melihat Invoice selama status masih Draft. Setelah data diperiksa, Admin dapat menerbitkan Invoice sehingga status berubah menjadi Issued.
                    </p>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('invoices.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        Simpan Invoice
                    </button>
                </div>
            </form>
        @endif
    </div>

    @if ($projects->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const projects = @json($projectData);
                const oldProjectId = @json($oldProjectId);

                const projectSelect = document.getElementById('project_id');
                const projectInfo = document.getElementById('project-info');

                const projectTitle = document.getElementById('info-project-title');
                const projectNumber = document.getElementById('info-project-number');
                const clientName = document.getElementById('info-client');
                const orderNumber = document.getElementById('info-order');
                const estimateNumber = document.getElementById('info-estimate');
                const estimateTotal = document.getElementById('info-estimate-total');
                const bappNumber = document.getElementById('info-bapp');
                const bappStatus = document.getElementById('info-bapp-status');

                const subtotalInput = document.getElementById('subtotal');
                const taxInput = document.getElementById('tax_amount');
                const subtotalPreview = document.getElementById('subtotalPreview');
                const taxPreview = document.getElementById('taxPreview');
                const totalPreview = document.getElementById('totalPreview');

                function formatCurrency(value) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        maximumFractionDigits: 0
                    }).format(Number(value) || 0);
                }

                function getSelectedProject() {
                    return projects.find(function (project) {
                        return String(project.id) === String(projectSelect.value);
                    });
                }

                function updateProjectInfo() {
                    const selected = getSelectedProject();

                    if (!selected) {
                        projectInfo.classList.add('hidden');
                        return;
                    }

                    projectTitle.textContent = selected.title;
                    projectNumber.textContent = selected.project_number;
                    clientName.textContent = selected.client_name;
                    orderNumber.textContent = selected.order_number;
                    estimateNumber.textContent = selected.estimate_number;
                    estimateTotal.textContent = formatCurrency(
                        selected.estimate_total
                    );
                    bappNumber.textContent = selected.bapp_number;
                    bappStatus.textContent = selected.bapp_status === 'approved'
                        ? 'Approved'
                        : selected.bapp_status;

                    projectInfo.classList.remove('hidden');
                }

                function updateTotal() {
                    const subtotal = parseFloat(
                        subtotalInput.value || 0
                    );

                    const tax = parseFloat(
                        taxInput.value || 0
                    );

                    subtotalPreview.textContent = formatCurrency(
                        subtotal
                    );

                    taxPreview.textContent = formatCurrency(
                        tax
                    );

                    totalPreview.textContent = formatCurrency(
                        subtotal + tax
                    );
                }

                projectSelect.addEventListener(
                    'change',
                    updateProjectInfo
                );

                subtotalInput.addEventListener(
                    'input',
                    updateTotal
                );

                taxInput.addEventListener(
                    'input',
                    updateTotal
                );

                if (oldProjectId) {
                    projectSelect.value = oldProjectId;
                }

                updateProjectInfo();
                updateTotal();
            });
        </script>
    @endif
@endsection