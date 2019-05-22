<?php

namespace App\Http\Controllers;

use App\Helpers\LtiHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function getByAuthenticated(Request $request)
    {
        Log::info('Requesting authenticated user.');

        return LtiHelpers::getUser($request);
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
    }
}
