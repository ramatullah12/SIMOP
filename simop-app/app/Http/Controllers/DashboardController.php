<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\Survey;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless(
            $user && $user->role,
            403,
            'Role pengguna belum diatur.'
        );

        $role = $user->role->slug;

        if ($role === 'petugas-lapangan') {
            return $this->petugasLapanganDashboard($user->id);
        }

        $dashboards = [
            'admin' => 'dashboard.admin',
            'direktur' => 'dashboard.direktur',
            'client' => 'dashboard.client',
        ];

        $view = $dashboards[$role] ?? null;

        abort_unless(
            $view,
            403,
            'Dashboard tidak tersedia.'
        );

        return view($view);
    }

    private function petugasLapanganDashboard(int $userId): View
    {
        $surveyHariIni = Survey::where(
            'assigned_to',
            $userId
        )
            ->whereDate(
                'scheduled_at',
                today()
            )
            ->whereNotIn(
                'status',
                ['selesai', 'dibatalkan']
            )
            ->count();

        $belumDilaporkan = Survey::where(
            'assigned_to',
            $userId
        )
            ->whereIn(
                'status',
                ['dijadwalkan', 'berlangsung']
            )
            ->count();

        $tugasAktif = Production::where(
            'assigned_to',
            $userId
        )
            ->whereIn(
                'status',
                ['pending', 'in_progress']
            )
            ->count();

        $progressRataRata = Production::where(
            'assigned_to',
            $userId
        )
            ->whereNotIn(
                'status',
                ['cancelled']
            )
            ->avg('progress');

        $progressRataRata = (int) round(
            $progressRataRata ?? 0
        );

        $jadwalHariIni = Survey::with([
            'order',
        ])
            ->where(
                'assigned_to',
                $userId
            )
            ->whereDate(
                'scheduled_at',
                today()
            )
            ->whereNotIn(
                'status',
                ['dibatalkan']
            )
            ->orderBy('scheduled_at')
            ->get();

        return view(
            'dashboard.petugas-lapangan',
            compact(
                'surveyHariIni',
                'belumDilaporkan',
                'tugasAktif',
                'progressRataRata',
                'jadwalHariIni'
            )
        );
    }
}