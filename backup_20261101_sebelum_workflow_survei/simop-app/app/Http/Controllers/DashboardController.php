<?php

namespace App\Http\Controllers;

use App\Models\FieldDocumentation;
use App\Models\Item;
use App\Models\Order;
use App\Models\Production;
use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\Survey;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $role = $user?->role?->slug;

        return match ($role) {
            'admin' => $this->adminDashboard(),
            'direktur' => $this->direkturDashboard(),
            'petugas-lapangan' => $this->petugasDashboard($user),
            'client' => $this->clientDashboard($user),
            default => abort(403),
        };
    }

    private function adminDashboard(): View
    {
        $stats = [
            'orders_total' => Order::count(),
            'surveys_waiting_admin' => Survey::where('status', 'diajukan_client')->count(),
            'surveys_waiting_direktur' => Survey::where('status', 'menunggu_persetujuan_direktur')->count(),
            'projects_active' => Project::whereIn('status', ['planning', 'in_progress'])->count(),
            'projects_completed' => Project::where('status', 'completed')->count(),
            'items_count' => Item::count(),
        ];

        $items = Item::with('activeProjectItems')->orderBy('item_code')->get();
        $recentSurveys = Survey::with(['order.client', 'assignedUser'])->latest()->take(5)->get();

        return view('dashboard.admin', compact('stats', 'items', 'recentSurveys'));
    }

    private function direkturDashboard(): View
    {
        $pendingSurveys = Survey::with(['order.client', 'assignedUser'])
            ->where('status', 'menunggu_persetujuan_direktur')
            ->latest()
            ->get();

        $items = Item::with(['projectItems.project.order'])
            ->orderBy('item_code')
            ->get();

        $recentAllocations = ProjectItem::with(['item', 'project.order'])
            ->latest()
            ->take(8)
            ->get();

        $stats = [
            'pending_survey_approvals' => $pendingSurveys->count(),
            'active_projects' => Project::whereIn('status', ['planning', 'in_progress'])->count(),
            'completed_projects' => Project::where('status', 'completed')->count(),
        ];

        return view('dashboard.direktur', compact(
            'stats',
            'pendingSurveys',
            'items',
            'recentAllocations'
        ));
    }

    private function petugasDashboard($user): View
    {
        $activeSps = Survey::with(['order.client', 'approver'])
            ->where('assigned_to', $user->id)
            ->whereNotNull('approved_at')
            ->whereIn('status', ['dijadwalkan', 'berlangsung', 'selesai'])
            ->latest()
            ->take(5)
            ->get();

        $assignedProductions = Production::with('planning.project.order')
            ->where('assigned_to', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $recentFieldDocs = FieldDocumentation::with('project')
            ->where('uploaded_by', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'sps_active' => Survey::where('assigned_to', $user->id)->whereNotNull('approved_at')->whereIn('status', ['dijadwalkan', 'berlangsung'])->count(),
            'sps_completed' => Survey::where('assigned_to', $user->id)->where('status', 'selesai')->count(),
            'productions_active' => Production::where('assigned_to', $user->id)->whereIn('status', ['pending', 'in_progress'])->count(),
            'field_docs_count' => FieldDocumentation::where('uploaded_by', $user->id)->count(),
        ];

        return view('dashboard.petugas-lapangan', compact(
            'stats',
            'activeSps',
            'assignedProductions',
            'recentFieldDocs'
        ));
    }

    private function clientDashboard($user): View
    {
        $orders = Order::with(['survey.assignedUser', 'project'])
            ->where('client_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'orders_count' => Order::where('client_id', $user->id)->count(),
            'surveys_count' => Survey::whereHas('order', fn ($q) => $q->where('client_id', $user->id))->count(),
            'active_projects' => Project::whereIn('status', ['planning', 'in_progress'])
                ->whereHas('order', fn ($q) => $q->where('client_id', $user->id))
                ->count(),
            'completed_projects' => Project::where('status', 'completed')
                ->whereHas('order', fn ($q) => $q->where('client_id', $user->id))
                ->count(),
        ];

        return view('dashboard.client', compact('stats', 'orders'));
    }
}
