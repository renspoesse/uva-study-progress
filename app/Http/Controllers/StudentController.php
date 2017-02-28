<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelpers;
use App\Models\Student;
use Illuminate\Http\Request;
use Validator;

class StudentController extends BaseController
{
    public function createByImport(Request $request)
    {
        // TODO RENS: import csv
    }

    public function index(Request $request)
    {
        $this->validate($request, [

            'order' => 'string|nullable',
            'query' => 'string|nullable'
        ]);

        $query = Student::query();

        if ($request->has('query')) {

            $query = $query->where('student_number', 'LIKE', '%' . $request->input('query') . '%');
        }

        $orderBy = 'updated_at';
        $orderDirection = 'desc';

        if ($request->has('order')) {

            list($field, $direction) = explode('|', $request->input('order'));

            if (!empty($field)) {

                $orderBy = $field;
                $orderDirection = !empty($direction) ? $direction : 'asc';
            }
        }

        $query = $query->orderBy(snake_case($orderBy), $orderDirection);

        $paginator = $query->paginate(PaginationHelpers::getLimit($request));
        $paginator->appends(PaginationHelpers::getOtherQueryParameters($request));

        return $paginator;
    }
}