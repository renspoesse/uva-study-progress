<?php

namespace App\Http\Middleware;

use Closure;

class RequireAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->session()->get('authenticated'))
            abort(401, 'No authenticated session.');

        return $next($request);
    }
}
