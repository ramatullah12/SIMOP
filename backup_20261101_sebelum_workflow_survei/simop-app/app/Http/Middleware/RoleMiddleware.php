<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $role = $user->role?->slug;

        $validRoles = [
            'admin',
            'direktur',
            'petugas-lapangan',
            'client',
        ];

        if (!$role || !in_array($role, $validRoles, true)) {
            abort(403, 'Role pengguna belum diatur dengan benar.');
        }

        if ($request->routeIs('dashboard')) {
            return $next($request);
        }

        if (!empty($roles) && !in_array($role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}