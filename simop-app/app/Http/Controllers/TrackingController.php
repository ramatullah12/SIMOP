<?php

namespace App\Http\Controllers;

use App\Models\Project;

class TrackingController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        abort_unless(
            $user?->role?->slug === 'client',
            403
        );

        $projects = Project::with([
            'workEstimate.order',
            'workEstimate.survey',
            'workEstimate.approvals.approver',
            'planning.production.bapp',
            'rating',
            'invoice' => fn ($query) => $query->whereIn(
                'status',
                ['issued', 'paid']
            ),
        ])
            ->whereHas(
                'workEstimate.order',
                fn ($query) => $query->where(
                    'client_id',
                    $user->id
                )
            )
            ->latest()
            ->paginate(10);

        return view(
            'tracking.index',
            compact('projects')
        );
    }

    public function show(Project $project)
    {
        $user = auth()->user();

        abort_unless(
            $user?->role?->slug === 'client',
            403
        );

        abort_unless(
            $project->workEstimate()
                ->whereHas(
                    'order',
                    fn ($query) => $query->where(
                        'client_id',
                        $user->id
                    )
                )
                ->exists(),
            403
        );

        $project->load([
            'workEstimate.order',
            'workEstimate.survey',
            'workEstimate.approvals.approver',
            'planning.production.bapp',
            'rating',
            'invoice' => fn ($query) => $query->whereIn(
                'status',
                ['issued', 'paid']
            ),
        ]);

        return view(
            'tracking.show',
            compact('project')
        );
    }
}