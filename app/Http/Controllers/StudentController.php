<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Helpers\LtiHelpers;
use App\Helpers\PaginationHelpers;
use App\Helpers\RoleHelpers;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class StudentController extends BaseController
{
    public function createByImport(Request $request)
    {
        if (!ini_get('auto_detect_line_endings'))
            ini_set('auto_detect_line_endings', true);

        $this->validate($request, [

            'data' => 'required|file'
        ]);

        $file = $request->file('data');

        if (!$file->isValid())
            throw new \Exception('The file was not uploaded successfully.');

        $stream = fopen($file, 'r');
        $csv    = Reader::createFromStream($stream)->setDelimiter(';')->setHeaderOffset(0);

        DB::transaction(function () use ($csv) {

            foreach ($csv->getRecords() as $offset => $record) {

                //try {

                if (empty($record['nummer']))
                    throw new \Exception('Student number cannot be empty.');

                $obj = new Student();

                $obj->student_number               = $record['nummer'];
                $obj->program_code                 = $record['opl'];
                $obj->program_name                 = $record['omschrijving'];
                $obj->cohort                       = $record['cohort'];
                $obj->bsa_credits                  = $record['BSA-crd'];
                $obj->bsa                          = $record['bsa'];
                $obj->second_year                  = $record['2ndY'];
                $obj->second_year_b1_credits       = !empty($record['2ndY-B1']) ? $record['2ndY-B1'] : null;
                $obj->second_year_b2_credits       = !empty($record['2ndY-B2']) ? $record['2ndY-B2'] : null;
                $obj->second_year_b3_credits       = !empty($record['2ndY-B3']) ? $record['2ndY-B3'] : null;
                $obj->second_year_b4_credits       = !empty($record['2ndY-B4']) ? $record['2ndY-B4'] : null;
                $obj->second_year_b5_credits       = !empty($record['2ndY-B5']) ? $record['2ndY-B5'] : null;
                $obj->second_year_b6_credits       = !empty($record['2ndY-B6']) ? $record['2ndY-B6'] : null;
                $obj->second_year_b1_subjects      = !empty($record['Nvakken-B1']) ? $record['Nvakken-B1'] : null;
                $obj->second_year_credits          = !empty($record['2ndY-crd']) ? $record['2ndY-crd'] : null;
                $obj->second_year_credits_expected = $record['2ndY-crd Prognose'];
                $obj->second_year_credits_goal     = $record['My2ndGoal'];
                $obj->dip_category                 = $record['DipCategory'];
                $obj->credits                      = $record['RunningTotal'];
                $obj->gpa_current                  = floatval($record['GPA actueel']);
                $obj->graduation_date_expected     = \DateTime::createFromFormat('d-m-Y', $record['prognose afstudeer datum obv tempo']);
                $obj->first_name                   = $record['VOORNAAM'];
                $obj->last_name                    = $record['ACHTERNAAM'];
                $obj->tussenvoegsel                = $record['TUSSENVOEGSEL'];
                $obj->initials                     = $record['INITIALEN'];
                $obj->birth_date                   = \DateTime::createFromFormat('d-m-Y', $record['GEBOORTEDATUM']);
                $obj->birth_place                  = $record['GEBOORTEPLAATS'];
                $obj->birth_country                = $record['GEBOORTELAND'];
                $obj->gender                       = $record['GESLACHT'] === 'M' ? 1 : ($record['GESLACHT'] === 'F' ? 2 : 9);
                $obj->nationality                  = $record['NATIONALITEIT'];
                $obj->email_address                = $record['EMAILADRES'];
                $obj->vooropleiding                = $record['vooropleidng'];
                $obj->is_published                 = false;

                if (!empty($obj->tussenvoegsel)) {

                    $obj->display_name = $obj->first_name . ' ' . $obj->tussenvoegsel . ' ' . $obj->last_name;
                }
                else
                    $obj->display_name = $obj->first_name . ' ' . $obj->last_name;

                // Delete any existing data that matches the student.

                Student::where('student_number', $obj->student_number)->delete();

                $obj->save();
                //}
                //catch (\Exception $ex) {
                //
                //    continue;
                //}
            }
        });

        return response('', 201);
    }

    public function getByAuthenticated(Request $request)
    {
        // if (!RoleHelpers::hasAnyRole($request, [Roles::StudyAdviser, Roles::Administrator]))
        //     return response(Student::where('student_number', array_get(LtiHelpers::getUser($request), 'ltiUserId'))->where('is_published', true)->firstOrFail());
        // else
        //     return response(Student::where('student_number', array_get(LtiHelpers::getUser($request), 'ltiUserId'))->firstOrFail());

        if (!RoleHelpers::hasAnyRole($request, [Roles::StudyAdviser, Roles::Administrator]))
            return response(Student::where('student_number', array_get(LtiHelpers::getUser($request), 'custom_student_number'))->where('is_published', true)->firstOrFail());
        else
            return response(Student::where('student_number', array_get(LtiHelpers::getUser($request), 'custom_student_number'))->firstOrFail());
    }

    public function getById($id, Request $request)
    {
        if (!RoleHelpers::hasAnyRole($request, [Roles::StudyAdviser, Roles::Administrator]))
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

    public function deleteByIds($ids)
    {
        Student::whereIn('id', explode(',', $ids))->delete();
    }

    public function deleteByParameters(Request $request)
    {
        $this->validate($request, [

            'query' => 'string|nullable'
        ]);

        $query = Student::query();

        if ($request->filled('query')) {

            $query = $query->where('student_number', 'LIKE', '%' . $request->input('query') . '%');
            $query = $query->orWhere('display_name', 'LIKE', '%' . $request->input('query') . '%');
            $query = $query->orWhere('program_name', 'LIKE', '%' . $request->input('query') . '%');
            $query = $query->orWhere('cohort', 'LIKE', '%' . $request->input('query') . '%');
        }

        $query->delete();
    }

    public function index(Request $request)
    {
        $this->validate($request, [

            'order'         => 'string|nullable',
            'publishedOnly' => 'nullable',
            'query'         => 'string|nullable'
        ]);

        $query = Student::query();

        if (filter_var($request->input('publishedOnly'), FILTER_VALIDATE_BOOLEAN) === true || !RoleHelpers::hasAnyRole($request, [Roles::StudyAdviser, Roles::Administrator]))
            $query = $query->where('is_published', true);

        if ($request->filled('query')) {

            $query = $query->where('student_number', 'LIKE', '%' . $request->input('query') . '%');
            $query = $query->orWhere('display_name', 'LIKE', '%' . $request->input('query') . '%');
            $query = $query->orWhere('program_name', 'LIKE', '%' . $request->input('query') . '%');
            $query = $query->orWhere('cohort', 'LIKE', '%' . $request->input('query') . '%');
        }

        $orderBy        = 'updated_at';
        $orderDirection = 'desc';

        if ($request->filled('order')) {

            list($field, $direction) = explode('|', $request->input('order'));

            if (!empty($field)) {

                $orderBy        = $field;
                $orderDirection = !empty($direction) ? $direction : 'asc';
            }
        }

        $query = $query->orderBy(snake_case($orderBy), $orderDirection);

        $paginator = $query->paginate(PaginationHelpers::getLimit($request));
        $paginator->appends(PaginationHelpers::getOtherQueryParameters($request));

        return $paginator;
    }

    public function updatePartialByAuthenticated(Request $request)
    {
        $student = Student::where('student_number', array_get(LtiHelpers::getUser($request), 'custom_student_number'))->firstOrFail();
        return $this->updatePartialById($student->id, $request);
    }

    public function updatePartialByIds($ids, Request $request)
    {
        $this->validate($request, $this->getValidatorPartial($request));

        $student = null;

        foreach (explode(',', $ids) as $id) {

            $student = Student::findOrFail($id);

            DB::transaction(function () use ($id, $request, $student) {

                if ($request->exists('second_year_credits_goal')) $student->second_year_credits_goal = $request->input('second_year_credits_goal');
                if ($request->exists('is_published')) $student->is_published = $request->input('is_published');

                $student->save();
            });
        }

        return response(Student::find($student->id));
    }

    public function updatePartialByParameters(Request $request)
    {
        $this->validate($request, [

            'query' => 'string|nullable'
        ]);

        $this->validate($request, $this->getValidatorPartial($request));

        $query = Student::query();

        if ($request->filled('query')) {

            $query = $query->where('student_number', 'LIKE', '%' . $request->input('query') . '%');
            $query = $query->orWhere('display_name', 'LIKE', '%' . $request->input('query') . '%');
            $query = $query->orWhere('program_name', 'LIKE', '%' . $request->input('query') . '%');
            $query = $query->orWhere('cohort', 'LIKE', '%' . $request->input('query') . '%');
        }

        DB::transaction(function () use ($request, $query) {

            foreach ($query->get() as $student) {

                if ($request->exists('second_year_credits_goal')) $student->second_year_credits_goal = $request->input('second_year_credits_goal');
                if ($request->exists('is_published')) $student->is_published = $request->input('is_published');

                $student->save();
            }
        });

        return response($query->get());
    }

    protected function getValidatorComplete(Request $request)
    {
        return [

            'second_year_credits_goal' => 'required|integer|min:0|max:100',
            'is_published'             => 'required|boolean'
        ];
    }

    protected function getValidatorPartial(Request $request)
    {
        return [

            'second_year_credits_goal' => 'integer|min:0|max:100',
            'is_published'             => 'boolean'
        ];
    }
}