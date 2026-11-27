<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Project;
use App\Models\Survey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Riwayat Event yang pernah dikerjakan.
 * Akses: Admin, Direktur (seluruh event) dan Petugas Lapangan (hanya event yang ia tangani).
 */
class EventHistoryController extends Controller
{
    private const DONE_PROJECT = ['completed'];
    private const CANCELLED_PROJECT = ['cancelled'];

    public function index(Request $request): View
    {
        $user = $request->user();
        $role = $user?->role?->slug;

        abort_unless(in_array($role, ['admin', 'direktur', 'petugas-lapangan'], true), 403);

        $search = trim((string) $request->query('search'));
        $year = $request->query('year');
        $status = $request->query('status');

        $base = $this->baseQuery($role, $user->id);

        $events = (clone $base)
            ->with([
                'client',
                'survey.assignedUser',
                'project.assignedUser',
                'project.rating',
                'project.planning.production.assignedUser',
            ])
            ->when($search !== '', function (Builder $q) use ($search) {
                $q->where(function (Builder $w) use ($search) {
                    $w->where('order_number', 'like', "%{$search}%")
                        ->orWhere('event_name', 'like', "%{$search}%")
                        ->orWhere('event_location', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                });
            })
            ->when($year, fn (Builder $q) => $q->where(function (Builder $w) use ($year) {
                $w->whereYear('event_date', $year)
                    ->orWhere(fn (Builder $x) => $x->whereNull('event_date')->whereYear('created_at', $year));
            }))
            ->when($status === 'selesai', fn (Builder $q) => $q->where(function (Builder $w) {
                $w->where('status', 'selesai')
                    ->orWhereHas('projects', fn (Builder $p) => $p->whereIn('status', self::DONE_PROJECT));
            }))
            ->when($status === 'berjalan', fn (Builder $q) => $q->where('status', '!=', 'selesai')
                ->whereDoesntHave('projects', fn (Builder $p) => $p->whereIn('status', array_merge(self::DONE_PROJECT, self::CANCELLED_PROJECT))))
            ->orderByRaw('COALESCE(event_date, DATE(created_at)) DESC')
            ->paginate(10)
            ->withQueryString();

        $allIds = (clone $base)->pluck('id');

        $stats = [
            'total' => $allIds->count(),
            'completed' => Order::whereIn('id', $allIds)->where(function (Builder $w) {
                $w->where('status', 'selesai')
                    ->orWhereHas('projects', fn (Builder $p) => $p->whereIn('status', self::DONE_PROJECT));
            })->count(),
            'surveys_done' => Survey::whereIn('order_id', $allIds)->where('status', Survey::STATUS_DONE)
                ->when($role === 'petugas-lapangan', fn ($q) => $q->where('assigned_to', $user->id))
                ->count(),
            'avg_rating' => round((float) Project::whereIn('order_id', $allIds)
                ->join('ratings', 'ratings.project_id', '=', 'projects.id')
                ->avg('ratings.rating'), 1),
        ];

        $years = (clone $base)
            ->selectRaw('DISTINCT YEAR(COALESCE(event_date, created_at)) as y')
            ->reorder()
            ->orderByDesc('y')
            ->pluck('y')
            ->filter()
            ->values();

        return view('event-history.index', compact('events', 'stats', 'years', 'role', 'search', 'year', 'status'));
    }

    /**
     * Event dianggap "pernah dikerjakan" bila surveinya sudah selesai atau sudah memiliki proyek.
     */
    private function baseQuery(string $role, int $userId): Builder
    {
        $query = Order::query()->where(function (Builder $q) {
            $q->whereHas('surveys', fn (Builder $s) => $s->where('status', Survey::STATUS_DONE))
                ->orWhereHas('projects');
        });

        if ($role === 'petugas-lapangan') {
            $query->where(function (Builder $q) use ($userId) {
                $q->whereHas('surveys', fn (Builder $s) => $s->where('assigned_to', $userId))
                    ->orWhereHas('projects', fn (Builder $p) => $p->where('assigned_to', $userId))
                    ->orWhereHas('projects.planning.production', fn (Builder $p) => $p->where('assigned_to', $userId))
                    ->orWhereHas('projects.fieldDocumentations', fn (Builder $d) => $d->where('uploaded_by', $userId));
            });
        }

        return $query;
    }
}
