<?php

namespace App\Http\Controllers;

use App\Helpers\LtiHelpers;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getByAuthenticated(Request $request)
    {
        return LtiHelpers::getUser($request);
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
    }
}
