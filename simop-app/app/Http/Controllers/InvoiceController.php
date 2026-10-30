<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(
            in_array($role, ['admin', 'client'], true),
            403
        );

        $invoices = Invoice::with([
            'project.workEstimate.order',
            'creator',
        ])
            ->when(
                $role === 'client',
                fn ($query) => $query
                    ->whereIn('status', ['issued', 'paid'])
                    ->whereHas(
                        'project.workEstimate.order',
                        fn ($orderQuery) => $orderQuery->where(
                            'client_id',
                            $user->id
                        )
                    )
            )
            ->latest()
            ->paginate(10);

        return view(
            'invoices.index',
            compact('invoices')
        );
    }

    public function create()
    {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $projects = Project::with([
            'workEstimate.order',
            'planning.production.bapp',
        ])
            ->where('status', 'completed')
            ->whereDoesntHave('invoice')
            ->whereHas(
                'planning.production.bapp',
                fn ($query) => $query->where(
                    'status',
                    'approved'
                )
            )
            ->latest()
            ->get();

        return view(
            'invoices.create',
            compact('projects')
        );
    }

    public function store(Request $request)
    {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $validated = $request->validate([
            'project_id' => [
                'required',
                'integer',
                'exists:projects,id',
                'unique:invoices,project_id',
            ],
            'invoice_date' => [
                'required',
                'date',
            ],
            'due_date' => [
                'nullable',
                'date',
                'after_or_equal:invoice_date',
            ],
            'subtotal' => [
                'required',
                'numeric',
                'min:0',
            ],
            'tax_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $project = Project::with([
            'planning.production.bapp',
            'invoice',
        ])->findOrFail(
            $validated['project_id']
        );

        abort_unless(
            $project->status === 'completed',
            422,
            'Invoice hanya dapat dibuat untuk Project yang sudah selesai.'
        );

        $bapp = $project
            ->planning
            ?->production
            ?->bapp;

        abort_unless(
            $bapp
            && $bapp->status === 'approved',
            422,
            'Invoice hanya dapat dibuat setelah BAPP disetujui.'
        );

        abort_if(
            $project->invoice,
            422,
            'Project ini sudah memiliki Invoice.'
        );

        $subtotal = (float) $validated['subtotal'];
        $taxAmount = (float) ($validated['tax_amount'] ?? 0);
        $totalAmount = $subtotal + $taxAmount;

        $invoice = DB::transaction(
            function () use (
                $validated,
                $project,
                $subtotal,
                $taxAmount,
                $totalAmount
            ) {
                return Invoice::create([
                    'project_id' =>
                        $project->id,
                    'created_by' =>
                        auth()->id(),
                    'invoice_number' =>
                        $this->generateInvoiceNumber(),
                    'invoice_date' =>
                        $validated['invoice_date'],
                    'due_date' =>
                        $validated['due_date'] ?? null,
                    'subtotal' =>
                        $subtotal,
                    'tax_amount' =>
                        $taxAmount,
                    'total_amount' =>
                        $totalAmount,
                    'status' =>
                        'draft',
                    'issued_at' =>
                        null,
                    'paid_at' =>
                        null,
                    'notes' =>
                        $validated['notes'] ?? null,
                ]);
            }
        );

        return redirect()
            ->route(
                'invoices.show',
                $invoice
            )
            ->with(
                'success',
                'Invoice berhasil dibuat.'
            );
    }

    public function show(Invoice $invoice)
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        abort_unless(
            in_array(
                $role,
                ['admin', 'client'],
                true
            ),
            403
        );

        $invoice->load([
            'project.workEstimate.order',
            'project.planning.production.bapp',
            'project.rating',
            'creator',
        ]);

        if ($role === 'client') {
            abort_unless(
                in_array(
                    $invoice->status,
                    ['issued', 'paid'],
                    true
                ),
                403
            );

            abort_unless(
                $invoice
                    ->project
                    ?->workEstimate
                    ?->order
                    ?->client_id === $user->id,
                403
            );
        }

        return view(
            'invoices.show',
            compact('invoice')
        );
    }

    public function updateStatus(
        Request $request,
        Invoice $invoice
    ) {
        abort_unless(
            auth()->user()?->role?->slug === 'admin',
            403
        );

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'issued',
                    'paid',
                    'cancelled',
                ]),
            ],
        ]);

        $currentStatus = $invoice->status;
        $newStatus = $validated['status'];

        $allowedTransitions = [
            'draft' => [
                'issued',
                'cancelled',
            ],
            'issued' => [
                'paid',
                'cancelled',
            ],
            'paid' => [],
            'cancelled' => [],
        ];

        abort_unless(
            array_key_exists(
                $currentStatus,
                $allowedTransitions
            ),
            422,
            'Status Invoice saat ini tidak valid.'
        );

        abort_unless(
            in_array(
                $newStatus,
                $allowedTransitions[$currentStatus],
                true
            ),
            422,
            'Perubahan status Invoice tidak diizinkan.'
        );

        DB::transaction(
            function () use (
                $invoice,
                $newStatus
            ) {
                $data = [
                    'status' => $newStatus,
                ];

                if ($newStatus === 'issued') {
                    $data['issued_at'] = now();
                    $data['paid_at'] = null;
                }

                if ($newStatus === 'paid') {
                    $data['paid_at'] = now();
                }

                if ($newStatus === 'cancelled') {
                    $data['paid_at'] = null;
                }

                $invoice->update($data);
            }
        );

        $message = match ($newStatus) {
            'issued' =>
                'Invoice berhasil diterbitkan dan sekarang dapat dilihat oleh Client.',
            'paid' =>
                'Invoice berhasil ditandai lunas. Client sekarang dapat memberikan Rating dan ulasan.',
            'cancelled' =>
                'Invoice berhasil dibatalkan.',
            default =>
                'Status Invoice berhasil diperbarui.',
        };

        return redirect()
            ->route(
                'invoices.show',
                $invoice
            )
            ->with(
                'success',
                $message
            );
    }

    private function generateInvoiceNumber(): string
    {
        do {
            $number = 'INV-'
                . now()->format('YmdHis')
                . '-'
                . strtoupper(
                    substr(
                        uniqid(),
                        -4
                    )
                );
        } while (
            Invoice::where(
                'invoice_number',
                $number
            )->exists()
        );

        return $number;
    }
}