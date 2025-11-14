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

        // Admin SELALU bisa akses semua
        if ($userRole === 'admin') {
            return $next($request);
        }

        // Normalisasi role untuk koordinator variants
        $normalizedUserRole = $this->normalizeRole($userRole);
        $normalizedAllowedRoles = array_map([$this, 'normalizeRole'], $roles);

        // Check if normalized user role is in allowed roles
        if (in_array($normalizedUserRole, $normalizedAllowedRoles)) {
            return $next($request);
        }

        // Unauthorized
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }

    /**
     * Normalize role names untuk handle variants
     */
    private function normalizeRole(string $role): string
    {
        // Map semua variant ke 'koordinator'
        $koordinatorVariants = [
            'koordinator',
            'coordinator',
            'wakil_koordinator',
            'wakil_coordinator'
        ];

        if (in_array($role, $koordinatorVariants)) {
            return 'koordinator';
        }

        return $role;
    }
}
