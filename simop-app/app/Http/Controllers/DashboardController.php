<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless($user && $user->role, 403, 'Role pengguna belum diatur.');

        $dashboards = [
            'admin' => 'dashboard.admin',
            'direktur' => 'dashboard.direktur',
            'ceo' => 'dashboard.ceo',
            'petugas-lapangan' => 'dashboard.petugas-lapangan',
            'client' => 'dashboard.client',
        ];

        $view = $dashboards[$user->role->slug] ?? null;

        abort_unless($view, 403, 'Dashboard tidak tersedia.');

        return view($view);
    }
}