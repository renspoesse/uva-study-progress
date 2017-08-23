<?php

namespace App\Http\Controllers;

use App\LTI\StudyProgressToolProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use IMSGlobal\LTI\ToolProvider\DataConnector;

class LtiController extends BaseController
{
    public function index(Request $request)
    {
        //dd($request->all());

       Log::info('Request method: ' . $request->method());
       Log::info('Request URL: ' . $request->fullUrl());
       //Log::info('Request: ' . print_r($request->all(), true));

        $connector = $this->getConnector();

        // $name = '';
        // $key = '';
        // $secret = '';
        //
        // $consumer = new \IMSGlobal\LTI\ToolProvider\ToolConsumer($key, $connector);
        //
        // $consumer->name = $name;
        // $consumer->secret = $secret;
        // $consumer->enabled = true;
        //
        // $consumer->save();

        $tool = new StudyProgressToolProvider($connector);
        $tool->handleRequest();
    }

    protected function getConnector()
    {
        return DataConnector\DataConnector::getDataConnector('', DB::connection()->getPdo());
    }
}