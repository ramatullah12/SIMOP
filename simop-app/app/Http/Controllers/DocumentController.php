<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'direktur', 'petugas-lapangan', 'client'], true), 403);

        $documents = Document::with([
            'project.order',
            'uploader',
        ])
            ->when($role === 'petugas-lapangan', function ($query) use ($user) {
                $query->whereHas('project', function ($projectQuery) use ($user) {
                    $projectQuery->where('assigned_to', $user->id);
                });
            })
            ->when($role === 'client', function ($query) use ($user) {
                $query->whereHas('project.order', function ($orderQuery) use ($user) {
                    $orderQuery->where('client_id', $user->id);
                });
            })
            ->latest()
            ->paginate(10);

        return view('documents.index', compact('documents'));
    }

    public function create(Request $request): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'petugas-lapangan'], true), 403);

        $projects = Project::with(['order'])
            ->when($role === 'petugas-lapangan', function ($query) use ($user) {
                $query->where('assigned_to', $user->id);
            })
            ->latest()
            ->get();

        $selectedProject = null;

        if ($request->filled('project_id')) {
            $selectedProject = Project::with(['order'])
                ->find($request->project_id);
        }

        return view('documents.create', compact('projects', 'selectedProject'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'petugas-lapangan'], true), 403);

        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'document_type' => ['required', 'in:contract,technical_drawing,other'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip', 'max:10240'],
            'notes' => ['nullable', 'string'],
        ]);

        $project = Project::findOrFail($validated['project_id']);

        if ($role === 'petugas-lapangan' && $project->assigned_to !== $user->id) {
            abort(403);
        }

        $uploadedFile = $request->file('file');
        $storedPath = $uploadedFile->store('documents', 'public');

        $document = Document::create([
            'project_id' => $project->id,
            'uploaded_by' => $user->id,
            'document_number' => $this->generateDocumentNumber(),
            'title' => $validated['title'],
            'document_type' => $validated['document_type'],
            'file_path' => $storedPath,
            'file_name' => $uploadedFile->getClientOriginalName(),
            'mime_type' => $uploadedFile->getClientMimeType(),
            'file_size' => $uploadedFile->getSize(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Dokumen berhasil diunggah.');
    }

    public function show(Document $document): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'direktur', 'petugas-lapangan', 'client'], true), 403);

        if ($role === 'petugas-lapangan' && $document->project->assigned_to !== $user->id) {
            abort(403);
        }

        if ($role === 'client' && $document->project->workEstimate->order->client_id !== $user->id) {
            abort(403);
        }

        $document->load([
            'project.order',
            'uploader',
        ]);

        return view('documents.show', compact('document'));
    }

    public function download(Document $document): StreamedResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'direktur', 'petugas-lapangan', 'client'], true), 403);

        if ($role === 'petugas-lapangan' && $document->project->assigned_to !== $user->id) {
            abort(403);
        }

        if ($role === 'client' && $document->project->workEstimate->order->client_id !== $user->id) {
            abort(403);
        }

        abort_unless(Storage::disk('public')->exists($document->file_path), 404);

        return Storage::disk('public')->download(
            $document->file_path,
            $document->file_name
        );
    }

    public function destroy(Document $document): RedirectResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'petugas-lapangan'], true), 403);

        if ($role === 'petugas-lapangan' && $document->uploaded_by !== $user->id) {
            abort(403);
        }

        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()
            ->route('documents.index')
            ->with('success', 'Dokumen berhasil dihapus.');
    }

    private function generateDocumentNumber(): string
    {
        $date = now()->format('Ymd');
        $count = Document::whereDate('created_at', today())->count() + 1;

        return 'DOC-' . $date . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
