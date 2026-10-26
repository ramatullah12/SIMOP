<?php

namespace App\Http\Controllers;

use App\Models\Planning;
use App\Models\Production;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductionController extends Controller
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

        $productions = Production::with([
            'planning.project.workEstimate.order',
            'planning.project.assignedUser',
            'creator',
            'assignedUser',
        ])
            ->when(
                $role === 'petugas-lapangan',
                fn ($query) => $query->where(
                    'assigned_to',
                    $user->id
                )
            )
            ->latest()
            ->paginate(10);

        return view(
            'productions.index',
            compact('productions')
        );
    }

    public function create()
    {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $plannings = Planning::with([
            'project.assignedUser',
        ])
            ->whereIn('status', [
                'ready',
                'in_progress',
                'completed',
            ])
            ->whereDoesntHave('production')
            ->latest()
            ->get();

        $fieldOfficers = User::whereHas(
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
            'productions.create',
            compact(
                'plannings',
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
            'planning_id' => [
                'required',
                'integer',
                'exists:plannings,id',
                'unique:productions,planning_id',
            ],
            'assigned_to' => [
                'required',
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
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
            'description' => [
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

        $planning = Planning::with('project')
            ->findOrFail(
                $validated['planning_id']
            );

        abort_unless(
            in_array(
                $planning->status,
                [
                    'ready',
                    'in_progress',
                    'completed',
                ],
                true
            ),
            422,
            'Planning harus berstatus Siap, Berjalan, atau Selesai.'
        );

        $fieldOfficer = User::with('role')
            ->findOrFail(
                $validated['assigned_to']
            );

        abort_unless(
            $fieldOfficer->role?->slug
                === 'petugas-lapangan',
            422,
            'Petugas yang dipilih harus memiliki role Petugas Lapangan.'
        );

        $production = DB::transaction(
            function () use (
                $validated,
                $planning
            ) {
                $production = Production::create([
                    'planning_id' =>
                        $planning->id,
                    'created_by' =>
                        auth()->id(),
                    'assigned_to' =>
                        $validated['assigned_to'],
                    'production_number' =>
                        $this->generateProductionNumber(),
                    'title' =>
                        $validated['title'],
                    'start_date' =>
                        $validated['start_date'] ?? null,
                    'end_date' =>
                        $validated['end_date'] ?? null,
                    'status' =>
                        'pending',
                    'progress' =>
                        0,
                    'description' =>
                        $validated['description'] ?? null,
                    'notes' =>
                        $validated['notes'] ?? null,
                ]);

                if ($planning->status === 'ready') {
                    $planning->update([
                        'status' => 'in_progress',
                    ]);
                }

                return $production;
            }
        );

        return redirect()
            ->route(
                'productions.show',
                $production
            )
            ->with(
                'success',
                'Produksi & Instalasi berhasil dibuat.'
            );
    }

    public function show(Production $production)
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(
            in_array(
                $role,
                [
                    'admin',
                    'direktur',
                    'petugas-lapangan',
                ],
                true
            ),
            403
        );

        if (
            $role === 'petugas-lapangan'
            && $production->assigned_to !== $user->id
        ) {
            abort(403);
        }

        $production->load([
            'planning.project.workEstimate.order',
            'planning.project.assignedUser',
            'creator',
            'assignedUser',
        ]);

        return view(
            'productions.show',
            compact('production')
        );
    }

    public function updateProgress(
        Request $request,
        Production $production
    ) {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(
            in_array(
                $role,
                [
                    'admin',
                    'petugas-lapangan',
                ],
                true
            ),
            403
        );

        if (
            $role === 'petugas-lapangan'
            && $production->assigned_to !== $user->id
        ) {
            abort(403);
        }

        $validated = $request->validate([
            'progress' => [
                'required',
                'integer',
                'min:0',
                'max:100',
            ],
        ]);

        $progress = (int) $validated['progress'];

        $status = match (true) {
            $progress === 100 => 'completed',
            $progress > 0 => 'in_progress',
            default => 'pending',
        };

        DB::transaction(
            function () use (
                $production,
                $progress,
                $status
            ) {
                $production->update([
                    'progress' => $progress,
                    'status' => $status,
                ]);

                if ($status === 'in_progress') {
                    $production
                        ->planning()
                        ->update([
                            'status' => 'in_progress',
                        ]);
                }

                if ($status === 'completed') {
                    $production
                        ->planning()
                        ->update([
                            'status' => 'completed',
                        ]);
                }
            }
        );

        return redirect()
            ->route(
                'productions.show',
                $production
            )
            ->with(
                'success',
                'Progress Produksi & Instalasi berhasil diperbarui.'
            );
    }

    public function updateStatus(
        Request $request,
        Production $production
    ) {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'in_progress',
                    'completed',
                    'cancelled',
                ]),
            ],
        ]);

        DB::transaction(
            function () use (
                $production,
                $validated
            ) {
                $status = $validated['status'];

                $data = [
                    'status' => $status,
                ];

                if ($status === 'completed') {
                    $data['progress'] = 100;
                }

                if ($status === 'pending') {
                    $data['progress'] = 0;
                }

                $production->update($data);

                if ($status === 'in_progress') {
                    $production
                        ->planning()
                        ->update([
                            'status' => 'in_progress',
                        ]);
                }

                if ($status === 'completed') {
                    $production
                        ->planning()
                        ->update([
                            'status' => 'completed',
                        ]);
                }
            }
        );

        return redirect()
            ->route(
                'productions.show',
                $production
            )
            ->with(
                'success',
                'Status Produksi & Instalasi berhasil diperbarui.'
            );
    }

    private function generateProductionNumber(): string
    {
        do {
            $number = 'PROD-'
                . now()->format('YmdHis')
                . '-'
                . strtoupper(
                    substr(
                        uniqid(),
                        -4
                    )
                );
        } while (
            Production::where(
                'production_number',
                $number
            )->exists()
        );

        return $number;
    }
}