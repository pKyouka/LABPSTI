<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        if (empty($roles)) {
            return $next($request);
        }

        $normalizedRoles = array_map(static fn (string $role): string => strtolower(trim($role)), $roles);
        $userRole = strtolower((string) $user->role);

        if (in_array($userRole, $normalizedRoles, true)) {
            return $next($request);
        }

        if (in_array('mahasiswa', $normalizedRoles, true) && method_exists($user, 'isMahasiswa') && $user->isMahasiswa()) {
            return $next($request);
        }

        abort(403);
    }
}
