<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class PaginationHelpers
{
    public static function getLimit(Request $request)
    {
        // Allow the client to specify the number of records per page.

        $limit = $request->input('limit', config('pagination.recordsPerPage'));
        return min($limit, config('pagination.maxRecordsPerPage'));
    }

    public static function getCursorInput(Request $request, &$before, &$after, &$limit)
    {
        $before = $request->input('before');
        $after  = $request->input('after');
        $limit  = PaginationHelpers::getLimit($request);
    }

    public static function getCursorInputInteger(Request $request, &$before, &$after, &$limit)
    {
        $before = $request->filled('before') ? (int)$request->input('before') : null;
        $after  = $request->filled('after') ? (int)$request->input('after') : null;
        $limit  = PaginationHelpers::getLimit($request);
    }

    public static function getOtherQueryParameters(Request $request)
    {
        // Keep any existing query parameters.

        return array_diff_key($_GET, array_flip(['page']));
    }
}