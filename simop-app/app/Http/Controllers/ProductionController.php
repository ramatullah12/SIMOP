<?php

namespace App\Http\Controllers;

use App\Models\Planning;
use App\Models\Production;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductionController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'direktur', 'petugas-lapangan'], true), 403);

        $productions = Production::with([
            'planning.project.order',
            'creator',
            'assignedUser',
        ])
            ->when($role === 'petugas-lapangan', function ($query) use ($user) {
                $query->where('assigned_to', $user->id);
            })
            ->latest()
            ->paginate(10);

        return view('productions.index', compact('productions'));
    }

    public function create(Request $request): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless($role === 'admin', 403);

        $plannings = Planning::with(['project.order', 'project.assignedUser'])
            ->whereIn('status', ['approved', 'in_progress'])
            ->whereDoesntHave('production')
            ->latest()
            ->get();

        $selectedPlanning = null;

        if ($request->filled('planning_id')) {
            $selectedPlanning = Planning::with(['project.order', 'project.assignedUser'])
                ->whereIn('status', ['approved', 'in_progress'])
                ->whereDoesntHave('production')
                ->find($request->planning_id);
        }

        $fieldUsers = User::whereHas('role', function ($query) {
            $query->where('slug', 'petugas-lapangan');
        })->orderBy('name')->get();

        return view('productions.create', compact(
            'plannings',
            'selectedPlanning',
            'fieldUsers'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless($role === 'admin', 403);

        $validated = $request->validate([
            'planning_id' => ['required', 'exists:plannings,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'description' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $planning = Planning::with(['production', 'project'])->findOrFail($validated['planning_id']);

        if ($planning->production) {
            return back()
                ->withInput()
                ->withErrors([
                    'planning_id' => 'Planning ini sudah memiliki Production.',
                ]);
        }

        $production = Production::create([
            'planning_id' => $planning->id,
            'created_by' => $user->id,
            'assigned_to' => $validated['assigned_to'] ?? $planning->project->assigned_to,
            'production_number' => $this->generateProductionNumber(),
            'title' => $validated['title'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'status' => $validated['status'],
            'progress' => $validated['progress'],
            'description' => $validated['description'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $project = $planning->project;

        if ($validated['status'] === 'in_progress') {
            $planning->update(['status' => 'in_progress']);
            $project->update([
                'status' => 'in_progress',
                'progress' => max((int) $project->progress, (int) $validated['progress']),
            ]);
        }

        if ($validated['status'] === 'completed' || (int) $validated['progress'] === 100) {
            $planning->update(['status' => 'completed']);
            $project->update([
                'status' => 'completed',
                'progress' => 100,
            ]);
            $project->returnAllocatedStock();
            $project->workEstimate?->order?->update(['status' => 'selesai']);
        }

        return redirect()
            ->route('productions.show', $production)
            ->with('success', 'Production berhasil dibuat.');
    }

    public function show(Production $production): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'direktur', 'petugas-lapangan'], true), 403);

        if ($role === 'petugas-lapangan' && $production->assigned_to !== $user->id) {
            abort(403);
        }

        $production->load([
            'planning.project.order',
            'planning.project.projectItems.item',
            'creator',
            'assignedUser',
            'fieldDocumentations.uploader',
        ]);

        return view('productions.show', compact('production'));
    }

    public function edit(Production $production): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless($role === 'admin', 403);

        $production->load([
            'planning.project.order',
            'assignedUser',
        ]);

        $fieldUsers = User::whereHas('role', function ($query) {
            $query->where('slug', 'petugas-lapangan');
        })->orderBy('name')->get();

        return view('productions.edit', compact('production', 'fieldUsers'));
    }

    public function update(Request $request, Production $production): RedirectResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless($role === 'admin', 403);

        $validated = $request->validate([
            'assigned_to' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'description' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validated['status'] === 'completed') {
            $validated['progress'] = 100;
        }

        $production->update([
            'assigned_to' => $validated['assigned_to'] ?? null,
            'title' => $validated['title'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'status' => $validated['status'],
            'progress' => $validated['progress'],
            'description' => $validated['description'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $planning = $production->planning;
        $project = $planning->project;

        $returnedCount = 0;

        if ($validated['status'] === 'in_progress') {
            $planning->update(['status' => 'in_progress']);
            $project->update([
                'status' => 'in_progress',
                'progress' => (int) $validated['progress'],
            ]);
        } elseif ($validated['status'] === 'completed' || (int) $validated['progress'] === 100) {
            $planning->update(['status' => 'completed']);
            $project->update([
                'status' => 'completed',
                'progress' => 100,
            ]);
            $returnedCount = $project->returnAllocatedStock();
            $project->workEstimate?->order?->update(['status' => 'selesai']);
        } elseif ($validated['status'] === 'cancelled') {
            $returnedCount = $project->returnAllocatedStock();
        }

        $msg = 'Production berhasil diperbarui.';
        if ($returnedCount > 0) {
            $msg .= ' Event/proyek selesai: seluruh stok barang yang dibawa otomatis dikembalikan ke stok awal.';
        }

        return redirect()
            ->route('productions.show', $production)
            ->with('success', $msg);
    }

    public function updateProgress(Request $request, Production $production): RedirectResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless($role === 'petugas-lapangan', 403);
        abort_unless($production->assigned_to === $user->id, 403);

        $validated = $request->validate([
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'status' => ['required', 'in:pending,in_progress,completed'],
            'notes' => ['nullable', 'string'],
        ]);

        if ((int) $validated['progress'] === 100) {
            $validated['status'] = 'completed';
        } elseif ($validated['status'] === 'completed' && (int) $validated['progress'] < 100) {
            $validated['progress'] = 100;
        }

        $production->update([
            'progress' => $validated['progress'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $planning = $production->planning;
        $project = $planning->project;
        $returnedCount = 0;

        if ($validated['status'] === 'in_progress') {
            $planning->update(['status' => 'in_progress']);
            $project->update([
                'status' => 'in_progress',
                'progress' => (int) $validated['progress'],
            ]);
        } elseif ($validated['status'] === 'completed') {
            $planning->update(['status' => 'completed']);
            $project->update([
                'status' => 'completed',
                'progress' => 100,
            ]);
            $returnedCount = $project->returnAllocatedStock();
            $project->workEstimate?->order?->update(['status' => 'selesai']);
        }

        $msg = 'Progress production berhasil diperbarui.';
        if ($returnedCount > 0) {
            $msg .= ' Pengerjaan selesai 100%: stok barang proyek otomatis dikembalikan ke stok awal.';
        }

        return redirect()
            ->route('productions.show', $production)
            ->with('success', $msg);
    }

    public function destroy(Production $production): RedirectResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless($role === 'admin', 403);

        $production->delete();

        return redirect()
            ->route('productions.index')
            ->with('success', 'Production berhasil dihapus.');
    }

    private function generateProductionNumber(): string
    {
        $date = now()->format('Ymd');
        $count = Production::whereDate('created_at', today())->count() + 1;

        return 'PRD-' . $date . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
