<?php

namespace App\Http\Controllers;

use App\Models\FieldDocumentation;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FieldDocumentationController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'direktur', 'petugas-lapangan', 'client'], true), 403);

        $query = FieldDocumentation::with([
            'project.order',
            'production',
            'uploader',
        ]);

        if ($role === 'client') {
            $query->whereHas('project.order', function ($q) use ($user) {
                $q->where('client_id', $user->id);
            });
        }

        $documentations = $query->latest()->paginate(10);

        return view('field-documentations.index', compact('documentations', 'role'));
    }

    public function create(Request $request): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless($role === 'petugas-lapangan', 403, 'Hanya Petugas Lapangan yang dapat mencatat dokumentasi lapangan pengerjaan.');

        $projects = Project::with(['order', 'planning.production'])
            ->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                    ->orWhereHas('planning.production', function ($sub) use ($user) {
                        $sub->where('assigned_to', $user->id);
                    });
            })
            ->latest()
            ->get();

        if ($projects->isEmpty()) {
            $projects = Project::with(['order', 'planning.production'])
                ->latest()
                ->get();
        }

        $selectedProject = null;
        if ($request->filled('project_id')) {
            $selectedProject = Project::with(['order', 'planning.production'])
                ->find($request->project_id);
        }

        return view('field-documentations.create', compact('projects', 'selectedProject'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless($role === 'petugas-lapangan', 403, 'Hanya Petugas Lapangan yang dapat mencatat dokumentasi lapangan pengerjaan.');

        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'activity_date' => ['required', 'date'],
            'progress_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'description' => ['required', 'string'],
            'file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,zip', 'max:10240'],
            'notes' => ['nullable', 'string'],
        ]);

        $project = Project::with('planning.production')->findOrFail($validated['project_id']);
        $production = $project->planning?->production;

        $filePath = null;
        $fileName = null;
        $mimeType = null;
        $fileSize = null;

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $filePath = $uploadedFile->store('field-documentations', 'public');
            $fileName = $uploadedFile->getClientOriginalName();
            $mimeType = $uploadedFile->getClientMimeType();
            $fileSize = $uploadedFile->getSize();
        }

        $docNumber = 'DOK-LAP-' . now()->format('Ymd') . '-' . str_pad((string) (FieldDocumentation::count() + 1), 4, '0', STR_PAD_LEFT);

        $doc = FieldDocumentation::create([
            'documentation_number' => $docNumber,
            'project_id' => $project->id,
            'production_id' => $production?->id,
            'uploaded_by' => $user->id,
            'title' => $validated['title'],
            'activity_date' => $validated['activity_date'],
            'progress_percentage' => $validated['progress_percentage'] ?? $project->progress,
            'description' => $validated['description'],
            'file_path' => $filePath,
            'file_name' => $fileName,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('field-documentations.show', $doc)
            ->with('success', 'Dokumentasi lapangan pengerjaan berhasil disimpan oleh Petugas Lapangan.');
    }

    public function show(FieldDocumentation $fieldDocumentation): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'direktur', 'petugas-lapangan', 'client'], true), 403);

        if ($role === 'client') {
            abort_unless(
                $fieldDocumentation->project?->order?->client_id === $user->id,
                403
            );
        }

        $fieldDocumentation->load([
            'project.order',
            'production',
            'uploader',
        ]);

        return view('field-documentations.show', compact('fieldDocumentation', 'role'));
    }

    public function download(FieldDocumentation $fieldDocumentation): StreamedResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'direktur', 'petugas-lapangan', 'client'], true), 403);

        if ($role === 'client') {
            abort_unless(
                $fieldDocumentation->project?->order?->client_id === $user->id,
                403
            );
        }

        abort_unless(
            $fieldDocumentation->file_path && Storage::disk('public')->exists($fieldDocumentation->file_path),
            404,
            'Berkas lampiran dokumentasi tidak ditemukan.'
        );

        return Storage::disk('public')->download(
            $fieldDocumentation->file_path,
            $fieldDocumentation->file_name ?? basename($fieldDocumentation->file_path)
        );
    }

    public function destroy(FieldDocumentation $fieldDocumentation): RedirectResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['petugas-lapangan', 'admin'], true), 403);

        if ($fieldDocumentation->file_path && Storage::disk('public')->exists($fieldDocumentation->file_path)) {
            Storage::disk('public')->delete($fieldDocumentation->file_path);
        }

        $fieldDocumentation->delete();

        return redirect()
            ->route('field-documentations.index')
            ->with('success', 'Dokumentasi lapangan pengerjaan berhasil dihapus.');
    }
}
