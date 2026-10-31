<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Survey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'client', 'direktur'], true), 403);

        $orders = Order::with(['client', 'survey.assignedUser', 'survey.approver'])
            ->when($role === 'client', function ($query) use ($user) {
                $query->where('client_id', $user->id);
            })
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function create(): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless($role === 'client', 403);

        return view('orders.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless($role === 'client', 403);

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'event_name' => ['required', 'string', 'max:255'],
            'event_location' => ['required', 'string', 'max:255'],
            'event_date' => ['nullable', 'date'],
            'description' => ['required', 'string'],
        ]);

        $order = Order::create([
            'order_number' => $this->generateOrderNumber(),
            'client_id' => $user->id,
            'company_name' => $validated['company_name'],
            'contact_person' => $validated['contact_person'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? $user->email,
            'event_name' => $validated['event_name'],
            'event_location' => $validated['event_location'],
            'event_date' => $validated['event_date'] ?? null,
            'description' => $validated['description'],
            'estimated_budget' => null,
            'status' => 'menunggu_survey',
        ]);

        // Otomatis membuat pengajuan survey dari Client ke Admin
        Survey::create([
            'order_id' => $order->id,
            'requested_by' => $user->id,
            'location' => $validated['event_location'],
            'status' => 'diajukan_client',
            'notes' => 'Pengajuan survey awal dari Client: ' . $validated['description'],
        ]);

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order & Pengajuan Survey berhasil dikirim ke Admin untuk diteruskan ke Direktur.');
    }

    public function show(Order $order): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'client', 'direktur'], true), 403);

        if ($role === 'client' && $order->client_id !== $user->id) {
            abort(403);
        }

        $order->load(['client', 'survey.assignedUser', 'survey.approver', 'project']);

        return view('orders.show', compact('order'));
    }

    public function edit(Order $order): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'client'], true), 403);

        if ($role === 'client') {
            abort_unless($order->client_id === $user->id, 403);
            abort_unless(in_array($order->status, ['masuk', 'menunggu_survey', 'ditolak'], true), 403);
        }

        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'client'], true), 403);

        if ($role === 'client') {
            abort_unless($order->client_id === $user->id, 403);
            abort_unless(in_array($order->status, ['masuk', 'menunggu_survey', 'ditolak'], true), 403);

            $validated = $request->validate([
                'company_name' => ['required', 'string', 'max:255'],
                'contact_person' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:30'],
                'email' => ['nullable', 'email', 'max:255'],
                'event_name' => ['required', 'string', 'max:255'],
                'event_location' => ['required', 'string', 'max:255'],
                'event_date' => ['nullable', 'date'],
                'description' => ['required', 'string'],
            ]);

            $order->update($validated);

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Order berhasil diperbarui.');
        }

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'event_name' => ['required', 'string', 'max:255'],
            'event_location' => ['required', 'string', 'max:255'],
            'event_date' => ['nullable', 'date'],
            'description' => ['required', 'string'],
            'status' => ['required', 'in:masuk,diproses,menunggu_survey,survey_selesai,disetujui,ditolak,selesai'],
            'admin_notes' => ['nullable', 'string'],
        ]);

        $order->update($validated);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order berhasil diperbarui oleh admin.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'client'], true), 403);

        if ($role === 'client') {
            abort_unless($order->client_id === $user->id, 403);
            abort_unless(in_array($order->status, ['masuk', 'menunggu_survey'], true), 403);
        }

        $order->delete();

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order berhasil dihapus.');
    }

    private function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $count = Order::whereDate('created_at', today())->count() + 1;

        return 'ORD-' . $date . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
