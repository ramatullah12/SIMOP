<?php

namespace App\Http\Controllers;

use App\Models\Bapp;
use App\Models\Production;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BappController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(
            in_array(
                $role,
                ['admin', 'direktur', 'ceo', 'petugas-lapangan'],
                true
            ),
            403
        );

        $bapps = Bapp::with([
            'production.assignedUser',
            'production.planning.project.workEstimate.order',
            'creator',
        ])
            ->when(
                $role === 'petugas-lapangan',
                fn ($query) => $query->whereHas(
                    'production',
                    fn ($productionQuery) => $productionQuery->where(
                        'assigned_to',
                        $user->id
                    )
                )
            )
            ->latest()
            ->paginate(10);

        return view('bapps.index', compact('bapps'));
    }

    public function create()
    {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $productions = Production::with([
            'assignedUser',
            'planning.project',
        ])
            ->where('status', 'completed')
            ->where('progress', 100)
            ->whereDoesntHave('bapp')
            ->latest()
            ->get();

        return view('bapps.create', compact('productions'));
    }

    public function store(Request $request)
    {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $validated = $request->validate([
            'production_id' => [
                'required',
                'integer',
                'exists:productions,id',
                'unique:bapps,production_id',
            ],
            'completion_date' => [
                'required',
                'date',
            ],
            'location' => [
                'nullable',
                'string',
                'max:255',
            ],
            'work_result' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $production = Production::findOrFail(
            $validated['production_id']
        );

        abort_unless(
            $production->status === 'completed'
            && $production->progress === 100,
            422,
            'BAPP hanya dapat dibuat dari pekerjaan yang sudah selesai 100%.'
        );

        $bapp = DB::transaction(function () use (
            $validated,
            $production
        ) {
            return Bapp::create([
                'production_id' => $production->id,
                'created_by' => auth()->id(),
                'bapp_number' => $this->generateBappNumber(),
                'completion_date' => $validated['completion_date'],
                'location' => $validated['location'] ?? null,
                'work_result' => $validated['work_result'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'draft',
            ]);
        });

        return redirect()
            ->route('bapps.show', $bapp)
            ->with(
                'success',
                'BAPP berhasil dibuat.'
            );
    }

    public function show(Bapp $bapp)
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(
            in_array(
                $role,
                ['admin', 'direktur', 'ceo', 'petugas-lapangan'],
                true
            ),
            403
        );

        $bapp->load([
            'production.assignedUser',
            'production.creator',
            'production.planning.project.workEstimate.order',
            'creator',
        ]);

        if (
            $role === 'petugas-lapangan'
            && $bapp->production->assigned_to !== $user->id
        ) {
            abort(403);
        }

        return view('bapps.show', compact('bapp'));
    }

    public function updateStatus(Request $request, Bapp $bapp)
    {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'draft',
                    'submitted',
                    'approved',
                ]),
            ],
        ]);

        $bapp->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('bapps.show', $bapp)
            ->with(
                'success',
                'Status BAPP berhasil diperbarui.'
            );
    }

    private function generateBappNumber(): string
    {
        do {
            $number = 'BAPP-'
                . now()->format('YmdHis')
                . '-'
                . strtoupper(substr(uniqid(), -4));
        } while (
            Bapp::where('bapp_number', $number)->exists()
        );

        return $number;
    }
}