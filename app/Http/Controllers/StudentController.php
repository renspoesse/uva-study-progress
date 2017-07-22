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

        //$contents = file_get_contents($file);
        //
        //if (!mb_check_encoding($contents, 'UTF-8') || !($contents === mb_convert_encoding(mb_convert_encoding($contents, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))) {
        //
        //    $contents = mb_convert_encoding($contents, 'UTF-8');
        //}
        //
        //$rows = explode(PHP_EOL, $contents);
        //$rows = array_map(function ($line) {
        //
        //    return str_getcsv($line, ';');
        //
        //}, $rows);

        /*
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
        */

        $stream = fopen($file, 'r');
        $csv    = Reader::createFromStream($stream)->setDelimiter(';');

        $headers = $csv->fetchOne();
        $rows    = $csv->setOffset(1)->fetchAll();

        $parsed = [];

        foreach ($rows as $row) {

            $record = [];

            // We assume that the file is UTF-8 encoded. If not, some loss will occur here.

            foreach ($headers as $index => $header)
                $record[$header] = utf8_encode($row[$index]);

            array_push($parsed, $record);
        }

        DB::transaction(function () use ($parsed) {

            foreach ($parsed as $record) {

                //try {

                //if (empty($record['ï»¿nummer'])) continue;
                if (empty($record['nummer'])) continue;

                $obj = new Student();

                //$obj->student_number = $record['ï»¿nummer'];
                $obj->student_number               = $record['nummer'];
                $obj->program_code                 = !empty($record['opl']) ? $record['opl'] : null;
                $obj->program_name                 = $record['omschrijving'];
                $obj->cohort                       = !empty($record['cohort']) ? $record['cohort'] : null;
                $obj->bsa_credits                  = !empty($record['BSA-crd']) ? $record['BSA-crd'] : null;
                $obj->bsa                          = $record['bsa'];
                $obj->second_year                  = !empty($record['2ndY']) ? $record['2ndY'] : null;
                $obj->second_year_b1_credits       = !empty($record['2ndY-B1']) ? $record['2ndY-B1'] : null;
                $obj->second_year_b2_credits       = !empty($record['2ndY-B2']) ? $record['2ndY-B2'] : null;
                $obj->second_year_b3_credits       = !empty($record['2ndY-B3']) ? $record['2ndY-B3'] : null;
                $obj->second_year_b4_credits       = !empty($record['2ndY-B4']) ? $record['2ndY-B4'] : null;
                $obj->second_year_b5_credits       = !empty($record['2ndY-B5']) ? $record['2ndY-B5'] : null;
                $obj->second_year_b6_credits       = !empty($record['2ndY-B6']) ? $record['2ndY-B6'] : null;
                $obj->second_year_b1_subjects      = !empty($record['Nvakken-B1']) ? $record['Nvakken-B1'] : null;
                $obj->second_year_credits          = !empty($record['2ndY-crd']) ? $record['2ndY-crd'] : null;
                $obj->second_year_credits_expected = !empty($record['2ndY-crd Prognose']) ? $record['2ndY-crd Prognose'] : null;
                $obj->second_year_credits_goal     = !empty($record['My2ndGoal']) ? $record['My2ndGoal'] : null;
                $obj->dip_category                 = $record['DipCategory'];
                $obj->credits                      = !empty($record['RunningTotal']) ? $record['RunningTotal'] : null;
                $obj->gpa_current                  = !empty($record['GPA actueel']) ? floatval($record['GPA actueel']) : null;
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
        // if (!RoleHelpers::hasAnyRole($request, [Roles::StudyAdvisor, Roles::Administrator]))
        //     return response(Student::where('student_number', array_get(LtiHelpers::getUser($request), 'ltiUserId'))->where('is_published', true)->firstOrFail());
        // else
        //     return response(Student::where('student_number', array_get(LtiHelpers::getUser($request), 'ltiUserId'))->firstOrFail());

        if (!RoleHelpers::hasAnyRole($request, [Roles::StudyAdvisor, Roles::Administrator]))
            return response(Student::where('student_number', array_get(LtiHelpers::getUser($request), 'custom_student_number'))->where('is_published', true)->firstOrFail());
        else
            return response(Student::where('student_number', array_get(LtiHelpers::getUser($request), 'custom_student_number'))->firstOrFail());
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

    public function deleteByParameters(Request $request)
    {
        $this->validate($request, [

            'query' => 'string|nullable'
        ]);

        $query = Student::query();

        if ($request->has('query')) {

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

        if (filter_var($request->input('publishedOnly'), FILTER_VALIDATE_BOOLEAN) === true || !RoleHelpers::hasAnyRole($request, [Roles::StudyAdvisor, Roles::Administrator]))
            $query = $query->where('is_published', true);

        if ($request->has('query')) {

            $query = $query->where('student_number', 'LIKE', '%' . $request->input('query') . '%');
            $query = $query->orWhere('display_name', 'LIKE', '%' . $request->input('query') . '%');
            $query = $query->orWhere('program_name', 'LIKE', '%' . $request->input('query') . '%');
            $query = $query->orWhere('cohort', 'LIKE', '%' . $request->input('query') . '%');
        }

        $orderBy        = 'updated_at';
        $orderDirection = 'desc';

        if ($request->has('order')) {

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

    public function updatePartialById($id, Request $request)
    {
        $this->validate($request, $this->getValidatorPartial($request));

        $student = Student::findOrFail($id);

        DB::transaction(function () use ($id, $request, $student) {

            if ($request->exists('second_year_credits_goal')) $student->second_year_credits_goal = $request->input('second_year_credits_goal');
            if ($request->exists('is_published')) $student->is_published = $request->input('is_published');

            $student->save();
        });

        return response(Student::find($student->id));
    }

    public function updatePartialByParameters(Request $request)
    {
        $this->validate($request, [

            'query' => 'string|nullable'
        ]);

        $this->validate($request, $this->getValidatorPartial($request));

        $query = Student::query();

        if ($request->has('query')) {

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

            'second_year_credits_goal' => 'required|integer|min:0',
            'is_published'             => 'required|boolean'
        ];
    }

    protected function getValidatorPartial(Request $request)
    {
        return [

            'second_year_credits_goal' => 'integer|min:0',
            'is_published'             => 'boolean'
        ];
    }
}