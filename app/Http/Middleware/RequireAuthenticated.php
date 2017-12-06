<?php

namespace App\Http\Middleware;

use App\Enums\Roles;
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

        $domain   = parse_url($request->url(), PHP_URL_HOST);
        $elements = explode('.', $domain);

        $tld = $elements[count($elements) - 1];

        if ($tld === 'dev' && !$request->session()->get('authenticated')) {

            $request->session()->put('user', [

                'firstname' => 'Developer',
                'lastname'  => '',
                'fullname'  => 'Developer',
                'email'     => 'email@example.com',
                'image'     => '',
                'roles'     => [Roles::Administrator],
                'groups'    => []
            ]);
            $request->session()->put('authenticated', true);
        }

        if (!$request->session()->get('authenticated')) {

            Log::error('No authenticated session.');
            abort(401, 'No authenticated session.');
        }

        return $next($request);
    }
}
