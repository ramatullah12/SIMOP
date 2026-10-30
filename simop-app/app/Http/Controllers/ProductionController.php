<?php

namespace App\Http\Controllers;

use App\Models\Planning;
use App\Models\Production;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductionController extends Controller
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

        $productions = Production::with([
            'planning.project.workEstimate.order',
            'planning.project.assignedUser',
            'creator',
            'assignedUser',
            'bapp',
        ])
            ->when(
                $role === 'petugas-lapangan',
                fn ($query) => $query->where(
                    'assigned_to',
                    $user->id
                )
            )
            ->latest()
            ->paginate(10);

        return view(
            'productions.index',
            compact('productions')
        );
    }

    public function create()
    {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $plannings = Planning::with([
            'project.assignedUser',
            'project.workEstimate.order',
        ])
            ->where('status', 'completed')
            ->whereDoesntHave('production')
            ->whereHas(
                'project',
                fn ($query) => $query->whereNotIn(
                    'status',
                    ['completed', 'cancelled']
                )
            )
            ->latest()
            ->get();

        $fieldOfficers = User::whereHas(
            'role',
            function ($query) {
                $query->where(
                    'slug',
                    'petugas-lapangan'
                );
            }
        )
            ->orderBy('name')
            ->get();

        return view(
            'productions.create',
            compact(
                'plannings',
                'fieldOfficers'
            )
        );
    }

    public function store(Request $request)
    {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $validated = $request->validate([
            'planning_id' => [
                'required',
                'integer',
                'exists:plannings,id',
                'unique:productions,planning_id',
            ],
            'assigned_to' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'start_date' => [
                'nullable',
                'date',
            ],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
            'description' => [
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

        $production = DB::transaction(
            function () use ($validated) {
                $planning = Planning::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $validated['planning_id']
                    );

                abort_unless(
                    $planning->status === 'completed',
                    422,
                    'Planning harus berstatus Selesai sebelum Produksi & Instalasi dapat dibuat.'
                );

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
                    'Produksi & Instalasi tidak dapat dibuat karena Project sudah selesai atau dibatalkan.'
                );

                $productionExists = Production::query()
                    ->where(
                        'planning_id',
                        $planning->id
                    )
                    ->exists();

                abort_if(
                    $productionExists,
                    422,
                    'Planning ini sudah memiliki Produksi & Instalasi.'
                );

                $fieldOfficer = User::with('role')
                    ->findOrFail(
                        $validated['assigned_to']
                    );

                abort_unless(
                    $fieldOfficer->role?->slug === 'petugas-lapangan',
                    422,
                    'Petugas yang dipilih harus memiliki role Petugas Lapangan.'
                );

                return Production::create([
                    'planning_id' => $planning->id,
                    'created_by' => auth()->id(),
                    'assigned_to' => $fieldOfficer->id,
                    'production_number' => $this->generateProductionNumber(),
                    'title' => $validated['title'],
                    'start_date' => $validated['start_date'] ?? null,
                    'end_date' => $validated['end_date'] ?? null,
                    'status' => 'pending',
                    'progress' => 0,
                    'description' => $validated['description'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ]);
            }
        );

        return redirect()
            ->route(
                'productions.show',
                $production
            )
            ->with(
                'success',
                'Produksi & Instalasi berhasil dibuat.'
            );
    }

    public function show(Production $production)
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

        if (
            $role === 'petugas-lapangan'
            && $production->assigned_to !== $user->id
        ) {
            abort(403);
        }

        $production->load([
            'planning.project.workEstimate.order',
            'planning.project.assignedUser',
            'creator',
            'assignedUser',
            'bapp',
        ]);

        return view(
            'productions.show',
            compact('production')
        );
    }

    public function updateProgress(
        Request $request,
        Production $production
    ) {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(
            in_array(
                $role,
                ['admin', 'petugas-lapangan'],
                true
            ),
            403
        );

        $validated = $request->validate([
            'progress' => [
                'required',
                'integer',
                'min:0',
                'max:100',
            ],
        ]);

        $progress = (int) $validated['progress'];

        DB::transaction(
            function () use (
                $production,
                $progress,
                $user,
                $role
            ) {
                $currentProduction = Production::query()
                    ->lockForUpdate()
                    ->findOrFail($production->id);

                if (
                    $role === 'petugas-lapangan'
                    && $currentProduction->assigned_to !== $user->id
                ) {
                    abort(403);
                }

                abort_if(
                    $currentProduction->status === 'cancelled',
                    422,
                    'Produksi & Instalasi yang dibatalkan tidak dapat diperbarui.'
                );

                abort_if(
                    $currentProduction->status === 'completed',
                    422,
                    'Produksi & Instalasi yang sudah selesai tidak dapat diperbarui.'
                );

                $planning = Planning::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $currentProduction->planning_id
                    );

                abort_unless(
                    $planning->status === 'completed',
                    422,
                    'Planning harus berstatus Selesai.'
                );

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
                    'Progress tidak dapat diperbarui karena Project sudah selesai atau dibatalkan.'
                );

                $status = match (true) {
                    $progress === 100 => 'completed',
                    $progress > 0 => 'in_progress',
                    default => 'pending',
                };

                $currentProduction->update([
                    'progress' => $progress,
                    'status' => $status,
                ]);

                if (
                    in_array(
                        $status,
                        ['in_progress', 'completed'],
                        true
                    )
                    && $project->status === 'planning'
                ) {
                    $project->update([
                        'status' => 'ongoing',
                    ]);
                }

                if ($status === 'completed') {
                    $project->update([
                        'progress' => 100,
                    ]);
                }
            }
        );

        return redirect()
            ->route(
                'productions.show',
                $production
            )
            ->with(
                'success',
                $progress === 100
                    ? 'Produksi & Instalasi telah mencapai 100% dan dinyatakan selesai. Tahap berikutnya adalah BAPP.'
                    : 'Progress Produksi & Instalasi berhasil diperbarui.'
            );
    }

    public function updateStatus(
        Request $request,
        Production $production
    ) {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'in_progress',
                    'cancelled',
                ]),
            ],
        ]);

        DB::transaction(
            function () use (
                $production,
                $validated
            ) {
                $currentProduction = Production::query()
                    ->lockForUpdate()
                    ->findOrFail($production->id);

                abort_if(
                    in_array(
                        $currentProduction->status,
                        ['completed', 'cancelled'],
                        true
                    ),
                    422,
                    'Produksi & Instalasi yang sudah selesai atau dibatalkan tidak dapat diubah statusnya.'
                );

                $planning = Planning::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $currentProduction->planning_id
                    );

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
                    'Status tidak dapat diperbarui karena Project sudah selesai atau dibatalkan.'
                );

                $status = $validated['status'];

                if ($status === 'pending') {
                    abort_unless(
                        $currentProduction->status === 'pending',
                        422,
                        'Produksi & Instalasi yang sudah berjalan tidak dapat dikembalikan ke status Menunggu.'
                    );
                }

                if ($status === 'in_progress') {
                    abort_unless(
                        in_array(
                            $currentProduction->status,
                            ['pending', 'in_progress'],
                            true
                        ),
                        422,
                        'Status Produksi & Instalasi tidak dapat diubah ke Dalam Pengerjaan.'
                    );

                    $progress = max(
                        1,
                        min(
                            99,
                            (int) $currentProduction->progress
                        )
                    );

                    $currentProduction->update([
                        'status' => 'in_progress',
                        'progress' => $progress,
                    ]);

                    if ($project->status === 'planning') {
                        $project->update([
                            'status' => 'ongoing',
                        ]);
                    }

                    return;
                }

                if ($status === 'cancelled') {
                    $currentProduction->update([
                        'status' => 'cancelled',
                    ]);
                }
            }
        );

        return redirect()
            ->route(
                'productions.show',
                $production
            )
            ->with(
                'success',
                $validated['status'] === 'cancelled'
                    ? 'Produksi & Instalasi berhasil dibatalkan.'
                    : 'Status Produksi & Instalasi berhasil diperbarui.'
            );
    }

    private function generateProductionNumber(): string
    {
        do {
            $number = 'PROD-'
                . now()->format('YmdHis')
                . '-'
                . strtoupper(
                    substr(
                        uniqid(),
                        -4
                    )
                );
        } while (
            Production::where(
                'production_number',
                $number
            )->exists()
        );

        return $number;
    }
}