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

        // $consumer = new \IMSGlobal\LTI\ToolProvider\ToolConsumer('2ndYD.blackboard', $connector);
        //
        // $consumer->name = '2ndYD Blackboard';
        // $consumer->secret = 'IjbLSnp-p5E|VY6H!';
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