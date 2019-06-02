<?php

namespace App\Http\Middleware;

use App\Helpers\LtiHelpers;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\DB;

class LastSeen
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
        if ($request->session()->get('authenticated')) {
            // We bypass Eloquent to prevent setting updated_at here.
            DB::table('students')->where('student_number', array_get(LtiHelpers::getUser($request),
                'custom_student_number'))->update(['last_seen' => Carbon::now()]);
        }

        return $next($request);
    }
}
