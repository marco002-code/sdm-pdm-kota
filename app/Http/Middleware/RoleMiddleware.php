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

        $user = auth()->user();

        // Gunakan metode hasRole dari User model yang mendukung seluruh alias RBAC
        if (!$user->hasRole($roles)) {
            abort(403, 'Akses ditolak: Anda tidak memiliki izin untuk peran atau halaman ini.');
        }

        return $next($request);
    }
}
