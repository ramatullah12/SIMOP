<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'client', 'direktur'], true), 403);

        $search = trim((string) $request->query('search'));
        $statusFilter = $request->query('status');
        $clientIdFilter = $request->query('client_id');

        $orders = Order::with(['client', 'survey.assignedUser', 'survey.approver', 'project'])
            ->when($role === 'client', function ($query) use ($user) {
                // Pelanggan hanya dapat melihat pesanan miliknya sendiri
                $query->where('client_id', $user->id);
            })
            ->when($role !== 'client' && $clientIdFilter, function ($query) use ($clientIdFilter) {
                $query->where('client_id', $clientIdFilter);
            })
            ->when($statusFilter, function ($query) use ($statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%")
                        ->orWhere('event_name', 'like', "%{$search}%")
                        ->orWhere('event_location', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $clients = ($role !== 'client')
            ? User::whereHas('role', fn ($q) => $q->where('slug', 'client'))->orderBy('name')->get()
            : collect();

        $statuses = [
            'masuk' => 'Masuk',
            'diproses' => 'Diproses',
            'menunggu_survey' => 'Menunggu Survei',
            'survey_selesai' => 'Survei Selesai',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'selesai' => 'Selesai',
        ];

        return view('orders.index', compact('orders', 'role', 'clients', 'statuses', 'search', 'statusFilter', 'clientIdFilter'));
    }

    public function create(Request $request): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        // Admin dan Client sama-sama berhak membuat pesanan baru
        abort_unless(in_array($role, ['admin', 'client'], true), 403);

        $selectedClientId = $request->query('client_id');

        // Admin memilih dari daftar pelanggan yang sudah terdaftar
        $clients = ($role === 'admin')
            ? User::whereHas('role', fn ($q) => $q->where('slug', 'client'))->orderBy('name')->get()
            : collect();

        return view('orders.create', compact('role', 'clients', 'selectedClientId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        // Admin dan Client sama-sama berhak menyimpan pesanan baru
        abort_unless(in_array($role, ['admin', 'client'], true), 403);

        $rules = [
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'event_name' => ['required', 'string', 'max:255'],
            'event_location' => ['required', 'string', 'max:255'],
            'event_date' => ['nullable', 'date'],
            'description' => ['required', 'string'],
        ];

        if ($role === 'admin') {
            $rules['client_id'] = [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $isClient = User::where('id', $value)
                        ->whereHas('role', fn ($q) => $q->where('slug', 'client'))
                        ->exists();
                    if (! $isClient) {
                        $fail('Pelanggan yang dipilih tidak valid atau bukan akun Klien.');
                    }
                },
            ];
            $rules['status'] = ['nullable', 'in:masuk,diproses,menunggu_survey'];
            $rules['admin_notes'] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules);

        // Menghubungkan otomatis ID pelanggan yang benar
        if ($role === 'admin') {
            $targetClientId = (int) $validated['client_id'];
            $clientUser = User::find($targetClientId);
            $email = $validated['email'] ?? $clientUser?->email;
            $status = $validated['status'] ?? 'masuk';
            $adminNotes = $validated['admin_notes'] ?? null;
        } else {
            // Pelanggan otomatis terhubung ke akun sendiri
            $targetClientId = $user->id;
            $email = $validated['email'] ?? $user->email;
            $status = 'masuk';
            $adminNotes = null;
        }

        $order = Order::create([
            'order_number' => $this->generateOrderNumber(),
            'client_id' => $targetClientId,
            'company_name' => $validated['company_name'],
            'contact_person' => $validated['contact_person'],
            'phone' => $validated['phone'],
            'email' => $email,
            'event_name' => $validated['event_name'],
            'event_location' => $validated['event_location'],
            'event_date' => $validated['event_date'] ?? null,
            'description' => $validated['description'],
            'estimated_budget' => null,
            'status' => $status,
            'admin_notes' => $adminNotes,
        ]);

        $message = ($role === 'admin')
            ? 'Pesanan untuk pelanggan berhasil dibuat oleh Admin.'
            : 'Pesanan Anda berhasil dikirim ke Admin. Admin akan meninjau kebutuhan dan menjadwalkan survei lokasi.';

        return redirect()
            ->route('orders.show', $order)
            ->with('success', $message);
    }

    public function show(Order $order): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'client', 'direktur'], true), 403);

        // Pelanggan hanya dapat melihat pesanan miliknya sendiri
        if ($role === 'client' && $order->client_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat pesanan ini.');
        }

        $order->load(['client', 'survey.assignedUser', 'survey.approver', 'project']);

        return view('orders.show', compact('order', 'role'));
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

        $clients = ($role === 'admin')
            ? User::whereHas('role', fn ($q) => $q->where('slug', 'client'))->orderBy('name')->get()
            : collect();

        $statuses = [
            'masuk' => 'Masuk',
            'diproses' => 'Diproses',
            'menunggu_survey' => 'Menunggu Survei',
            'survey_selesai' => 'Survei Selesai',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'selesai' => 'Selesai',
        ];

        return view('orders.edit', compact('order', 'role', 'clients', 'statuses'));
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
                ->with('success', 'Data pesanan berhasil diperbarui.');
        }

        // Admin dapat memperbarui seluruh data pesanan termasuk client_id, status, dan catatan admin
        $validated = $request->validate([
            'client_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $isClient = User::where('id', $value)
                        ->whereHas('role', fn ($q) => $q->where('slug', 'client'))
                        ->exists();
                    if (! $isClient) {
                        $fail('Pelanggan yang dipilih tidak valid atau bukan akun Klien.');
                    }
                },
            ],
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
            ->with('success', 'Data pesanan berhasil diperbarui oleh Admin.');
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
            ->with('success', 'Data pesanan berhasil dihapus.');
    }

    private function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $count = Order::whereDate('created_at', today())->count() + 1;

        return 'ORD-' . $date . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
