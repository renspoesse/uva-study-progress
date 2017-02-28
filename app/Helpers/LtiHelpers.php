<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use IMSGlobal\LTI\ToolProvider\DataConnector;
use IMSGlobal\LTI\ToolProvider\User;
use DB;

class LtiHelpers
{
    public static function getRoles(Request $request)
    {
        $user = LtiHelpers::getUser($request);
        return $user ? $user['roles'] : [];
    }

    public static function getUser(Request $request)
    {
        $connector = DataConnector\DataConnector::getDataConnector('', DB::connection()->getPdo());
        $user = User::fromRecordId($request->session()->get('record_id'), $connector);

        return array_merge($request->session()->get('user'), [

            'userId' => $user->getId()
        ]);
    }

    public static function hasRole(Request $request, $role)
    {
        return in_array($role, LtiHelpers::getRoles($request));
    }
}