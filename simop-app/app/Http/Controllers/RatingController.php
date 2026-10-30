<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RatingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(
            in_array($role, ['admin', 'client'], true),
            403
        );

        if ($role === 'admin') {
            $ratings = Rating::with([
                'project.workEstimate.order',
                'project.invoice',
                'client',
            ])
                ->latest()
                ->paginate(10);

            return view(
                'ratings.index',
                compact('ratings')
            );
        }

        $projects = Project::with([
            'workEstimate.order',
            'invoice',
            'rating',
        ])
            ->where('status', 'completed')
            ->whereHas(
                'workEstimate.order',
                fn ($query) => $query->where(
                    'client_id',
                    $user->id
                )
            )
            ->whereHas(
                'invoice',
                fn ($query) => $query->where(
                    'status',
                    'paid'
                )
            )
            ->latest()
            ->paginate(10);

        return view(
            'ratings.index',
            compact('projects')
        );
    }

    public function create(Project $project)
    {
        $user = auth()->user();

        abort_unless(
            $user?->role?->slug === 'client',
            403
        );

        $project->load([
            'workEstimate.order',
            'invoice',
            'rating',
        ]);

        abort_unless(
            $project
                ->workEstimate
                ?->order
                ?->client_id === $user->id,
            403
        );

        abort_unless(
            $project->status === 'completed',
            422,
            'Rating hanya dapat diberikan setelah Project selesai.'
        );

        abort_unless(
            $project->invoice
                && $project->invoice->status === 'paid',
            422,
            'Rating hanya dapat diberikan setelah Invoice lunas.'
        );

        abort_if(
            $project->rating,
            422,
            'Project ini sudah memiliki Rating.'
        );

        return view(
            'ratings.create',
            compact('project')
        );
    }

    public function store(
        Request $request,
        Project $project
    ) {
        $user = auth()->user();

        abort_unless(
            $user?->role?->slug === 'client',
            403
        );

        $project->load([
            'workEstimate.order',
            'invoice',
            'rating',
        ]);

        abort_unless(
            $project
                ->workEstimate
                ?->order
                ?->client_id === $user->id,
            403
        );

        abort_unless(
            $project->status === 'completed',
            422,
            'Rating hanya dapat diberikan setelah Project selesai.'
        );

        abort_unless(
            $project->invoice
                && $project->invoice->status === 'paid',
            422,
            'Rating hanya dapat diberikan setelah Invoice lunas.'
        );

        abort_if(
            $project->rating,
            422,
            'Project ini sudah memiliki Rating.'
        );

        $validated = $request->validate([
            'rating' => [
                'required',
                'integer',
                Rule::in([
                    1,
                    2,
                    3,
                    4,
                    5,
                ]),
            ],
            'review' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $rating = DB::transaction(
            function () use (
                $project,
                $user,
                $validated
            ) {
                abort_if(
                    Rating::where(
                        'project_id',
                        $project->id
                    )->exists(),
                    422,
                    'Project ini sudah memiliki Rating.'
                );

                return Rating::create([
                    'project_id' =>
                        $project->id,
                    'client_id' =>
                        $user->id,
                    'rating' =>
                        $validated['rating'],
                    'review' =>
                        $validated['review'] ?? null,
                ]);
            }
        );

        return redirect()
            ->route(
                'ratings.show',
                $rating
            )
            ->with(
                'success',
                'Rating dan ulasan berhasil dikirim.'
            );
    }

    public function show(Rating $rating)
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(
            in_array(
                $role,
                ['admin', 'client'],
                true
            ),
            403
        );

        $rating->load([
            'project.workEstimate.order',
            'project.invoice',
            'client',
        ]);

        if ($role === 'client') {
            abort_unless(
                $rating->client_id === $user->id,
                403
            );

            abort_unless(
                $rating
                    ->project
                    ?->workEstimate
                    ?->order
                    ?->client_id === $user->id,
                403
            );
        }

        return view(
            'ratings.show',
            compact('rating')
        );
    }
}