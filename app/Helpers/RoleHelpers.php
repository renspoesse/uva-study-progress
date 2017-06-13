<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class RoleHelpers
{
    public static function getRoles(Request $request)
    {
        $user = LtiHelpers::getUser($request);
        return $user ? $user['roles'] : [];
    }

    public static function hasAnyRole(Request $request, $roles)
    {
        $userRoles = RoleHelpers::getRoles($request);

        if (is_array($roles)) {

            foreach ($roles as $role) {

                if (in_array($role, $userRoles))
                    return true;
            }

            return false;
        }
        else
            return in_array($roles, $userRoles);
    }
}