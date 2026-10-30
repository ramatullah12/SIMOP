<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SurveyController extends Controller
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

        $query = Survey::with([
            'order',
            'assignedUser',
        ])->latest();

        if ($role === 'petugas-lapangan') {
            $query->where(
                'assigned_to',
                $user->id
            );
        }

        $surveys = $query->paginate(10);

        return view(
            'surveys.index',
            compact('surveys')
        );
    }

    public function create()
    {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $orders = Order::latest()->get();

        $officers = User::whereHas(
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
            'surveys.create',
            compact('orders', 'officers')
        );
    }

    public function store(Request $request)
    {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $validated = $request->validate([
            'order_id' => [
                'required',
                'exists:orders,id',
            ],
            'assigned_to' => [
                'nullable',
                Rule::exists(
                    'users',
                    'id'
                )->where(function ($query) {
                    $query->whereIn(
                        'role_id',
                        function ($subquery) {
                            $subquery
                                ->select('id')
                                ->from('roles')
                                ->where(
                                    'slug',
                                    'petugas-lapangan'
                                );
                        }
                    );
                }),
            ],
            'scheduled_at' => [
                'nullable',
                'date',
            ],
            'location' => [
                'required',
                'string',
                'max:255',
            ],
            'status' => [
                'required',
                Rule::in([
                    'dijadwalkan',
                    'berlangsung',
                    'selesai',
                    'dibatalkan',
                ]),
            ],
            'findings' => [
                'nullable',
                'string',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        Survey::create($validated);

        return redirect()
            ->route('admin.surveys.index')
            ->with(
                'success',
                'Survey berhasil dibuat.'
            );
    }

    public function show(Survey $survey)
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

        if ($role === 'petugas-lapangan') {
            abort_unless(
                $survey->assigned_to === $user->id,
                403
            );
        }

        $survey->load([
            'order',
            'assignedUser',
        ]);

        $officers = collect();

        if ($role === 'admin') {
            $officers = User::whereHas(
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
        }

        return view(
            'surveys.show',
            compact(
                'survey',
                'officers'
            )
        );
    }

    public function update(
        Request $request,
        Survey $survey
    ) {
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

        if ($role === 'petugas-lapangan') {
            abort_unless(
                $survey->assigned_to === $user->id,
                403
            );

            abort_if(
                $survey->status === 'dibatalkan',
                422,
                'Survey yang dibatalkan tidak dapat diperbarui.'
            );

            abort_if(
                $survey->status === 'selesai',
                422,
                'Survey yang sudah selesai tidak dapat diperbarui.'
            );

            $validated = $request->validate([
                'status' => [
                    'required',
                    Rule::in([
                        'berlangsung',
                        'selesai',
                    ]),
                ],
                'findings' => [
                    'nullable',
                    'string',
                ],
                'notes' => [
                    'nullable',
                    'string',
                ],
            ]);

            if ($validated['status'] === 'selesai') {
                $request->validate([
                    'findings' => [
                        'required',
                        'string',
                    ],
                ]);
            }

            $survey->update($validated);

            return redirect()
                ->route(
                    'admin.surveys.show',
                    $survey
                )
                ->with(
                    'success',
                    'Hasil Survey berhasil diperbarui.'
                );
        }

        $validated = $request->validate([
            'assigned_to' => [
                'nullable',
                Rule::exists(
                    'users',
                    'id'
                )->where(function ($query) {
                    $query->whereIn(
                        'role_id',
                        function ($subquery) {
                            $subquery
                                ->select('id')
                                ->from('roles')
                                ->where(
                                    'slug',
                                    'petugas-lapangan'
                                );
                        }
                    );
                }),
            ],
            'scheduled_at' => [
                'nullable',
                'date',
            ],
            'location' => [
                'required',
                'string',
                'max:255',
            ],
            'status' => [
                'required',
                Rule::in([
                    'dijadwalkan',
                    'berlangsung',
                    'selesai',
                    'dibatalkan',
                ]),
            ],
            'findings' => [
                'nullable',
                'string',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        if ($validated['status'] === 'selesai') {
            $request->validate([
                'findings' => [
                    'required',
                    'string',
                ],
            ]);
        }

        $survey->update($validated);

        return redirect()
            ->route(
                'admin.surveys.show',
                $survey
            )
            ->with(
                'success',
                'Survey berhasil diperbarui.'
            );
    }
}