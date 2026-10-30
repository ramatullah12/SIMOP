<?php

namespace App\Http\Controllers;

use App\Models\Bapp;
use App\Models\Production;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BappController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(
            in_array(
                $role,
                ['admin', 'direktur', 'petugas-lapangan'],
                true
            ),
            403
        );

        $bapps = Bapp::with([
            'production.assignedUser',
            'production.planning.project.workEstimate.order',
            'creator',
        ])
            ->when(
                $role === 'petugas-lapangan',
                fn ($query) => $query->whereHas(
                    'production',
                    fn ($productionQuery) => $productionQuery->where(
                        'assigned_to',
                        $user->id
                    )
                )
            )
            ->latest()
            ->paginate(10);

        return view(
            'bapps.index',
            compact('bapps')
        );
    }

    public function create()
    {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $productions = Production::with([
            'assignedUser',
            'planning.project.workEstimate.order',
        ])
            ->where('status', 'completed')
            ->where('progress', 100)
            ->whereDoesntHave('bapp')
            ->whereHas(
                'planning.project',
                fn ($query) => $query->whereNotIn(
                    'status',
                    ['completed', 'cancelled']
                )
            )
            ->latest()
            ->get();

        return view(
            'bapps.create',
            compact('productions')
        );
    }

    public function store(Request $request)
    {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $validated = $request->validate([
            'production_id' => [
                'required',
                'integer',
                'exists:productions,id',
                'unique:bapps,production_id',
            ],
            'completion_date' => [
                'required',
                'date',
            ],
            'location' => [
                'nullable',
                'string',
                'max:255',
            ],
            'work_result' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $bapp = DB::transaction(
            function () use ($validated) {
                $production = Production::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $validated['production_id']
                    );

                abort_unless(
                    $production->status === 'completed'
                    && (int) $production->progress === 100,
                    422,
                    'BAPP hanya dapat dibuat dari Produksi & Instalasi yang sudah selesai 100%.'
                );

                $planning = $production->planning()
                    ->lockForUpdate()
                    ->firstOrFail();

                $project = $planning->project()
                    ->lockForUpdate()
                    ->firstOrFail();

                abort_if(
                    in_array(
                        $project->status,
                        ['completed', 'cancelled'],
                        true
                    ),
                    422,
                    'BAPP tidak dapat dibuat karena Project sudah selesai atau dibatalkan.'
                );

                $bappExists = Bapp::query()
                    ->where(
                        'production_id',
                        $production->id
                    )
                    ->exists();

                abort_if(
                    $bappExists,
                    422,
                    'Produksi & Instalasi ini sudah memiliki BAPP.'
                );

                return Bapp::create([
                    'production_id' => $production->id,
                    'created_by' => auth()->id(),
                    'bapp_number' => $this->generateBappNumber(),
                    'completion_date' => $validated['completion_date'],
                    'location' => $validated['location'] ?? null,
                    'work_result' => $validated['work_result'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'draft',
                ]);
            }
        );

        return redirect()
            ->route(
                'bapps.show',
                $bapp
            )
            ->with(
                'success',
                'BAPP berhasil dibuat dengan status Draft.'
            );
    }

    public function show(Bapp $bapp)
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(
            in_array(
                $role,
                ['admin', 'direktur', 'petugas-lapangan'],
                true
            ),
            403
        );

        $bapp->load([
            'production.assignedUser',
            'production.creator',
            'production.planning.project.workEstimate.order',
            'production.planning.project.assignedUser',
            'creator',
        ]);

        if (
            $role === 'petugas-lapangan'
            && $bapp->production->assigned_to !== $user->id
        ) {
            abort(403);
        }

        return view(
            'bapps.show',
            compact('bapp')
        );
    }

    public function updateStatus(
        Request $request,
        Bapp $bapp
    ) {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'submitted',
                    'approved',
                ]),
            ],
        ]);

        $newStatus = $validated['status'];

        DB::transaction(
            function () use (
                $bapp,
                $newStatus
            ) {
                $currentBapp = Bapp::query()
                    ->lockForUpdate()
                    ->findOrFail($bapp->id);

                abort_if(
                    $currentBapp->status === 'approved',
                    422,
                    'BAPP yang sudah disetujui tidak dapat diubah kembali.'
                );

                abort_unless(
                    in_array(
                        $currentBapp->status,
                        ['draft', 'submitted'],
                        true
                    ),
                    422,
                    'Status BAPP saat ini tidak valid untuk diproses.'
                );

                if ($currentBapp->status === 'draft') {
                    abort_unless(
                        $newStatus === 'submitted',
                        422,
                        'BAPP berstatus Draft hanya dapat diajukan.'
                    );
                }

                if ($currentBapp->status === 'submitted') {
                    abort_unless(
                        $newStatus === 'approved',
                        422,
                        'BAPP yang sudah diajukan hanya dapat disetujui.'
                    );
                }

                $production = Production::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $currentBapp->production_id
                    );

                abort_unless(
                    $production->status === 'completed'
                    && (int) $production->progress === 100,
                    422,
                    'BAPP tidak dapat diproses karena Produksi & Instalasi belum selesai 100%.'
                );

                $planning = $production->planning()
                    ->lockForUpdate()
                    ->firstOrFail();

                $project = $planning->project()
                    ->lockForUpdate()
                    ->firstOrFail();

                abort_if(
                    $project->status === 'cancelled',
                    422,
                    'BAPP tidak dapat diproses karena Project telah dibatalkan.'
                );

                if (
                    $newStatus !== 'approved'
                    && $project->status === 'completed'
                ) {
                    abort(
                        422,
                        'BAPP tidak dapat diproses karena Project sudah selesai.'
                    );
                }

                $currentBapp->update([
                    'status' => $newStatus,
                ]);

                if ($newStatus === 'approved') {
                    $project->update([
                        'status' => 'completed',
                        'progress' => 100,
                    ]);
                }
            }
        );

        return redirect()
            ->route(
                'bapps.show',
                $bapp
            )
            ->with(
                'success',
                $newStatus === 'approved'
                    ? 'BAPP berhasil disetujui. Project telah dinyatakan selesai.'
                    : 'BAPP berhasil diajukan dan menunggu persetujuan.'
            );
    }

    private function generateBappNumber(): string
    {
        do {
            $number = 'BAPP-'
                . now()->format('YmdHis')
                . '-'
                . strtoupper(
                    substr(
                        uniqid(),
                        -4
                    )
                );
        } while (
            Bapp::where(
                'bapp_number',
                $number
            )->exists()
        );

        return $number;
    }
}