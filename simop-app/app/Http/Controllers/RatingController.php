<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Rating;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RatingController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'direktur', 'client'], true), 403);

        $ratings = Rating::with([
            'project.order',
            'client',
        ])
            ->when($role === 'client', function ($query) use ($user) {
                $query->where('client_id', $user->id);
            })
            ->latest()
            ->paginate(10);

        return view('ratings.index', compact('ratings'));
    }

    public function create(Request $request): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless($role === 'client', 403);

        $projects = Project::with(['order'])
            ->where('status', 'completed')
            ->whereHas('order', function ($query) use ($user) {
                $query->where('client_id', $user->id);
            })
            ->whereDoesntHave('rating')
            ->latest()
            ->get();

        $selectedProject = null;

        if ($request->filled('project_id')) {
            $selectedProject = Project::with(['order'])
                ->where('status', 'completed')
                ->whereHas('order', function ($query) use ($user) {
                    $query->where('client_id', $user->id);
                })
                ->whereDoesntHave('rating')
                ->find($request->project_id);
        }

        return view('ratings.create', compact('projects', 'selectedProject'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless($role === 'client', 403);

        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['required', 'string'],
        ]);

        $project = Project::with(['order', 'rating'])
            ->findOrFail($validated['project_id']);

        abort_unless($project->order?->client_id === $user->id, 403);

        if ($project->status !== 'completed') {
            return back()
                ->withInput()
                ->withErrors([
                    'project_id' => 'Penilaian hanya dapat diberikan untuk proyek yang sudah selesai.',
                ]);
        }

        if ($project->rating) {
            return back()
                ->withInput()
                ->withErrors([
                    'project_id' => 'Proyek ini sudah memiliki penilaian.',
                ]);
        }

        $rating = Rating::create([
            'project_id' => $project->id,
            'client_id' => $user->id,
            'rating' => $validated['rating'],
            'review' => $validated['review'],
        ]);

        return redirect()
            ->route('ratings.show', $rating)
            ->with('success', 'Terima kasih, penilaian dan ulasan berhasil dikirim.');
    }

    public function show(Rating $rating): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'direktur', 'client'], true), 403);

        if ($role === 'client' && $rating->client_id !== $user->id) {
            abort(403);
        }

        $rating->load([
            'project.order',
            'client',
        ]);

        return view('ratings.show', compact('rating'));
    }

    public function destroy(Rating $rating): RedirectResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless($role === 'admin', 403);

        $rating->delete();

        return redirect()
            ->route('ratings.index')
            ->with('success', 'Penilaian berhasil dihapus.');
    }
}
