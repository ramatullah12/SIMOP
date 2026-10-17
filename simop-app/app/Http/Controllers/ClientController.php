<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->role?->slug === 'admin', 403);

        $clients = Client::with('user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');

                $query->where(function ($query) use ($search) {
                    $query->where('company_name', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('clients.index', compact('clients'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->role?->slug === 'admin', 403);

        return view('clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->role?->slug === 'admin', 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email', 'unique:clients,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'company_type' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $clientRole = Role::where('slug', 'client')->firstOrFail();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $clientRole->id,
            'email_verified_at' => now(),
        ]);

        Client::create([
            'user_id' => $user->id,
            'company_name' => $validated['company_name'],
            'contact_person' => $validated['contact_person'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'company_type' => $validated['company_type'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'aktif',
        ]);

        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Data Client berhasil ditambahkan.');
    }

    public function show(Request $request, Client $client): View
    {
        abort_unless($request->user()->role?->slug === 'admin', 403);

        $client->load(['user', 'orders']);

        return view('clients.show', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        abort_unless($request->user()->role?->slug === 'admin', 403);

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('clients', 'email')->ignore($client->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'company_type' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($client->user && $validated['email'] !== $client->user->email) {
            $request->validate([
                'email' => [
                    'unique:users,email,' . $client->user->id,
                ],
            ]);

            $client->user->update(['email' => $validated['email']]);
        }

        $client->update($validated);

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('success', 'Data Client berhasil diperbarui.');
    }
}
