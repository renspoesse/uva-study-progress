<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends BaseController
{
    public function get()
    {
        return response(Settings::firstOrFail());
    }

    public function updatePartial(Request $request)
    {
        $this->validate($request, $this->getValidatorPartial($request));

        $settings = Settings::firstOrFail();

        DB::transaction(function () use ($request, $settings) {

            if ($request->exists('active_block')) $settings->active_block = $request->input('active_block');

            $settings->save();
        });

        return response(Settings::firstOrFail());
    }

    protected function getValidatorComplete(Request $request)
    {
        return [

            'active_block' => 'required|integer|in:1,2,3,4,5,6'
        ];
    }

    protected function getValidatorPartial(Request $request)
    {
        return [

            'active_block' => 'required|integer|in:1,2,3,4,5,6'
        ];
    }
}