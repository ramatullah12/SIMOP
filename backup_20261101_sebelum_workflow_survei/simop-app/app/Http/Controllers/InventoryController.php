<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Project;
use App\Models\ProjectItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'direktur'], true), 403);

        $items = Item::with(['projectItems.project.order'])
            ->orderBy('item_code')
            ->get();

        $allocations = ProjectItem::with(['item', 'project.order'])
            ->latest()
            ->get();

        $activeProjects = Project::with('order')
            ->whereIn('status', ['planning', 'in_progress'])
            ->orderByDesc('created_at')
            ->get();

        return view('inventories.index', compact(
            'items',
            'allocations',
            'activeProjects',
            'role'
        ));
    }

    public function storeItem(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()?->role?->slug === 'admin', 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:50'],
            'total_stock' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $count = Item::count() + 1;
        $itemCode = 'BRG-' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
        while (Item::where('item_code', $itemCode)->exists()) {
            $count++;
            $itemCode = 'BRG-' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
        }

        Item::create([
            'item_code' => $itemCode,
            'name' => $validated['name'],
            'category' => $validated['category'] ?? 'Perlengkapan Operasional',
            'unit' => $validated['unit'],
            'total_stock' => $validated['total_stock'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('inventories.index')
            ->with('success', 'Data stok barang berhasil ditambahkan.');
    }

    public function updateItem(Request $request, Item $item): RedirectResponse
    {
        abort_unless(auth()->user()?->role?->slug === 'admin', 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:50'],
            'total_stock' => ['required', 'integer', 'min:' . $item->used_stock],
            'notes' => ['nullable', 'string'],
        ], [
            'total_stock.min' => 'Stok awal tidak boleh lebih kecil dari jumlah barang yang sedang dibawa proyek (' . $item->used_stock . ' ' . $item->unit . ').',
        ]);

        $item->update($validated);

        return redirect()
            ->route('inventories.index')
            ->with('success', 'Data stok barang berhasil diperbarui.');
    }

    public function destroyItem(Item $item): RedirectResponse
    {
        abort_unless(auth()->user()?->role?->slug === 'admin', 403);

        $item->delete();

        return redirect()
            ->route('inventories.index')
            ->with('success', 'Data barang berhasil dihapus.');
    }

    public function allocateToProject(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()?->role?->slug === 'admin', 403);

        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'item_id' => ['required', 'exists:items,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $project = Project::findOrFail($validated['project_id']);

        if (in_array($project->status, ['completed', 'cancelled'], true)) {
            return back()->with('error', 'Tidak dapat mengalokasikan barang ke proyek yang sudah selesai atau dibatalkan.');
        }

        return DB::transaction(function () use ($validated, $project) {
            $item = Item::lockForUpdate()->findOrFail($validated['item_id']);
            $available = $item->available_stock;

            if ($validated['quantity'] > $available) {
                return back()
                    ->withInput()
                    ->with('error', "Stok {$item->name} tidak mencukupi. Stok tersedia saat ini: {$available} {$item->unit} (Stok awal: {$item->total_stock} {$item->unit}, sedang dipakai: {$item->used_stock} {$item->unit}).");
            }

            ProjectItem::create([
                'project_id' => $project->id,
                'item_id' => $item->id,
                'quantity' => $validated['quantity'],
                'status' => 'allocated',
                'allocated_at' => now(),
                'notes' => $validated['notes'] ?? null,
            ]);

            $remaining = $available - $validated['quantity'];

            return back()->with(
                'success',
                "Berhasil membawa {$validated['quantity']} {$item->unit} {$item->name} ke proyek {$project->project_number}. Sisa stok tersedia sekarang: {$remaining} {$item->unit}."
            );
        });
    }

    public function returnStock(ProjectItem $projectItem): RedirectResponse
    {
        abort_unless(auth()->user()?->role?->slug === 'admin', 403);

        if ($projectItem->status === 'returned') {
            return back()->with('error', 'Barang ini sudah dikembalikan ke stok awal.');
        }

        $projectItem->update([
            'status' => 'returned',
            'returned_at' => now(),
        ]);

        $item = $projectItem->item;

        return back()->with(
            'success',
            "{$projectItem->quantity} {$item->unit} {$item->name} dari proyek {$projectItem->project->project_number} telah dikembalikan ke stok awal."
        );
    }

    public function destroyAllocation(ProjectItem $projectItem): RedirectResponse
    {
        abort_unless(auth()->user()?->role?->slug === 'admin', 403);

        $projectItem->delete();

        return back()->with('success', 'Data riwayat pemakaian barang berhasil dihapus.');
    }
}

