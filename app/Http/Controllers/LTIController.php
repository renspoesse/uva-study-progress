<?php

namespace App\Http\Controllers;

use DB;
use IMSGlobal\LTI\ToolProvider\DataConnector;
use IMSGlobal\LTI\ToolProvider;
use App\LTI\StudyProgressToolProvider;

class LTIController extends Controller
{
    public function index()
    {
        $connector = DataConnector\DataConnector::getDataConnector('', DB::connection()->getPdo());

        /*
        $consumer = new ToolProvider\ToolConsumer('testing.edu', $connector);

        $consumer->name = 'Testing';
        $consumer->secret = 'ThisIsASecret!';
        $consumer->enabled = true;

        $consumer->save();
        */

        $tool = new StudyProgressToolProvider($connector);
        $tool->handleRequest();
    }
}