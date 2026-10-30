<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\WorkEstimate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ApprovalController extends Controller
{
    public function index()
    {
        $role = auth()->user()?->role?->slug;

        abort_unless(
            in_array($role, ['admin', 'direktur'], true),
            403
        );

        $approvals = Approval::with([
            'workEstimate.order',
            'workEstimate.creator',
            'approver',
        ])
            ->where('stage', 'direktur')
            ->latest()
            ->paginate(10);

        return view(
            'approvals.index',
            compact('approvals')
        );
    }

    public function store(
        Request $request,
        WorkEstimate $workEstimate
    ) {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $validated = $request->validate([
            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        DB::transaction(function () use (
            $workEstimate,
            $validated
        ) {
            $estimate = WorkEstimate::query()
                ->lockForUpdate()
                ->findOrFail($workEstimate->id);

            abort_unless(
                $estimate->status === 'draft',
                422,
                'Hanya Work Estimate berstatus Draft yang dapat diajukan.'
            );

            $pendingApprovalExists = Approval::query()
                ->where('work_estimate_id', $estimate->id)
                ->where('stage', 'direktur')
                ->where('status', 'pending')
                ->exists();

            abort_if(
                $pendingApprovalExists,
                422,
                'Work Estimate ini sudah memiliki pengajuan Approval Direktur yang masih menunggu keputusan.'
            );

            $estimate->update([
                'status' => 'pending',
            ]);

            Approval::create([
                'work_estimate_id' => $estimate->id,
                'stage' => 'direktur',
                'approver_id' => null,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'approved_at' => null,
            ]);
        });

        return redirect()
            ->route(
                'admin.work-estimates.show',
                $workEstimate
            )
            ->with(
                'success',
                'Work Estimate berhasil diajukan dan sedang menunggu persetujuan Direktur.'
            );
    }

    public function decide(
        Request $request,
        Approval $approval
    ) {
        $user = auth()->user();

        abort_unless(
            $user?->role?->slug === 'direktur',
            403
        );

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'approved',
                    'rejected',
                ]),
            ],
            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        DB::transaction(function () use (
            $approval,
            $validated,
            $user
        ) {
            $currentApproval = Approval::query()
                ->with('workEstimate')
                ->lockForUpdate()
                ->findOrFail($approval->id);

            abort_unless(
                $currentApproval->stage === 'direktur',
                403,
                'Pengajuan ini bukan tahap persetujuan Direktur.'
            );

            abort_unless(
                $currentApproval->status === 'pending',
                422,
                'Pengajuan ini sudah diproses sebelumnya.'
            );

            $estimate = WorkEstimate::query()
                ->lockForUpdate()
                ->findOrFail(
                    $currentApproval->work_estimate_id
                );

            abort_unless(
                $estimate->status === 'pending',
                422,
                'Work Estimate ini tidak sedang menunggu persetujuan Direktur.'
            );

            $currentApproval->update([
                'approver_id' => $user->id,
                'status' => $validated['status'],
                'notes' => $validated['notes']
                    ?? $currentApproval->notes,
                'approved_at' => now(),
            ]);

            $estimate->update([
                'status' => $validated['status'] === 'approved'
                    ? 'approved'
                    : 'rejected',
            ]);
        });

        if ($validated['status'] === 'approved') {
            return redirect()
                ->route('approvals.index')
                ->with(
                    'success',
                    'Work Estimate berhasil disetujui oleh Direktur dan mendapatkan persetujuan final.'
                );
        }

        return redirect()
            ->route('approvals.index')
            ->with(
                'success',
                'Work Estimate berhasil ditolak oleh Direktur.'
            );
    }
}