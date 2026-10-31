<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\Project;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'direktur', 'petugas-lapangan', 'client'], true), 403);

        $projects = Project::with([
            'order',
            'survey',
            'creator',
            'assignedUser',
            'projectItems.item',
        ])
            ->when($role === 'petugas-lapangan', function ($query) use ($user) {
                $query->where('assigned_to', $user->id);
            })
            ->when($role === 'client', function ($query) use ($user) {
                $query->whereHas('order', function ($orderQuery) use ($user) {
                    $orderQuery->where('client_id', $user->id);
                });
            })
            ->latest()
            ->paginate(10);

        return view('projects.index', compact('projects'));
    }

    public function create(Request $request): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless($role === 'admin', 403);

        $orders = Order::with(['client', 'survey'])
            ->whereDoesntHave('project')
            ->latest()
            ->get();

        $selectedOrder = null;

        if ($request->filled('order_id')) {
            $selectedOrder = Order::with(['client', 'survey'])
                ->whereDoesntHave('project')
                ->find($request->order_id);
        } elseif ($request->filled('survey_id')) {
            $survey = Survey::with('order')->find($request->survey_id);
            if ($survey && $survey->order && ! $survey->order->project) {
                $selectedOrder = $survey->order->load(['client', 'survey']);
            }
        }

        $fieldUsers = User::whereHas('role', function ($query) {
            $query->where('slug', 'petugas-lapangan');
        })->orderBy('name')->get();

        return view('projects.create', compact(
            'orders',
            'selectedOrder',
            'fieldUsers'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless($role === 'admin', 403);

        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'deadline' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:planning,in_progress,completed,cancelled'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $order = Order::with(['project', 'survey'])->findOrFail($validated['order_id']);

        if ($order->project) {
            return back()
                ->withInput()
                ->withErrors([
                    'order_id' => 'Pesanan ini sudah memiliki proyek.',
                ]);
        }

        $project = Project::create([
            'order_id' => $order->id,
            'survey_id' => $order->survey?->id,
            'created_by' => $user->id,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'project_number' => $this->generateProjectNumber(),
            'title' => $validated['title'],
            'start_date' => $validated['start_date'] ?? null,
            'deadline' => $validated['deadline'] ?? null,
            'status' => $validated['status'],
            'progress' => $validated['progress'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Proyek berhasil dibuat.');
    }

    public function show(Project $project): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'direktur', 'petugas-lapangan', 'client'], true), 403);

        if ($role === 'petugas-lapangan' && $project->assigned_to !== $user->id) {
            abort(403);
        }

        if ($role === 'client' && $project->order?->client_id !== $user->id) {
            abort(403);
        }

        $project->load([
            'order',
            'survey',
            'creator',
            'assignedUser',
            'planning.production',
            'documents.uploader',
            'projectItems.item',
            'fieldDocumentations.uploader',
            'rating.client',
        ]);

        $items = Item::orderBy('name')->get();

        return view('projects.show', compact('project', 'items'));
    }

    public function edit(Project $project): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless($role === 'admin', 403);

        $project->load(['order', 'survey', 'assignedUser']);

        $fieldUsers = User::whereHas('role', function ($query) {
            $query->where('slug', 'petugas-lapangan');
        })->orderBy('name')->get();

        return view('projects.edit', compact('project', 'fieldUsers'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless($role === 'admin', 403);

        $validated = $request->validate([
            'assigned_to' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'deadline' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:planning,in_progress,completed,cancelled'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validated['status'] === 'completed') {
            $validated['progress'] = 100;
        }

        $project->update([
            'assigned_to' => $validated['assigned_to'] ?? null,
            'title' => $validated['title'],
            'start_date' => $validated['start_date'] ?? null,
            'deadline' => $validated['deadline'] ?? null,
            'status' => $validated['status'],
            'progress' => $validated['progress'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $returnedCount = 0;
        if (in_array($validated['status'], ['completed', 'cancelled'], true)) {
            $returnedCount = $project->returnAllocatedStock();
            if ($validated['status'] === 'completed') {
                $project->order?->update(['status' => 'selesai']);
            }
        }

        $msg = 'Data proyek berhasil diperbarui.';
        if ($returnedCount > 0) {
            $msg .= ' Semua stok barang yang dibawa ke proyek ini otomatis dikembalikan ke stok awal.';
        }

        return redirect()
            ->route('projects.show', $project)
            ->with('success', $msg);
    }

    public function destroy(Project $project): RedirectResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless($role === 'admin', 403);

        $project->returnAllocatedStock();
        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Proyek berhasil dihapus dan stok barang telah dikembalikan.');
    }

    private function generateProjectNumber(): string
    {
        $date = now()->format('Ymd');
        $count = Project::whereDate('created_at', today())->count() + 1;

        return 'PRJ-' . $date . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
