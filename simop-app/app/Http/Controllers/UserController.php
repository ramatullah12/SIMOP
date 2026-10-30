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
        $roleId = $request->query('role');

        $users = User::with('role')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(
                $roleId,
                fn ($query) => $query->where('role_id', $roleId)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $roles = $this->availableRoles();

        return view(
            'users.index',
            compact(
                'users',
                'roles',
                'search',
                'roleId'
            )
        );
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        $roles = $this->availableRoles();

        return view(
            'users.create',
            compact('roles')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $allowedRoleIds = $this->availableRoles()
            ->pluck('id')
            ->all();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'role_id' => [
                'required',
                Rule::in($allowedRoleIds),
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function show(Request $request, User $user): View
    {
        $this->authorizeAdmin($request);

        $user->load('role');

        return view(
            'users.show',
            compact('user')
        );
    }

    public function edit(Request $request, User $user): View
    {
        $this->authorizeAdmin($request);

        $roles = $this->availableRoles();

        return view(
            'users.edit',
            compact('user', 'roles')
        );
    }

    public function update(
        Request $request,
        User $user
    ): RedirectResponse {
        $this->authorizeAdmin($request);

        $allowedRoleIds = $this->availableRoles()
            ->pluck('id')
            ->all();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role_id' => [
                'required',
                Rule::in($allowedRoleIds),
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        if (
            $request->user()->is($user)
            && (int) $validated['role_id'] !== (int) $user->role_id
        ) {
            return back()
                ->withErrors([
                    'role_id' => 'Role akun yang sedang digunakan tidak dapat diubah.',
                ])
                ->withInput();
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function updateVerification(
        Request $request,
        User $user
    ): RedirectResponse {
        $this->authorizeAdmin($request);

        if ($request->user()->is($user)) {
            return back()
                ->withErrors([
                    'verification' => 'Akun yang sedang digunakan tidak dapat diubah status verifikasinya.',
                ]);
        }

        if ($user->email_verified_at) {
            $user->forceFill([
                'email_verified_at' => null,
            ])->save();

            return back()->with(
                'success',
                'Verifikasi pengguna berhasil dibatalkan.'
            );
        }

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        return back()->with(
            'success',
            'Pengguna berhasil diverifikasi.'
        );
    }

    private function availableRoles()
    {
        return Role::whereIn('slug', [
            'admin',
            'direktur',
            'petugas-lapangan',
            'client',
        ])
            ->orderBy('name')
            ->get();
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(
            $request->user()?->role?->slug === 'admin',
            403
        );
    }
}