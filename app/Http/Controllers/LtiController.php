<?php

namespace App\Http\Controllers;

use App\LTI\StudyProgressToolProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use IMSGlobal\LTI\ToolProvider\DataConnector;

class LtiController extends Controller
{
    public function index(Request $request)
    {
        // The LTI library relies on some server variables that are not set when running behind a proxy.

        $_SERVER['SERVER_NAME'] = 'studyprogress.uva.nl';

        if ($request->header('X-Forwarded-Proto') === 'https') {
            $_SERVER['HTTPS'] = 'on';
            $_SERVER['SERVER_PORT'] = 443;
        }

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
