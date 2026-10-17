<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Survey;
use App\Models\WorkEstimate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class WorkEstimateController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()?->role?->slug === 'admin', 403);

        $estimates = WorkEstimate::with(['order', 'creator'])
            ->latest()
            ->paginate(10);

        return view('work-estimates.index', compact('estimates'));
    }

    public function create()
    {
        abort_unless(auth()->user()?->role?->slug === 'admin', 403);

        $orders = Order::latest()->get();
        $surveys = Survey::with('order')->latest()->get();

        return view('work-estimates.create', compact('orders', 'surveys'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()?->role?->slug === 'admin', 403);

        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'survey_id' => [
                'nullable',
                Rule::exists('surveys', 'id')->where(
                    fn ($query) => $query->where(
                        'order_id',
                        $request->input('order_id')
                    )
                ),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit' => ['required', 'string', 'max:30'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $estimate = DB::transaction(function () use ($validated) {
            $subtotal = 0;

            foreach ($validated['items'] as $item) {
                $subtotal += (float) $item['quantity']
                    * (float) $item['unit_price'];
            }

            $taxAmount = round(
                $subtotal * (float) $validated['tax_rate'] / 100,
                2
            );

            $estimate = WorkEstimate::create([
                'order_id' => $validated['order_id'],
                'survey_id' => $validated['survey_id'] ?? null,
                'created_by' => auth()->id(),
                'estimate_number' => 'WE-'
                    . now()->format('YmdHis')
                    . '-'
                    . strtoupper(Str::random(4)),
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $subtotal + $taxAmount,
                'status' => 'draft',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];

                $estimate->items()->create([
                    'item_name' => $item['item_name'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $quantity,
                    'unit' => $item['unit'],
                    'unit_price' => $unitPrice,
                    'total_price' => round(
                        $quantity * $unitPrice,
                        2
                    ),
                ]);
            }

            return $estimate;
        });

        return redirect()
            ->route('admin.work-estimates.show', $estimate)
            ->with('success', 'Estimasi biaya berhasil dibuat.');
    }

    public function show(WorkEstimate $workEstimate)
    {
        abort_unless(auth()->user()?->role?->slug === 'admin', 403);

        $workEstimate->load([
            'order',
            'survey',
            'creator',
            'items',
        ]);

        return view('work-estimates.show', [
            'estimate' => $workEstimate,
        ]);
    }
}
