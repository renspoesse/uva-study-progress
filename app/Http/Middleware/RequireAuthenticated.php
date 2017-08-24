<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

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
        Log::info('Request method: ' . $request->method());
        Log::info('Request URL: ' . $request->fullUrl());
        Log::info('Session id (middleware): ' . $request->session()->getId());

        if (!$request->session()->get('authenticated')) {

            Log::error('No authenticated session.');
            abort(401, 'No authenticated session.');
        }

        return $next($request);
    }
}
