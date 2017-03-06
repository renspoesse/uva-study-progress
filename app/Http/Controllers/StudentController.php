<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelpers;
use App\Models\Student;
use Illuminate\Http\Request;
use Validator;
use DB;

class StudentController extends BaseController
{
    public function createByImport(Request $request)
    {
        // TODO RENS: upload csv

        /*
        $this->validate($request, [

            'file' => 'required|file'
        ]);

        $file = $request->file('file');

        if (!$file->isValid())
            throw new \Exception('The file was not uploaded successfully.');
        */

        $file = 'https://studyprogress.rens.amsterdam/TB_RensTest.csv';

        $rows = [];

        if (($handle = fopen($file, 'r')) !== false) {

            while (($data = fgetcsv($handle, 0, ';')) !== false)
                array_push($rows, $data);

            fclose($handle);
        }

        $headers = array_splice($rows, 0, 1)[0];
        $parsed = [];

        foreach ($rows as $row) {

            $record = [];

            foreach ($headers as $index => $header)
                $record[$header] = $row[$index];

            array_push($parsed, $record);
        }

        DB::transaction(function () use ($parsed) {

            foreach ($parsed as $record) {

                $obj = new Student();

                $obj->student_number = $record['nummer'];
                $obj->program_code = $record['opl'];
                $obj->program_name = $record['omschrijving'];
                $obj->cohort = $record['cohort'];
                $obj->bsa_credits = $record['BSA-crd'];
                $obj->bsa = $record['bsa'];
                $obj->second_year = $record['2ndY'];
                $obj->second_year_b1_credits = $record['2ndY-B1'];
                $obj->second_year_b1_subjects = $record['Nvakken-B1'];
                $obj->second_year_credits = $record['2ndY-crd'];
                $obj->dip_category = $record['DipCategory'];
                $obj->gpa_current = floatval($record['GPA actueel']);
                $obj->first_name = $record['VOORNAAM'];
                $obj->last_name = $record['ACHTERNAAM'];
                $obj->tussenvoegsel = $record['TUSSENVOEGSEL'];
                $obj->initials = $record['INITIALEN'];
                $obj->birth_date = \DateTime::createFromFormat('d-m-Y', $record['GEBOORTEDATUM']);
                $obj->birth_place = 'TODO';
                # $obj->birth_place = $record['GEBOORTEPLAATS'];
                $obj->birth_country = $record['GEBOORTELAND'];
                $obj->gender = $record['GESLACHT'] === 'M' ? 1 : ($record['GESLACHT'] === 'F' ? 2 : 9);
                $obj->nationality = $record['NATIONALITEIT'];
                $obj->email_address = $record['EMAILADRES'];
                $obj->vooropleiding = $record['vooropleidng'];

                $obj->save();
            }
        });

        return response('', 201);
    }

    public function deleteById($id)
    {
        Student::where('id', $id)->delete();
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
            $query = $query->orWhere('program_code', 'LIKE', '%' . $request->input('query') . '%');
            $query = $query->orWhere('program_name', 'LIKE', '%' . $request->input('query') . '%');
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