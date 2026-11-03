<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        $userRole = auth()->user()->role;
        $allowedRoles = [];

        foreach ($roles as $role) {
            $allowedRoles[] = $role;
            if ($role === 'yayasan') {
                $allowedRoles[] = 'ketua_yayasan';
            }
            if ($role === 'ketua_yayasan') {
                $allowedRoles[] = 'yayasan';
            }
            if ($role === 'kepsek') {
                $allowedRoles[] = 'kepala_sekolah';
            }
            if ($role === 'kepala_sekolah') {
                $allowedRoles[] = 'kepsek';
            }
            if ($role === 'tu') {
                $allowedRoles[] = 'tu_sekolah';
            }
            if ($role === 'tu_sekolah') {
                $allowedRoles[] = 'tu';
            }
        }

        if (!in_array($userRole, $allowedRoles, true)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk peran ini.');
        }

        return $next($request);
    }
}
