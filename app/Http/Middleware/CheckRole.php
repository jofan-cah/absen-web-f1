<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $userRole = $user->role;

        // Admin bisa akses semua
        if ($userRole === 'admin') {
            return $next($request);
        }

        // Check if user role is in allowed roles
        $coordinatorRoles = ['coordinator', 'koordinator', 'wakil_coordinator', 'wakil_koordinator'];

        foreach ($roles as $role) {
            if ($role === 'coordinator' && in_array($userRole, $coordinatorRoles)) {
                return $next($request);
            }

            if ($userRole === $role) {
                return $next($request);
            }
        }

        // Unauthorized
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}
