<?php

namespace App\Http\Controllers;


use App\Models\Approval;
use App\Models\WorkEstimate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ApprovalController extends Controller
{
    /**
     * Menampilkan daftar pengajuan persetujuan.
     */
    public function index()
    {
        abort_unless(
            in_array(
                auth()->user()?->role?->slug,
                ['admin', 'direktur', 'ceo'],
                true
            ),
            403
        );

        $approvals = Approval::with([
            'workEstimate.order',
            'workEstimate.creator',
            'approver',
        ])
            ->latest()
            ->paginate(10);

        return view('approvals.index', compact('approvals'));
    }

    /**
     * Mengajukan Work Estimate untuk persetujuan.
     * Hanya Admin yang dapat mengajukan.
     */
    public function store(Request $request, WorkEstimate $workEstimate)
    {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        abort_unless(
            $workEstimate->status === 'draft',
            422,
            'Hanya estimasi berstatus draft yang dapat diajukan.'
        );

        DB::transaction(function () use ($workEstimate, $validated) {
            $workEstimate->update([
                'status' => 'pending',
            ]);

            Approval::create([
                'work_estimate_id' => $workEstimate->id,
                'approver_id' => null,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'approved_at' => null,
            ]);
        });

        return redirect()
            ->route('admin.work-estimates.show', $workEstimate)
            ->with(
                'success',
                'Estimasi berhasil diajukan untuk persetujuan.'
            );
    }

    /**
     * Menyetujui atau menolak pengajuan.
     * Hanya Direktur atau CEO yang dapat memutuskan.
     */
    public function decide(Request $request, Approval $approval)
    {
        abort_unless(
            in_array(
                auth()->user()?->role?->slug,
                ['direktur', 'ceo'],
                true
            ),
            403
        );

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(['approved', 'rejected']),
            ],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        abort_unless(
            $approval->status === 'pending',
            422,
            'Pengajuan ini sudah diproses sebelumnya.'
        );

        DB::transaction(function () use ($approval, $validated) {
            $approval->update([
                'approver_id' => auth()->id(),
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? $approval->notes,
                'approved_at' => now(),
            ]);

            $approval->workEstimate()->update([
                'status' => $validated['status'],
            ]);
        });

        return redirect()
            ->route('approvals.index')
            ->with(
                'success',
                $validated['status'] === 'approved'
                    ? 'Work Estimate berhasil disetujui.'
                    : 'Work Estimate ditolak.'
            );
    }
}
