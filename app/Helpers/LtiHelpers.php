<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class LtiHelpers
{
    public static function getUser(Request $request)
    {
        /*
        $connector = DataConnector\DataConnector::getDataConnector('', DB::connection()->getPdo());
        $recordId = $request->session()->get('record_id');

        // In some cases (usually when authenticated as student), the user is saved to the database.
        // We use the record id to retrieve additional database information, if available.

        if ($recordId) {

            $user = User::fromRecordId($recordId, $connector);

            return array_merge($request->session()->get('user'), [

                'userId' => $user->getId()
            ]);
        }
        else
        */

        $user = $request->session()->get('user');

        //array_push($user['roles'], Roles::Administrator);
        //array_push($user['roles'], Roles::StudyAdvisor);
        //array_push($user['roles'], Roles::Student);

        return $user;
    }
}