
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Persetujuan Work Estimate
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 rounded bg-green-100 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto p-6">
                    <table class="w-full border-collapse border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border p-3 text-left">Nomor Estimasi</th>
                                <th class="border p-3 text-left">Judul</th>
                                <th class="border p-3 text-left">Total</th>
                                <th class="border p-3 text-left">Status Approval</th>
                                <th class="border p-3 text-left">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($approvals as $approval)
                                <tr>
                                    <td class="border p-3">
                                        {{ $approval->workEstimate->estimate_number ?? '-' }}
                                    </td>

                                    <td class="border p-3">
                                        {{ $approval->workEstimate->title ?? '-' }}
                                    </td>

                                    <td class="border p-3">
                                        Rp {{ number_format((float) ($approval->workEstimate->total_amount ?? 0), 0, ',', '.') }}
                                    </td>

                                    <td class="border p-3">
                                        {{ ucfirst($approval->status) }}
                                    </td>

                                    <td class="border p-3">
                                        @if ($approval->status === 'pending')
                                            <form method="POST"
                                                  action="{{ route('approvals.decide', $approval) }}"
                                                  class="space-y-2">
                                                @csrf
                                                @method('PATCH')

                                                <textarea
                                                    name="notes"
                                                    class="w-full rounded border-gray-300"
                                                    placeholder="Catatan keputusan"
                                                >{{ old('notes') }}</textarea>

                                                <button
                                                    type="submit"
                                                    name="status"
                                                    value="approved"
                                                    class="rounded bg-green-600 px-3 py-2 text-white">
                                                    Setujui
                                                </button>

                                                <button
                                                    type="submit"
                                                    name="status"
                                                    value="rejected"
                                                    class="rounded bg-red-600 px-3 py-2 text-white">
                                                    Tolak
                                                </button>
                                            </form>
                                        @else
                                            {{ $approval->approver->name ?? '-' }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="border p-4 text-center">
                                        Belum ada pengajuan persetujuan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $approvals->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
