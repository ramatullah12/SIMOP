<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SurveyController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'direktur', 'petugas-lapangan', 'client'], true), 403);

        $surveys = Survey::with(['order.client', 'assignedUser', 'approver', 'requester'])
            ->when($role === 'client', function ($query) use ($user) {
                $query->whereHas('order', function ($q) use ($user) {
                    $q->where('client_id', $user->id);
                });
            })
            ->when($role === 'petugas-lapangan', function ($query) use ($user) {
                $query->where('assigned_to', $user->id)
                    ->whereNotNull('approved_at')
                    ->whereIn('status', ['dijadwalkan', 'berlangsung', 'selesai']);
            })
            ->latest()
            ->paginate(10);

        return view('surveys.index', compact('surveys', 'role'));
    }

    public function create(Request $request): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'client'], true), 403);

        if ($role === 'client') {
            $orders = Order::where('client_id', $user->id)
                ->whereDoesntHave('surveys', function ($q) {
                    $q->whereNotIn('status', ['dibatalkan', 'ditolak_direktur']);
                })
                ->latest()
                ->get();

            $fieldOfficers = collect();
        } else {
            $orders = Order::with('client')
                ->whereIn('status', ['masuk', 'diproses', 'menunggu_survey'])
                ->latest()
                ->get();

            $fieldOfficers = User::whereHas('role', function ($query) {
                $query->where('slug', 'petugas-lapangan');
            })->orderBy('name')->get();
        }

        $selectedOrderId = $request->query('order_id');

        return view('surveys.create', compact('orders', 'fieldOfficers', 'role', 'selectedOrderId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'client'], true), 403);

        if ($role === 'client') {
            $validated = $request->validate([
                'order_id' => ['required', 'exists:orders,id'],
                'location' => ['required', 'string', 'max:255'],
                'scheduled_at' => ['nullable', 'date'],
                'notes' => ['nullable', 'string'],
            ]);

            $order = Order::where('id', $validated['order_id'])
                ->where('client_id', $user->id)
                ->firstOrFail();

            $survey = Survey::create([
                'order_id' => $order->id,
                'requested_by' => $user->id,
                'scheduled_at' => $validated['scheduled_at'] ?? null,
                'location' => $validated['location'],
                'status' => 'diajukan_client',
                'notes' => $validated['notes'] ?? null,
            ]);

            $order->update(['status' => 'menunggu_survey']);

            return redirect()
                ->route('surveys.show', $survey)
                ->with('success', 'Pengajuan survei berhasil dikirim ke Admin untuk diteruskan ke Direktur.');
        }

        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'assigned_to' => ['required', 'exists:users,id'],
            'scheduled_at' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $survey = Survey::create([
            'order_id' => $validated['order_id'],
            'requested_by' => $user->id,
            'assigned_to' => $validated['assigned_to'],
            'scheduled_at' => $validated['scheduled_at'],
            'location' => $validated['location'],
            'status' => 'menunggu_persetujuan_direktur',
            'submitted_to_direktur_at' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        $survey->order->update([
            'status' => 'menunggu_survey',
        ]);

        return redirect()
            ->route('surveys.show', $survey)
            ->with('success', 'Pengajuan survei telah dikirim ke Direktur. Menunggu persetujuan Direktur untuk menerbitkan Surat Perintah Survei ke Petugas Lapangan.');
    }

    public function show(Survey $survey): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'direktur', 'petugas-lapangan', 'client'], true), 403);

        if ($role === 'client') {
            abort_unless($survey->order?->client_id === $user->id, 403);
        }

        if ($role === 'petugas-lapangan') {
            abort_unless(
                $survey->assigned_to === $user->id && $survey->approved_at !== null,
                403,
                'Surat Perintah Survei belum diterbitkan atau belum disetujui Direktur.'
            );
        }

        $survey->load(['order.client', 'assignedUser', 'approver', 'requester', 'project']);

        $fieldOfficers = collect();
        if ($role === 'admin') {
            $fieldOfficers = User::whereHas('role', function ($query) {
                $query->where('slug', 'petugas-lapangan');
            })->orderBy('name')->get();
        }

        return view('surveys.show', compact('survey', 'role', 'fieldOfficers'));
    }

    public function edit(Survey $survey): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'petugas-lapangan'], true), 403);

        if ($role === 'petugas-lapangan') {
            abort_unless(
                $survey->assigned_to === $user->id && $survey->approved_at !== null,
                403,
                'Surat Perintah Survei belum disetujui Direktur.'
            );
        }

        $survey->load(['order.client', 'assignedUser', 'approver']);

        $orders = Order::with('client')->latest()->get();

        $fieldOfficers = User::whereHas('role', function ($query) {
            $query->where('slug', 'petugas-lapangan');
        })->orderBy('name')->get();

        return view('surveys.edit', compact('survey', 'orders', 'fieldOfficers', 'role'));
    }

    public function update(Request $request, Survey $survey): RedirectResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'petugas-lapangan'], true), 403);

        if ($role === 'petugas-lapangan') {
            abort_unless(
                $survey->assigned_to === $user->id && $survey->approved_at !== null,
                403,
                'Surat Perintah Survei belum disetujui Direktur.'
            );

            $validated = $request->validate([
                'status' => ['required', 'in:dijadwalkan,berlangsung,selesai'],
                'findings' => ['required_if:status,selesai', 'nullable', 'string'],
                'notes' => ['nullable', 'string'],
            ], [
                'findings.required_if' => 'Hasil temuan survei lapangan wajib diisi saat menyelesaikan survei.',
            ]);

            $survey->update($validated);

            if ($validated['status'] === 'selesai') {
                $survey->order->update([
                    'status' => 'survey_selesai',
                ]);
            }

            return redirect()
                ->route('surveys.show', $survey)
                ->with('success', 'Laporan hasil pelaksanaan Surat Perintah Survei berhasil diperbarui oleh Petugas Lapangan.');
        }

        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'assigned_to' => ['required', 'exists:users,id'],
            'scheduled_at' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string'],
            'findings' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $nextStatus = $survey->approved_at
            ? ($validated['status'] ?? $survey->status)
            : 'menunggu_persetujuan_direktur';

        $survey->update([
            'order_id' => $validated['order_id'],
            'assigned_to' => $validated['assigned_to'],
            'scheduled_at' => $validated['scheduled_at'],
            'location' => $validated['location'],
            'status' => $nextStatus,
            'submitted_to_direktur_at' => $survey->submitted_to_direktur_at ?? now(),
            'findings' => $validated['findings'] ?? $survey->findings,
            'notes' => $validated['notes'] ?? $survey->notes,
        ]);

        if ($nextStatus === 'selesai') {
            $survey->order->update(['status' => 'survey_selesai']);
        } else {
            $survey->order->update(['status' => 'menunggu_survey']);
        }

        return redirect()
            ->route('surveys.show', $survey)
            ->with('success', $survey->approved_at
                ? 'Data survei berhasil diperbarui oleh Admin.'
                : 'Pengajuan survei berhasil diteruskan oleh Admin ke Direktur untuk persetujuan Surat Perintah Survei.');
    }

    public function submitToDirektur(Request $request, Survey $survey): RedirectResponse
    {
        abort_unless(auth()->user()?->role?->slug === 'admin', 403);

        $validated = $request->validate([
            'assigned_to' => ['required', 'exists:users,id'],
            'scheduled_at' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ], [
            'assigned_to.required' => 'Pilih Petugas Lapangan yang akan ditugaskan.',
            'scheduled_at.required' => 'Tentukan jadwal pelaksanaan survei.',
        ]);

        $survey->update([
            'assigned_to' => $validated['assigned_to'],
            'scheduled_at' => $validated['scheduled_at'],
            'location' => $validated['location'],
            'notes' => $validated['notes'] ?? $survey->notes,
            'status' => 'menunggu_persetujuan_direktur',
            'submitted_to_direktur_at' => now(),
            'rejection_notes' => null,
        ]);

        $survey->order->update([
            'status' => 'menunggu_survey',
        ]);

        return redirect()
            ->route('surveys.show', $survey)
            ->with('success', 'Pengajuan survei berhasil dikirim ke Direktur. Menunggu Direktur menyetujui Surat Perintah Survei (SPS).');
    }

    public function approveByDirektur(Request $request, Survey $survey): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user?->role?->slug === 'direktur', 403);

        if (! $survey->assigned_to) {
            return back()->with('error', 'Admin belum memilih Petugas Lapangan untuk Surat Perintah Survei ini.');
        }

        $spsNumber = $survey->sps_number ?: ('SPS-' . now()->format('Ymd') . '-' . str_pad((string) $survey->id, 4, '0', STR_PAD_LEFT));

        $survey->update([
            'sps_number' => $spsNumber,
            'status' => 'dijadwalkan',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'rejection_notes' => null,
            'notes' => $request->filled('notes') ? $request->input('notes') : $survey->notes,
        ]);

        $survey->order->update([
            'status' => 'menunggu_survey',
        ]);

        return redirect()
            ->route('surveys.show', $survey)
            ->with('success', "Pengajuan survei disetujui Direktur! Surat Perintah Survei ({$spsNumber}) resmi diterbitkan dan dikirim ke Petugas Lapangan ({$survey->assignedUser?->name}).");
    }

    public function rejectByDirektur(Request $request, Survey $survey): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user?->role?->slug === 'direktur', 403);

        $validated = $request->validate([
            'rejection_notes' => ['required', 'string'],
        ], [
            'rejection_notes.required' => 'Alasan penolakan atau revisi wajib diisi.',
        ]);

        $survey->update([
            'status' => 'ditolak_direktur',
            'rejection_notes' => $validated['rejection_notes'],
        ]);

        return redirect()
            ->route('surveys.show', $survey)
            ->with('success', 'Pengajuan survei dikembalikan ke Admin dengan catatan revisi Direktur.');
    }

    public function destroy(Survey $survey): RedirectResponse
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless($role === 'admin', 403);

        $survey->delete();

        return redirect()
            ->route('surveys.index')
            ->with('success', 'Data survei berhasil dihapus.');
    }
}
