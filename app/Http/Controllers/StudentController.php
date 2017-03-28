<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Helpers\LtiHelpers;
use App\Helpers\RoleHelpers;
use App\Helpers\PaginationHelpers;
use App\Models\Student;
use Illuminate\Http\Request;
use Validator;
use DB;

class StudentController extends BaseController
{
    public function createByImport(Request $request)
    {
        ini_set('auto_detect_line_endings', true);

        $this->validate($request, [

            'data' => 'required|file'
        ]);

        $file = $request->file('data');

        if (!$file->isValid())
            throw new \Exception('The file was not uploaded successfully.');

        $rows = [];

        if (($handle = fopen($file, 'r')) !== false) {

            while (($data = fgetcsv($handle, 0, ';')) !== false)
                array_push($rows, $data);

            fclose($handle);
        }
        else
            throw new \Exception('Unable to open file.');

        $headers = array_splice($rows, 0, 1)[0];
        $parsed = [];

        foreach ($rows as $row) {

            $record = [];

            foreach ($headers as $index => $header)
                $record[utf8_encode($header)] = $row[$index];

            array_push($parsed, $record);
        }

        DB::transaction(function () use ($parsed) {

            foreach ($parsed as $record) {

                try {

                    $obj = new Student();

                    $obj->student_number = $record['ï»¿nummer'];
                    $obj->program_code = $record['opl'];
                    $obj->program_name = $record['omschrijving'];
                    $obj->cohort = $record['cohort'];
                    $obj->bsa_credits = $record['BSA-crd'];
                    $obj->bsa = $record['bsa'];
                    $obj->second_year = $record['2ndY'];
                    $obj->second_year_b1_credits = $record['2ndY-B1'];
                    $obj->second_year_b1_subjects = $record['Nvakken-B1'];
                    $obj->second_year_credits = $record['2ndY-crd'];
                    $obj->second_year_credits_expected = $record['2ndY-crd Prognose'];
                    $obj->dip_category = $record['DipCategory'];
                    $obj->gpa_current = floatval($record['GPA actueel']);
                    $obj->first_name = $record['VOORNAAM'];
                    $obj->last_name = $record['ACHTERNAAM'];
                    $obj->tussenvoegsel = $record['TUSSENVOEGSEL'];
                    $obj->initials = $record['INITIALEN'];
                    $obj->birth_date = \DateTime::createFromFormat('d-m-Y', $record['GEBOORTEDATUM']);
                    $obj->birth_place = $record['GEBOORTEPLAATS'];
                    $obj->birth_country = $record['GEBOORTELAND'];
                    $obj->gender = $record['GESLACHT'] === 'M' ? 1 : ($record['GESLACHT'] === 'F' ? 2 : 9);
                    $obj->nationality = $record['NATIONALITEIT'];
                    $obj->email_address = $record['EMAILADRES'];
                    $obj->vooropleiding = $record['vooropleidng'];
                    $obj->is_published = false;

                    if (!empty($obj->tussenvoegsel)) {

                        $obj->display_name = $obj->first_name . ' ' . $obj->tussenvoegsel . ' ' . $obj->last_name;
                    }
                    else
                        $obj->display_name = $obj->first_name . ' ' . $obj->last_name;

                    // Delete any existing data that matches the student.

                    Student::where('student_number', $obj->student_number)->delete();

                    $obj->save();
                }
                catch (\Exception $ex) {

                    continue;
                }
            }
        });

        return response('', 201);
    }

    public function getByAuthenticated(Request $request)
    {
        if (!RoleHelpers::hasAnyRole($request, [Roles::StudyAdvisor, Roles::Administrator]))
            return response(Student::where('student_number', array_get(LtiHelpers::getUser($request), 'ltiUserId'))->where('is_published', true)->firstOrFail());
        else
            return response(Student::where('student_number', array_get(LtiHelpers::getUser($request), 'ltiUserId'))->firstOrFail());
    }

    public function getById($id, Request $request)
    {
        if (!RoleHelpers::hasAnyRole($request, [Roles::StudyAdvisor, Roles::Administrator]))
            return response(Student::where('id', $id)->where('is_published', true)->firstOrFail());
        else
            return response(Student::where('id', $id)->firstOrFail());
    }

    public function getCreditsExpected()
    {
        // TODO RENS: cachen.

        $result = DB::table('students')->select('bsa_credits', 'second_year_b1_subjects', DB::raw('AVG(second_year_credits) AS second_year_credits_expected'))->whereNotNull('second_year_credits')->groupBy('bsa_credits', 'second_year_b1_subjects')->get();

        $result->each(function ($record) {

            $record->second_year_credits_expected = (int)round((double)$record->second_year_credits_expected);
        });

        return response($result);
    }

    public function deleteById($id)
    {
        Student::where('id', $id)->delete();
    }

    public function index(Request $request)
    {
        $this->validate($request, [

            'order'         => 'string|nullable',
            'publishedOnly' => 'nullable',
            'query'         => 'string|nullable'
        ]);

        $query = Student::query();

        if (filter_var($request->input('publishedOnly'), FILTER_VALIDATE_BOOLEAN) === true || !RoleHelpers::hasAnyRole($request, [Roles::StudyAdvisor, Roles::Administrator]))
            $query = $query->where('is_published', true);

        if ($request->has('query')) {

            $query = $query->where('student_number', 'LIKE', '%' . $request->input('query') . '%');
            $query = $query->orWhere('display_name', 'LIKE', '%' . $request->input('query') . '%');
            $query = $query->orWhere('program_name', 'LIKE', '%' . $request->input('query') . '%');
            $query = $query->orWhere('cohort', 'LIKE', '%' . $request->input('query') . '%');
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

    public function updatePartialById($id, Request $request)
    {
        $this->validate($request, $this->getValidatorPartial($request));

        $student = Student::findOrFail($id);

        DB::transaction(function () use ($id, $request, $student) {

            if ($request->exists('is_published')) $student->is_published = $request->input('is_published');

            $student->save();
        });

        return response(Student::find($student->id));
    }

    protected function getValidatorComplete(Request $request)
    {
        return [

            'is_published' => 'required|boolean'
        ];
    }

    protected function getValidatorPartial(Request $request)
    {
        return [

            'is_published' => 'boolean'
        ];
    }
}