<?php

namespace App\Http\Controllers;

use App\Models\Planning;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PlanningController extends Controller
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

        $plannings = Planning::with([
            'project.assignedUser',
            'project.order',
            'creator',
            'production',
        ])
            ->when(
                $role === 'petugas-lapangan',
                function ($query) use ($user) {
                    $query->whereHas(
                        'project',
                        function ($projectQuery) use ($user) {
                            $projectQuery->where(
                                'assigned_to',
                                $user->id
                            );
                        }
                    );
                }
            )
            ->latest()
            ->paginate(10);

        return view(
            'plannings.index',
            compact('plannings')
        );
    }

    public function create()
    {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $projects = Project::with([
            'assignedUser',
            'order',
        ])
            ->whereDoesntHave('planning')
            ->whereNotIn(
                'status',
                ['completed', 'cancelled']
            )
            ->latest()
            ->get();

        return view(
            'plannings.create',
            compact('projects')
        );
    }

    public function store(Request $request)
    {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $validated = $request->validate([
            'project_id' => [
                'required',
                'integer',
                'exists:projects,id',
                'unique:plannings,project_id',
            ],
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'planned_start_date' => [
                'nullable',
                'date',
            ],
            'planned_end_date' => [
                'nullable',
                'date',
                'after_or_equal:planned_start_date',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $planning = DB::transaction(
            function () use ($validated) {
                $project = Project::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $validated['project_id']
                    );

                abort_if(
                    in_array(
                        $project->status,
                        ['completed', 'cancelled'],
                        true
                    ),
                    422,
                    'Proyek yang sudah selesai atau dibatalkan tidak dapat dibuatkan perencanaan.'
                );

                $planningExists = Planning::query()
                    ->where(
                        'project_id',
                        $project->id
                    )
                    ->exists();

                abort_if(
                    $planningExists,
                    422,
                    'Proyek ini sudah memiliki perencanaan.'
                );

                return Planning::create([
                    'project_id' => $project->id,
                    'created_by' => auth()->id(),
                    'planning_number' => $this->generatePlanningNumber(),
                    'title' => $validated['title'],
                    'description' => $validated['description'] ?? null,
                    'planned_start_date' => $validated['planned_start_date'] ?? null,
                    'planned_end_date' => $validated['planned_end_date'] ?? null,
                    'status' => 'draft',
                    'notes' => $validated['notes'] ?? null,
                ]);
            }
        );

        return redirect()
            ->route(
                'plannings.show',
                $planning
            )
            ->with(
                'success',
                'Perencanaan berhasil dibuat.'
            );
    }

    public function show(Planning $planning)
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

        $planning->load([
            'project.assignedUser',
            'project.creator',
            'project.order',
            'creator',
            'production',
        ]);

        if (
            $role === 'petugas-lapangan'
            && $planning->project->assigned_to !== $user->id
        ) {
            abort(403);
        }

        return view(
            'plannings.show',
            compact('planning')
        );
    }

    public function updateStatus(
        Request $request,
        Planning $planning
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
            'status' => [
                'required',
                Rule::in([
                    'draft',
                    'ready',
                    'in_progress',
                    'completed',
                    'cancelled',
                ]),
            ],
        ]);

        DB::transaction(
            function () use (
                $planning,
                $validated,
                $user,
                $role
            ) {
                $currentPlanning = Planning::query()
                    ->lockForUpdate()
                    ->findOrFail($planning->id);

                $project = Project::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $currentPlanning->project_id
                    );

                if (
                    $role === 'petugas-lapangan'
                    && $project->assigned_to !== $user->id
                ) {
                    abort(403);
                }

                abort_if(
                    in_array(
                        $project->status,
                        ['completed', 'cancelled'],
                        true
                    ),
                    422,
                    'Perencanaan tidak dapat diubah karena proyek sudah selesai atau dibatalkan.'
                );

                abort_if(
                    in_array(
                        $currentPlanning->status,
                        ['completed', 'cancelled'],
                        true
                    ),
                    422,
                    'Perencanaan yang sudah selesai atau dibatalkan tidak dapat diubah kembali.'
                );

                if (
                    $role === 'petugas-lapangan'
                    && in_array(
                        $validated['status'],
                        ['draft', 'cancelled'],
                        true
                    )
                ) {
                    abort(
                        403,
                        'Petugas Lapangan tidak dapat mengubah perencanaan ke status tersebut.'
                    );
                }

                $allowedTransitions = [
                    'draft' => [
                        'ready',
                        'cancelled',
                    ],
                    'ready' => [
                        'in_progress',
                        'cancelled',
                    ],
                    'in_progress' => [
                        'completed',
                        'cancelled',
                    ],
                ];

                $currentStatus = $currentPlanning->status;
                $newStatus = $validated['status'];

                abort_unless(
                    isset($allowedTransitions[$currentStatus])
                    && in_array(
                        $newStatus,
                        $allowedTransitions[$currentStatus],
                        true
                    ),
                    422,
                    'Perubahan status perencanaan tidak sesuai urutan proses.'
                );

                $currentPlanning->update([
                    'status' => $newStatus,
                ]);

                if (
                    in_array(
                        $newStatus,
                        ['ready', 'in_progress', 'completed'],
                        true
                    )
                    && $project->status === 'planning'
                ) {
                    $project->update([
                        'status' => 'ongoing',
                    ]);
                }
            }
        );

        return redirect()
            ->route(
                'plannings.show',
                $planning
            )
            ->with(
                'success',
                'Status perencanaan berhasil diperbarui.'
            );
    }

    private function generatePlanningNumber(): string
    {
        do {
            $number = 'PLN-'
                . now()->format('YmdHis')
                . '-'
                . strtoupper(
                    substr(
                        uniqid(),
                        -4
                    )
                );
        } while (
            Planning::where(
                'planning_number',
                $number
            )->exists()
        );

        return $number;
    }
}