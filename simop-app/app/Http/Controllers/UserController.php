<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $search = trim((string) $request->query('search'));
        $roleId = $request->query('role_id');
        $verificationStatus = $request->query('verification_status');

        $users = User::with('role')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($roleId, fn ($query) => $query->where('role_id', $roleId))
            ->when($verificationStatus === 'unverified', fn ($query) => $query->whereNull('email_verified_at'))
            ->when($verificationStatus === 'verified', fn ($query) => $query->whereNotNull('email_verified_at'))
            ->orderByRaw('CASE WHEN email_verified_at IS NULL THEN 0 ELSE 1 END')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();
        $pendingVerificationCount = User::whereNull('email_verified_at')->count();

        return view('users.index', compact('users', 'roles', 'search', 'roleId', 'verificationStatus', 'pendingVerificationCount'));
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        $roles = Role::orderBy('name')->get();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil ditambahkan.');
    }

    public function show(Request $request, User $user): View
    {
        $this->authorizeAdmin($request);

        $user->load('role');

        return view('users.show', compact('user'));
    }

    public function edit(Request $request, User $user): View
    {
        $this->authorizeAdmin($request);

        $roles = Role::orderBy('name')->get();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin($request);

        if ($user->id === $request->user()?->id) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun yang sedang digunakan.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil dihapus.');
    }

    public function verify(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'role_id' => ['nullable', 'exists:roles,id'],
        ]);

        $roleId = $validated['role_id'] ?? $user->role_id;
        if (! $roleId) {
            $clientRole = Role::where('slug', 'client')->first();
            $roleId = $clientRole?->id;
        }

        $user->update([
            'role_id' => $roleId,
            'email_verified_at' => now(),
        ]);

        return back()->with('success', "Akun pengguna {$user->name} ({$user->email}) berhasil diverifikasi dan aktif.");
    }

    public function unverify(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin($request);

        if ($user->id === $request->user()?->id) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan/membatalkan verifikasi akun Anda sendiri.');
        }

        $user->update([
            'email_verified_at' => null,
        ]);

        return back()->with('success', "Verifikasi akun {$user->name} berhasil dibatalkan (akun dinonaktifkan sementara).");
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role?->slug === 'admin', 403);
    }
}

