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
            in_array($role, ['admin', 'direktur', 'ceo', 'petugas-lapangan'], true),
            403
        );

        $plannings = Planning::with([
            'project.assignedUser',
            'project.workEstimate.order',
            'creator',
        ])
            ->when(
                $role === 'petugas-lapangan',
                fn ($query) => $query->whereHas(
                    'project',
                    fn ($projectQuery) => $projectQuery->where(
                        'assigned_to',
                        $user->id
                    )
                )
            )
            ->latest()
            ->paginate(10);

        return view('plannings.index', compact('plannings'));
    }

    public function create()
    {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $projects = Project::with([
            'assignedUser',
            'workEstimate',
        ])
            ->whereDoesntHave('planning')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->latest()
            ->get();

        return view('plannings.create', compact('projects'));
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

        $project = Project::findOrFail($validated['project_id']);

        abort_if(
            in_array($project->status, ['completed', 'cancelled'], true),
            422,
            'Project yang sudah selesai atau dibatalkan tidak dapat dibuatkan Planning.'
        );

        $planning = DB::transaction(function () use ($validated) {
            return Planning::create([
                'project_id' => $validated['project_id'],
                'created_by' => auth()->id(),
                'planning_number' => $this->generatePlanningNumber(),
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'planned_start_date' => $validated['planned_start_date'] ?? null,
                'planned_end_date' => $validated['planned_end_date'] ?? null,
                'status' => 'draft',
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('plannings.show', $planning)
            ->with('success', 'Planning berhasil dibuat.');
    }

    public function show(Planning $planning)
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(
            in_array($role, ['admin', 'direktur', 'ceo', 'petugas-lapangan'], true),
            403
        );

        $planning->load([
            'project.assignedUser',
            'project.creator',
            'project.workEstimate.order',
            'creator',
        ]);

        if (
            $role === 'petugas-lapangan'
            && $planning->project->assigned_to !== $user->id
        ) {
            abort(403);
        }

        return view('plannings.show', compact('planning'));
    }

    public function updateStatus(Request $request, Planning $planning)
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(
            in_array($role, ['admin', 'petugas-lapangan'], true),
            403
        );

        $planning->load('project');

        if (
            $role === 'petugas-lapangan'
            && $planning->project->assigned_to !== $user->id
        ) {
            abort(403);
        }

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

        if (
            $role === 'petugas-lapangan'
            && in_array($validated['status'], ['draft', 'cancelled'], true)
        ) {
            abort(
                403,
                'Petugas Lapangan tidak dapat mengubah Planning ke status tersebut.'
            );
        }

        $planning->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('plannings.show', $planning)
            ->with('success', 'Status Planning berhasil diperbarui.');
    }

    private function generatePlanningNumber(): string
    {
        do {
            $number = 'PLN-'
                . now()->format('YmdHis')
                . '-'
                . strtoupper(substr(uniqid(), -4));
        } while (
            Planning::where('planning_number', $number)->exists()
        );

        return $number;
    }
}