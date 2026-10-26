<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    public function index()
    {
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

        $documents = Document::with([
            'project',
            'project.assignedUser',
            'uploader',
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

        return view(
            'documents.index',
            compact('documents')
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
            'workEstimate.order',
        ])
            ->whereNotIn(
                'status',
                ['cancelled']
            )
            ->latest()
            ->get();

        return view(
            'documents.create',
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
            ],
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'document_type' => [
                'required',
                Rule::in([
                    'po_spk',
                    'survey',
                    'work_estimate',
                    'production',
                    'bapp',
                    'invoice',
                    'other',
                ]),
            ],
            'file' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
                'max:10240',
            ],
            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'client_access' => [
                'nullable',
                'boolean',
            ],
        ]);

        $project = Project::findOrFail(
            $validated['project_id']
        );

        $file = $request->file('file');

        $storedPath = $file->store(
            'documents/' . $project->id,
            'local'
        );

        try {
            $document = DB::transaction(
                function () use (
                    $validated,
                    $project,
                    $file,
                    $storedPath
                ) {
                    return Document::create([
                        'project_id' =>
                            $project->id,
                        'uploaded_by' =>
                            auth()->id(),
                        'document_number' =>
                            $this->generateDocumentNumber(),
                        'title' =>
                            $validated['title'],
                        'document_type' =>
                            $validated['document_type'],
                        'file_name' =>
                            $file->getClientOriginalName(),
                        'file_path' =>
                            $storedPath,
                        'mime_type' =>
                            $file->getMimeType(),
                        'file_size' =>
                            $file->getSize(),
                        'description' =>
                            $validated['description'] ?? null,
                        'client_access' =>
                            $request->boolean('client_access'),
                    ]);
                }
            );
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete(
                $storedPath
            );

            throw $exception;
        }

        return redirect()
            ->route(
                'documents.show',
                $document
            )
            ->with(
                'success',
                'Dokumen berhasil diunggah.'
            );
    }

    public function show(Document $document)
    {
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

        $document->load([
            'project.assignedUser',
            'project.workEstimate.order',
            'uploader',
        ]);

        if (
            $role === 'petugas-lapangan'
            && $document->project->assigned_to !== $user->id
        ) {
            abort(403);
        }

        return view(
            'documents.show',
            compact('document')
        );
    }

    public function download(Document $document)
    {
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

        $document->load('project');

        if (
            $role === 'petugas-lapangan'
            && $document->project->assigned_to !== $user->id
        ) {
            abort(403);
        }

        abort_unless(
            Storage::disk('local')->exists(
                $document->file_path
            ),
            404,
            'File dokumen tidak ditemukan.'
        );

        return Storage::disk('local')->download(
            $document->file_path,
            $document->file_name
        );
    }

    public function destroy(Document $document)
    {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        DB::transaction(
            function () use ($document) {
                $filePath = $document->file_path;

                $document->delete();

                if (
                    Storage::disk('local')->exists(
                        $filePath
                    )
                ) {
                    Storage::disk('local')->delete(
                        $filePath
                    );
                }
            }
        );

        return redirect()
            ->route('documents.index')
            ->with(
                'success',
                'Dokumen berhasil dihapus.'
            );
    }

    private function generateDocumentNumber(): string
    {
        do {
            $number = 'DOC-'
                . now()->format('YmdHis')
                . '-'
                . strtoupper(
                    substr(
                        uniqid(),
                        -4
                    )
                );
        } while (
            Document::where(
                'document_number',
                $number
            )->exists()
        );

        return $number;
    }
}