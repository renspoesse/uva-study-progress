<?php

namespace App\Http\Middleware;

use App\Enums\Roles;
use Closure;
use Illuminate\Support\Facades\App;

class RequireAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (App::environment() === 'local' && !$request->session()->get('authenticated')) {
            $request->session()->put('user', [
                'firstname' => 'Developer',
                'lastname' => '',
                'fullname' => 'Developer',
                'email' => 'email@example.com',
                'image' => '',
                'roles' => [Roles::Student, Roles::Administrator],
                'groups' => [],
                'custom_student_number' => '00000000'
            ]);
            $request->session()->put('authenticated', true);
        }

        if (!$request->session()->get('authenticated')) {
            abort(401, 'No authenticated session.');
        }

        return $next($request);
    }
}
