<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $role = $request->user()?->role?->slug;
        abort_unless(in_array($role, ['admin', 'direktur'], true), 403);

        $search = trim((string) $request->query('search'));
        $status = $request->query('status');

        $employees = Employee::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('employee_code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(in_array($status, ['aktif', 'nonaktif'], true), fn ($query) => $query->where('status', $status))
            ->orderBy('employee_code')
            ->paginate(10)
            ->withQueryString();

        return view('employees.index', compact('employees', 'search', 'status', 'role'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()?->role?->slug === 'admin', 403);

        return view('employees.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->role?->slug === 'admin', 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'join_date' => ['nullable', 'date'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'notes' => ['nullable', 'string'],
        ]);

        $count = Employee::count() + 1;
        $code = 'PGW-' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
        while (Employee::where('employee_code', $code)->exists()) {
            $count++;
            $code = 'PGW-' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
        }

        Employee::create([
            ...$validated,
            'employee_code' => $code,
        ]);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    public function show(Request $request, Employee $employee): View
    {
        $role = $request->user()?->role?->slug;
        abort_unless(in_array($role, ['admin', 'direktur'], true), 403);

        return view('employees.show', compact('employee', 'role'));
    }

    public function edit(Request $request, Employee $employee): View
    {
        abort_unless($request->user()?->role?->slug === 'admin', 403);

        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        abort_unless($request->user()?->role?->slug === 'admin', 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'join_date' => ['nullable', 'date'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'notes' => ['nullable', 'string'],
        ]);

        $employee->update($validated);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(Request $request, Employee $employee): RedirectResponse
    {
        abort_unless($request->user()?->role?->slug === 'admin', 403);

        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', 'Data pegawai berhasil dihapus.');
    }
}

