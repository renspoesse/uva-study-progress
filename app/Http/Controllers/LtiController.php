<?php

namespace App\Http\Controllers;

use App\LTI\StudyProgressToolProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use IMSGlobal\LTI\ToolProvider\DataConnector;

class LtiController extends BaseController
{
    public function index(Request $request)
    {
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