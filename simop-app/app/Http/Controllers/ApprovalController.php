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
            in_array(auth()->user()?->role?->slug, ['direktur', 'ceo', 'admin']),
            403
        );

        $approvals = Approval::with([
            'workEstimate.order',
            'approver',
        ])->latest()->paginate(10);

        return view('approvals.index', compact('approvals'));
    }

    public function store(Request $request, WorkEstimate $workEstimate)
    {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $validated = $request->validate([
            'notes' => ['nullable', 'string'],
        ]);

        abort_unless(
            $workEstimate->status === 'draft',
            422,
            'Hanya estimasi berstatus draft yang dapat diajukan.'
        );

        DB::transaction(function () use ($workEstimate, $validated) {
            $workEstimate->update(['status' => 'pending']);

            Approval::create([
                'work_estimate_id' => $workEstimate->id,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.work-estimates.show', $workEstimate)
            ->with('success', 'Estimasi berhasil diajukan untuk persetujuan.');
    }

    public function decide(Request $request, Approval $approval)
    {
        abort_unless(
            in_array(auth()->user()?->role?->slug, ['direktur', 'ceo']),
            403
        );

        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'notes' => ['nullable', 'string'],
        ]);

        abort_unless($approval->status === 'pending', 422);

        DB::transaction(function () use ($approval, $validated) {
            $approval->update([
                'approver_id' => auth()->id(),
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'approved_at' => now(),
            ]);

            $approval->workEstimate()->update([
                'status' => $validated['status'],
            ]);
        });

        return redirect()
            ->route('approvals.index')
            ->with('success', 'Keputusan persetujuan berhasil disimpan.');
    }
}
