
@extends('layouts.app')

@section('title', 'Approval | SIMOP')
@section('page-title', 'Approval Work Estimate')

@section('content')
<div class="space-y-6">

    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div>
        <h2 class="text-xl font-bold text-slate-900">
            Daftar Persetujuan Work Estimate
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Tinjau dan proses pengajuan estimasi biaya pekerjaan.
        </p>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">No.</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">Nomor Estimasi</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">Judul</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">Total Biaya</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">Status</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">Keputusan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($approvals as $approval)
                        <tr class="hover:bg-slate-50">
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                {{ $approvals->firstItem() + $loop->index }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm font-medium text-slate-900">
                                {{ $approval->workEstimate->estimate_number ?? '-' }}
                            </td>

                            <td class="px-5 py-4 text-sm text-slate-700">
                                {{ $approval->workEstimate->title ?? '-' }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">
                                Rp {{ number_format((float) ($approval->workEstimate->total_amount ?? 0), 0, ',', '.') }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                @if ($approval->status === 'pending')
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                        Menunggu Persetujuan
                                    </span>
                                @elseif ($approval->status === 'approved')
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                        Ditolak
                                    </span>
                                @endif
                            </td>

                            <td class="min-w-64 px-5 py-4 text-sm">
                                @if ($approval->status === 'pending'
                                    && in_array(auth()->user()?->role?->slug, ['direktur', 'ceo'], true))

                                    <form method="POST"
                                          action="{{ route('approvals.decide', $approval) }}"
                                          class="space-y-2">
                                        @csrf
                                        @method('PATCH')

                                        <textarea name="notes"
                                                  rows="2"
                                                  class="w-full rounded-lg border-slate-300 text-sm"
                                                  placeholder="Catatan keputusan (opsional)">{{ old('notes') }}</textarea>

                                        <div class="flex flex-wrap gap-2">
                                            <button type="submit"
                                                    name="status"
                                                    value="approved"
                                                    class="rounded-lg bg-green-600 px-3 py-2 text-xs font-semibold text-white hover:bg-green-700">
                                                Setujui
                                            </button>

                                            <button type="submit"
                                                    name="status"
                                                    value="rejected"
                                                    class="rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">
                                                Tolak
                                            </button>
                                        </div>
                                    </form>

                                @elseif ($approval->status === 'pending')
                                    <span class="text-slate-500">Menunggu keputusan Direktur/CEO</span>
                                @else
                                    <div class="font-medium text-slate-800">
                                        {{ $approval->approver->name ?? '-' }}
                                    </div>
                                    @if ($approval->approved_at)
                                        <div class="mt-1 text-xs text-slate-500">
                                            {{ $approval->approved_at->format('d/m/Y H:i') }}
                                        </div>
                                    @endif
                                    @if ($approval->notes)
                                        <div class="mt-1 text-xs text-slate-500">
                                            Catatan: {{ $approval->notes }}
                                        </div>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center">
                                <p class="font-medium text-slate-700">
                                    Belum ada pengajuan persetujuan.
                                </p>
                                <p class="mt-1 text-sm text-slate-500">
                                    Ajukan Work Estimate berstatus Draft melalui menu Work Estimate.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-5 py-4">
            {{ $approvals->links() }}
        </div>
    </div>
</div>
@endsection
