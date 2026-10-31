<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Data Stok Barang & Pemakaian Proyek
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Pengelolaan stok barang gudang, alokasi pemakaian barang pada proyek, serta pengembalian barang setelah proyek selesai.
            </p>
        </div>
    </x-slot>

    @php
        $totalInitialStock = $items->sum('total_stock');
        $totalUsedStock = $items->sum(fn ($item) => $item->used_stock);
        $totalAvailableStock = $items->sum(fn ($item) => $item->available_stock);
        $returnedCount = $allocations->where('status', 'returned')->count();
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6">
            @if (session('success'))
                <div class="rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3">
                    <ul class="list-disc pl-5 text-sm space-y-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase text-gray-500">Total Stok Awal</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($totalInitialStock) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $items->count() }} jenis barang</p>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase text-yellow-700">Sedang Dipakai Proyek</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-2">{{ number_format($totalUsedStock) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Barang di lokasi proyek aktif</p>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase text-green-700">Sisa Stok Tersedia</p>
                    <p class="text-2xl font-bold text-green-600 mt-2">{{ number_format($totalAvailableStock) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Tersedia di gudang</p>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase text-blue-700">Riwayat Barang Kembali</p>
                    <p class="text-2xl font-bold text-blue-600 mt-2">{{ number_format($returnedCount) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Telah dikembalikan ke stok awal</p>
                </div>
            </div>

            @if ($role === 'admin')
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                        <h3 class="text-base font-semibold text-gray-900 mb-1">
                            Input Pemakaian Barang Proyek
                        </h3>
                        <p class="text-xs text-gray-500 mb-4">
                            Pilih proyek dan jumlah barang yang dibawa. Stok otomatis kembali saat proyek selesai.
                        </p>

                        <form method="POST" action="{{ route('inventories.allocate') }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Proyek</label>
                                <select name="project_id" required class="w-full rounded-md border-gray-300 text-sm">
                                    <option value="">-- Pilih Proyek --</option>
                                    @foreach ($activeProjects as $proj)
                                        <option value="{{ $proj->id }}">
                                            {{ $proj->project_number }} - {{ $proj->title }} ({{ $proj->workEstimate?->order?->event_name ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Barang</label>
                                    <select name="item_id" required class="w-full rounded-md border-gray-300 text-sm">
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->name }} (Sisa: {{ $item->available_stock }} / {{ $item->total_stock }} {{ $item->unit }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Dibawa</label>
                                    <input type="number" name="quantity" min="1" required class="w-full rounded-md border-gray-300 text-sm">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                                <input type="text" name="notes" class="w-full rounded-md border-gray-300 text-sm">
                            </div>

                            <button type="submit" class="px-4 py-2 bg-blue-600 rounded-md text-white text-sm font-medium hover:bg-blue-700">
                                Simpan Alokasi Barang
                            </button>
                        </form>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                        <h3 class="text-base font-semibold text-gray-900 mb-1">
                            Tambah Data Barang Baru
                        </h3>
                        <p class="text-xs text-gray-500 mb-4">
                            Tambahkan data barang inventaris beserta jumlah stok awalnya.
                        </p>

                        <form method="POST" action="{{ route('inventories.items.store') }}" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                                    <input type="text" name="name" required class="w-full rounded-md border-gray-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                    <input type="text" name="category" class="w-full rounded-md border-gray-300 text-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok Awal</label>
                                    <input type="number" name="total_stock" min="0" required class="w-full rounded-md border-gray-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                                    <input type="text" name="unit" value="unit" required class="w-full rounded-md border-gray-300 text-sm">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                                <input type="text" name="notes" class="w-full rounded-md border-gray-300 text-sm">
                            </div>

                            <button type="submit" class="px-4 py-2 bg-gray-800 rounded-md text-white text-sm font-medium hover:bg-gray-900">
                                Tambah Barang
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Daftar Stok Barang</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Kode</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama Barang</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600">Stok Awal</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600">Dipakai Proyek</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600">Sisa Stok</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Keterangan Proyek</th>
                                @if ($role === 'admin')
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Ubah Stok & Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($items as $item)
                                @php
                                    $activeUsages = $item->projectItems->where('status', 'allocated');
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $item->item_code }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-gray-900">{{ $item->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->category ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold">
                                        {{ $item->total_stock }} {{ $item->unit }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold text-yellow-700">
                                        {{ $item->used_stock }} {{ $item->unit }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold text-green-700">
                                        {{ $item->available_stock }} {{ $item->unit }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($activeUsages->isNotEmpty())
                                            <div class="space-y-1">
                                                @foreach ($activeUsages as $usage)
                                                    <div class="text-xs text-gray-700">
                                                        <a href="{{ route('projects.show', $usage->project) }}" class="text-blue-600 hover:underline font-medium">
                                                            {{ $usage->project?->project_number }}
                                                        </a>
                                                        ({{ $usage->project?->workEstimate?->order?->event_name ?? $usage->project?->title }}):
                                                        <strong>{{ $usage->quantity }} {{ $item->unit }}</strong>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-xs text-green-700">Stok lengkap di gudang</span>
                                        @endif
                                    </td>
                                    @if ($role === 'admin')
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <form method="POST" action="{{ route('inventories.items.update', $item) }}" class="flex items-center gap-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="name" value="{{ $item->name }}">
                                                    <input type="hidden" name="category" value="{{ $item->category }}">
                                                    <input type="hidden" name="unit" value="{{ $item->unit }}">
                                                    <input type="hidden" name="notes" value="{{ $item->notes }}">
                                                    <input type="number" name="total_stock" value="{{ $item->total_stock }}" min="{{ $item->used_stock }}"
                                                           style="width: 80px; padding: 5px 8px;">
                                                    <button type="submit" class="px-2.5 py-1 bg-blue-600 text-white rounded text-xs font-medium hover:bg-blue-700">
                                                        Simpan
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('inventories.items.destroy', $item) }}"
                                                      data-confirm-title="Hapus Data Stok Barang"
                                                      data-confirm-detail="{{ $item->item_code }} - {{ $item->name }} ({{ $item->total_stock }} {{ $item->unit }})"
                                                      data-confirm-message="Data barang ini beserta catatan pemakaiannya akan dihapus secara permanen dari sistem.">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-2.5 py-1 bg-red-600 text-white rounded text-xs font-medium hover:bg-red-700">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">Belum ada data barang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900 mb-1">
                    Riwayat Pemakaian & Pengembalian Barang Proyek
                </h3>
                <p class="text-xs text-gray-500 mb-4">
                    Daftar barang yang dibawa ke proyek dan status pengembaliannya ke stok awal setelah event selesai.
                </p>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Proyek & Event</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Barang</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600">Stok Awal</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600">Jumlah Dibawa</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600">Status Proyek</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Status Stok Barang</th>
                                @if ($role === 'admin')
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($allocations as $alloc)
                                <tr>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('projects.show', $alloc->project) }}" class="font-medium text-blue-600 hover:underline">
                                            {{ $alloc->project?->project_number }}
                                        </a>
                                        <p class="text-xs text-gray-600">{{ $alloc->project?->workEstimate?->order?->event_name ?? $alloc->project?->title }}</p>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900">
                                        {{ $alloc->item?->name }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        {{ $alloc->item?->total_stock }} {{ $alloc->item?->unit }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold">
                                        {{ $alloc->quantity }} {{ $alloc->item?->unit }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($alloc->project?->status === 'completed')
                                            <span class="px-2 py-1 rounded bg-green-100 text-green-800 text-xs font-medium">
                                                Selesai
                                            </span>
                                        @else
                                            <span class="px-2 py-1 rounded bg-blue-100 text-blue-800 text-xs font-medium">
                                                {{ strtoupper(str_replace('_', ' ', $alloc->project?->status ?? '-')) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($alloc->status === 'returned')
                                            <span class="px-2.5 py-1 rounded bg-green-100 text-green-800 text-xs font-medium">
                                                Sudah kembali ke stok awal ({{ $alloc->item?->total_stock }} {{ $alloc->item?->unit }})
                                            </span>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Tanggal kembali: {{ $alloc->returned_at?->format('d/m/Y H:i') ?? '-' }}
                                            </p>
                                        @else
                                            <span class="px-2.5 py-1 rounded bg-yellow-100 text-yellow-800 text-xs font-medium">
                                                Sedang dibawa proyek (Sisa: {{ $alloc->item?->available_stock }} {{ $alloc->item?->unit }})
                                            </span>
                                        @endif
                                    </td>
                                    @if ($role === 'admin')
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                @if ($alloc->status === 'allocated')
                                                    <form method="POST" action="{{ route('inventories.return', $alloc) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="px-2.5 py-1 bg-green-600 text-white rounded text-xs font-medium hover:bg-green-700">
                                                            Kembalikan Stok
                                                        </button>
                                                    </form>
                                                @endif

                                                <form method="POST" action="{{ route('inventories.allocations.destroy', $alloc) }}"
                                                      data-confirm-title="Hapus Riwayat Pemakaian Barang"
                                                      data-confirm-detail="{{ $alloc->item?->name }} - {{ $alloc->quantity }} {{ $alloc->item?->unit }} ({{ $alloc->project?->project_number }})"
                                                      data-confirm-message="Riwayat pemakaian barang pada proyek ini akan dihapus dari daftar.">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-2.5 py-1 bg-red-600 text-white rounded text-xs font-medium hover:bg-red-700">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                        Belum ada data pemakaian barang proyek.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
