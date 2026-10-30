<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Project;
use App\Models\Rating;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAccess($request);

        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = isset($validated['start_date'])
            ? Carbon::parse($validated['start_date'])->startOfDay()
            : now()->startOfMonth();

        $endDate = isset($validated['end_date'])
            ? Carbon::parse($validated['end_date'])->endOfDay()
            : now()->endOfMonth();

        $totalOrders = Order::whereBetween(
            'created_at',
            [$startDate, $endDate]
        )->count();

        $totalProjects = Project::whereBetween(
            'created_at',
            [$startDate, $endDate]
        )->count();

        $completedProjects = Project::where('status', 'completed')
            ->whereBetween(
                'updated_at',
                [$startDate, $endDate]
            )
            ->count();

        $totalInvoiceValue = Invoice::whereIn(
            'status',
            ['issued', 'paid']
        )
            ->whereBetween(
                'invoice_date',
                [
                    $startDate->toDateString(),
                    $endDate->toDateString(),
                ]
            )
            ->sum('total_amount');

        $paidInvoiceValue = Invoice::where('status', 'paid')
            ->whereBetween(
                'invoice_date',
                [
                    $startDate->toDateString(),
                    $endDate->toDateString(),
                ]
            )
            ->sum('total_amount');

        $outstandingInvoiceValue = Invoice::where('status', 'issued')
            ->whereBetween(
                'invoice_date',
                [
                    $startDate->toDateString(),
                    $endDate->toDateString(),
                ]
            )
            ->sum('total_amount');

        $averageRating = Rating::whereBetween(
            'created_at',
            [$startDate, $endDate]
        )->avg('rating');

        $averageRating = round((float) ($averageRating ?? 0), 1);

        $recentProjects = Project::with([
            'workEstimate.order',
            'invoice',
        ])
            ->whereBetween(
                'created_at',
                [$startDate, $endDate]
            )
            ->latest()
            ->limit(10)
            ->get();

        $recentInvoices = Invoice::with([
            'project.workEstimate.order',
        ])
            ->whereBetween(
                'invoice_date',
                [
                    $startDate->toDateString(),
                    $endDate->toDateString(),
                ]
            )
            ->latest('invoice_date')
            ->limit(10)
            ->get();

        return view(
            'reports.index',
            compact(
                'startDate',
                'endDate',
                'totalOrders',
                'totalProjects',
                'completedProjects',
                'totalInvoiceValue',
                'paidInvoiceValue',
                'outstandingInvoiceValue',
                'averageRating',
                'recentProjects',
                'recentInvoices'
            )
        );
    }

    private function authorizeAccess(Request $request): void
    {
        abort_unless(
            in_array(
                $request->user()?->role?->slug,
                ['admin', 'direktur'],
                true
            ),
            403
        );
    }
}