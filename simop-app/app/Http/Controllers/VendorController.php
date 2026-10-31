<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VendorController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $search = trim((string) $request->query('search'));
        $status = $request->query('status');

        $vendors = Vendor::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('vendor_code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('service_type', 'like', "%{$search}%");
                });
            })
            ->when(
                in_array($status, ['active', 'inactive'], true),
                fn ($query) => $query->where('status', $status)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'vendors.index',
            compact('vendors', 'search', 'status')
        );
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('vendors.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'service_type' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string'],
        ]);

        $vendor = DB::transaction(function () use ($validated) {
            return Vendor::create([
                ...$validated,
                'vendor_code' => $this->generateVendorCode(),
            ]);
        });

        return redirect()
            ->route('admin.vendors.show', $vendor)
            ->with('success', 'Data mitra kerja berhasil ditambahkan.');
    }

    public function show(Request $request, Vendor $vendor): View
    {
        $this->authorizeAdmin($request);

        return view(
            'vendors.show',
            compact('vendor')
        );
    }

    public function edit(Request $request, Vendor $vendor): View
    {
        $this->authorizeAdmin($request);

        return view(
            'vendors.edit',
            compact('vendor')
        );
    }

    public function update(
        Request $request,
        Vendor $vendor
    ): RedirectResponse {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'service_type' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string'],
        ]);

        $vendor->update($validated);

        return redirect()
            ->route('admin.vendors.show', $vendor)
            ->with('success', 'Data mitra kerja berhasil diperbarui.');
    }

    public function destroy(Request $request, Vendor $vendor): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $vendor->delete();

        return redirect()
            ->route('admin.vendors.index')
            ->with('success', 'Data mitra kerja berhasil dihapus.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(
            $request->user()?->role?->slug === 'admin',
            403
        );
    }

    private function generateVendorCode(): string
    {
        do {
            $code = 'VND-'
                . now()->format('YmdHis')
                . '-'
                . strtoupper(substr(uniqid(), -4));
        } while (
            Vendor::where('vendor_code', $code)->exists()
        );

        return $code;
    }
}
