<?php

namespace App\Http\Middleware;

use App\Helpers\RoleHelpers;
use Closure;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param array $roles
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!RoleHelpers::hasAnyRole($request, $roles)) {
            abort(403, 'User has insufficient permissions.');
        }

        return $next($request);
    }
}
