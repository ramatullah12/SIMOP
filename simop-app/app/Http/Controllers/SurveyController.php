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
        abort_unless(auth()->user()?->role?->slug === 'admin', 403);

        $surveys = Survey::with(['order', 'assignedUser'])
            ->latest()
            ->paginate(10);

        return view('surveys.index', compact('surveys'));
    }

    public function create()
    {
        abort_unless(auth()->user()?->role?->slug === 'admin', 403);

        $orders = Order::latest()->get();

        $officers = User::whereHas('role', function ($query) {
            $query->where('slug', 'petugas-lapangan');
        })->orderBy('name')->get();

        return view('surveys.create', compact('orders', 'officers'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()?->role?->slug === 'admin', 403);

        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'assigned_to' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->whereIn('role_id', function ($subquery) {
                        $subquery->select('id')
                            ->from('roles')
                            ->where('slug', 'petugas-lapangan');
                    });
                }),
            ],
            'scheduled_at' => ['nullable', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'status' => [
                'required',
                Rule::in([
                    'dijadwalkan',
                    'berlangsung',
                    'selesai',
                    'dibatalkan',
                ]),
            ],
            'findings' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        Survey::create($validated);

        return redirect()
            ->route('admin.surveys.index')
            ->with('success', 'Survey berhasil dibuat.');
    }

    public function show(Survey $survey)
    {
        abort_unless(auth()->user()?->role?->slug === 'admin', 403);

        $survey->load(['order', 'assignedUser']);

        $officers = User::whereHas('role', function ($query) {
            $query->where('slug', 'petugas-lapangan');
        })->orderBy('name')->get();

        return view('surveys.show', compact('survey', 'officers'));
    }

    public function update(Request $request, Survey $survey)
    {
        abort_unless(auth()->user()?->role?->slug === 'admin', 403);

        $validated = $request->validate([
            'assigned_to' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->whereIn('role_id', function ($subquery) {
                        $subquery->select('id')
                            ->from('roles')
                            ->where('slug', 'petugas-lapangan');
                    });
                }),
            ],
            'scheduled_at' => ['nullable', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'status' => [
                'required',
                Rule::in([
                    'dijadwalkan',
                    'berlangsung',
                    'selesai',
                    'dibatalkan',
                ]),
            ],
            'findings' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $survey->update($validated);

        return redirect()
            ->route('admin.surveys.show', $survey)
            ->with('success', 'Survey berhasil diperbarui.');
    }
}
