<?php

namespace App\Http\Controllers;


use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $role = $user->role?->slug;

        abort_unless(
            in_array($role, ['admin', 'client'], true),
            403
        );

        $orders = Order::query()
            ->when($role === 'client', function ($query) use ($user) {
                $query->where('client_id', $user->id);
            })
            ->with('client')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders', 'role'));
    }

    public function create(Request $request): View
    {
        abort_unless(
            $request->user()->role?->slug === 'client',
            403
        );

        return view('orders.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(
            $request->user()->role?->slug === 'client',
            403
        );

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
            'event_name' => ['required', 'string', 'max:255'],
            'event_location' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date', 'after_or_equal:today'],
            'description' => ['required', 'string'],
            'estimated_budget' => ['nullable', 'numeric', 'min:0'],
        ]);

        $validated['order_number'] = 'RFQ-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        $validated['client_id'] = $request->user()->id;
        $validated['status'] = 'baru';

        Order::create($validated);

        return redirect()
            ->route('client.orders.index')
            ->with('success', 'Order berhasil diajukan.');
    }

    public function show(Request $request, Order $order): View
    {
        $user = $request->user();
        $role = $user->role?->slug;

        abort_unless(
            $role === 'admin' ||
            ($role === 'client' && $order->client_id === $user->id),
            403
        );

        $order->load('client');

        return view('orders.show', compact('order', 'role'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        abort_unless(
            $request->user()->role?->slug === 'admin',
            403
        );

        $validated = $request->validate([
            'status' => [
                'required',
                'in:baru,diperiksa,perlu_revisi,ditolak,menunggu_survey',
            ],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $order->update($validated);

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Status order berhasil diperbarui.');
    }
}
