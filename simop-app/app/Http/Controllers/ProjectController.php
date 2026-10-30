<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\WorkEstimate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
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

        $projects = Project::with([
            'workEstimate.order',
            'creator',
            'assignedUser',
            'planning.production.bapp',
        ])
            ->when(
                $role === 'petugas-lapangan',
                function ($query) use ($user) {
                    $query->where(
                        'assigned_to',
                        $user->id
                    );
                }
            )
            ->latest()
            ->paginate(10);

        return view(
            'projects.index',
            compact('projects')
        );
    }

    public function create()
    {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $workEstimates = WorkEstimate::with([
            'order',
            'survey',
        ])
            ->where('status', 'approved')
            ->whereDoesntHave('project')
            ->latest()
            ->get();

        $fieldOfficers = User::with('role')
            ->whereHas(
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
            'projects.create',
            compact(
                'workEstimates',
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
            'work_estimate_id' => [
                'required',
                'integer',
                'exists:work_estimates,id',
                'unique:projects,work_estimate_id',
            ],
            'assigned_to' => [
                'nullable',
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
            'deadline' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        if (!empty($validated['assigned_to'])) {
            $fieldOfficer = User::with('role')
                ->findOrFail(
                    $validated['assigned_to']
                );

            abort_unless(
                $fieldOfficer->role?->slug === 'petugas-lapangan',
                422,
                'Petugas yang dipilih harus memiliki role Petugas Lapangan.'
            );
        }

        $project = DB::transaction(
            function () use ($validated) {
                $workEstimate = WorkEstimate::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $validated['work_estimate_id']
                    );

                abort_unless(
                    $workEstimate->status === 'approved',
                    422,
                    'Hanya Work Estimate yang sudah mendapatkan persetujuan final Direktur yang dapat dijadikan Project.'
                );

                $projectExists = Project::query()
                    ->where(
                        'work_estimate_id',
                        $workEstimate->id
                    )
                    ->exists();

                abort_if(
                    $projectExists,
                    422,
                    'Work Estimate ini sudah memiliki Project.'
                );

                return Project::create([
                    'work_estimate_id' => $workEstimate->id,
                    'created_by' => auth()->id(),
                    'assigned_to' => $validated['assigned_to'] ?? null,
                    'project_number' => $this->generateProjectNumber(),
                    'title' => $validated['title'],
                    'start_date' => $validated['start_date'] ?? null,
                    'deadline' => $validated['deadline'] ?? null,
                    'status' => 'planning',
                    'progress' => 0,
                    'notes' => $validated['notes'] ?? null,
                ]);
            }
        );

        return redirect()
            ->route(
                'projects.show',
                $project
            )
            ->with(
                'success',
                'Project berhasil dibuat dari Work Estimate yang telah disetujui Direktur.'
            );
    }

    public function show(Project $project)
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
            && $project->assigned_to !== $user->id
        ) {
            abort(403);
        }

        $project->load([
            'workEstimate.order',
            'workEstimate.survey.assignedUser',
            'workEstimate.approvals.approver',
            'creator',
            'assignedUser',
            'planning.production.bapp',
            'invoice',
            'rating',
        ]);

        return view(
            'projects.show',
            compact('project')
        );
    }

    public function updateProgress(
        Request $request,
        Project $project
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

        if (
            $role === 'petugas-lapangan'
            && $project->assigned_to !== $user->id
        ) {
            abort(403);
        }

        abort_if(
            $project->status === 'completed',
            422,
            'Project yang sudah selesai tidak dapat diubah progress-nya.'
        );

        abort_if(
            $project->status === 'cancelled',
            422,
            'Project yang dibatalkan tidak dapat diubah progress-nya.'
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

        $status = $progress > 0
            ? 'ongoing'
            : 'planning';

        $project->update([
            'progress' => $progress,
            'status' => $status,
        ]);

        return redirect()
            ->route(
                'projects.show',
                $project
            )
            ->with(
                'success',
                $progress === 100
                    ? 'Progress Project mencapai 100%. Project tetap berstatus berjalan sampai BAPP disetujui.'
                    : 'Progress Project berhasil diperbarui.'
            );
    }

    public function updateStatus(
        Request $request,
        Project $project
    ) {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        abort_if(
            $project->status === 'completed',
            422,
            'Project yang sudah selesai tidak dapat diubah statusnya.'
        );

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'planning',
                    'ongoing',
                    'cancelled',
                ]),
            ],
        ]);

        $status = $validated['status'];

        $data = [
            'status' => $status,
        ];

        if ($status === 'planning') {
            $data['progress'] = 0;
        }

        $project->update($data);

        return redirect()
            ->route(
                'projects.show',
                $project
            )
            ->with(
                'success',
                $status === 'cancelled'
                    ? 'Project berhasil dibatalkan.'
                    : 'Status Project berhasil diperbarui.'
            );
    }

    private function generateProjectNumber(): string
    {
        do {
            $number = 'PRJ-'
                . now()->format('YmdHis')
                . '-'
                . strtoupper(
                    substr(
                        uniqid(),
                        -4
                    )
                );
        } while (
            Project::where(
                'project_number',
                $number
            )->exists()
        );

        return $number;
    }
}