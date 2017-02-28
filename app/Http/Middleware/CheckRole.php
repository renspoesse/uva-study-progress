<?php

namespace App\Http\Middleware;

use App\Helpers\LtiHelpers;
use Closure;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string                   $role
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if (!LtiHelpers::hasRole($request, $role))
            abort(403, 'User has insufficient permissions.');

        return $next($request);
    }
}