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
                'stage' => 'direktur',
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
                'Work Estimate berhasil diajukan dan menunggu persetujuan Direktur.'
            );
    }

    public function decide(Request $request, Approval $approval)
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(
            in_array($role, ['direktur', 'ceo'], true),
            403
        );

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(['approved', 'rejected']),
            ],
            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        abort_unless(
            $approval->status === 'pending',
            422,
            'Pengajuan ini sudah diproses sebelumnya.'
        );

        abort_unless(
            $approval->stage === $role,
            403,
            'Pengajuan ini bukan tahap persetujuan Anda.'
        );

        DB::transaction(function () use (
            $approval,
            $validated,
            $user,
            $role
        ) {
            $approval->update([
                'approver_id' => $user->id,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? $approval->notes,
                'approved_at' => now(),
            ]);

            if ($validated['status'] === 'rejected') {
                $approval->workEstimate()->update([
                    'status' => 'rejected',
                ]);

                return;
            }

            if ($role === 'direktur') {
                $approval->workEstimate()->update([
                    'status' => 'pending',
                ]);

                Approval::firstOrCreate(
                    [
                        'work_estimate_id' => $approval->work_estimate_id,
                        'stage' => 'ceo',
                    ],
                    [
                        'approver_id' => null,
                        'status' => 'pending',
                        'notes' => null,
                        'approved_at' => null,
                    ]
                );

                return;
            }

            if ($role === 'ceo') {
                $approval->workEstimate()->update([
                    'status' => 'approved',
                ]);
            }
        });

        if ($validated['status'] === 'rejected') {
            return redirect()
                ->route('approvals.index')
                ->with(
                    'success',
                    'Work Estimate berhasil ditolak.'
                );
        }

        if ($role === 'direktur') {
            return redirect()
                ->route('approvals.index')
                ->with(
                    'success',
                    'Work Estimate disetujui Direktur dan diteruskan ke CEO.'
                );
        }

        return redirect()
            ->route('approvals.index')
            ->with(
                'success',
                'Work Estimate berhasil disetujui CEO dan mendapatkan persetujuan final.'
            );
    }
}