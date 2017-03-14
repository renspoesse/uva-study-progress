<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Helpers\RoleHelpers;
use App\Helpers\PaginationHelpers;
use App\Models\News;
use Illuminate\Http\Request;
use Validator;
use DB;

class NewsController extends BaseController
{
    public function create(Request $request)
    {
        $this->validate($request, $this->getValidatorComplete($request));

        DB::beginTransaction();

        try {

            $news = new News();

            $news->title = $request->input('title');
            $news->text = $request->input('text');
            $news->is_published = $request->input('is_published');

            $news->save();

            DB::commit();

            return response(News::find($news->id), 201);
        }
        catch (\Exception $ex) {

            DB::rollBack();
            throw $ex;
        }
    }

    public function getById($id)
    {
        return response(News::where('id', $id)->firstOrFail());
    }

    public function deleteById($id)
    {
        News::where('id', $id)->delete();
    }

    public function index(Request $request)
    {
        $this->validate($request, [

            'order'         => 'string|nullable',
            'publishedOnly' => 'nullable',
            'query'         => 'string|nullable'
        ]);

        $query = News::query();

        if (filter_var($request->input('publishedOnly'), FILTER_VALIDATE_BOOLEAN) === true || !RoleHelpers::hasAnyRole($request, [Roles::Instructor, Roles::Administrator]))
            $query = $query->where('is_published', true);

        if ($request->has('query')) {

            $query = $query->where('title', 'LIKE', '%' . $request->input('query') . '%');
            $query = $query->orWhere('text', 'LIKE', '%' . $request->input('query') . '%');
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

        $news = News::findOrFail($id);

        DB::transaction(function () use ($id, $request, $news) {

            if ($request->exists('title')) $news->title = $request->input('title');
            if ($request->exists('text')) $news->text = $request->input('text');
            if ($request->exists('is_published')) $news->is_published = $request->input('is_published');

            $news->save();
        });

        return response(News::find($news->id));
    }

    protected function getValidatorComplete(Request $request)
    {
        return [

            'title'        => 'required|string|max:128|filled',
            'text'         => 'required|string',
            'is_published' => 'required|boolean'
        ];
    }

    protected function getValidatorPartial(Request $request)
    {
        return [

            'title'        => 'string|max:128|filled',
            'text'         => 'string',
            'is_published' => 'boolean'
        ];
    }
}