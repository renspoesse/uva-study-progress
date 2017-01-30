<?php

namespace App\Http\Controllers;

use DB;
use IMSGlobal\LTI\ToolProvider\DataConnector;
use IMSGlobal\LTI\ToolProvider;
use App\LTI\StudyProgressToolProvider;
use Carbon\Carbon;

class LTIController extends Controller
{
    public function index()
    {
        $connector = $this->getConnector();

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

    public function testAdmin()
    {
        $provider = new StudyProgressToolProvider($this->getConnector());

        $provider->user = [

            'userId'             => '12345678',
            'firstname'          => 'Peter',
            'lastname'           => 'Janssen',
            'fullname'           => 'Peter Janssen',
            'email'              => 'p.janssen@uva.nl',
            'image'              => 'http://ltiapps.net/test/images/lti.gif',
            'roles'              => ['urn:lti:instrole:ims/lis/Administrator'],
            'groups'             => [],
            'ltiResultSourcedId' => null,
            'created'            => Carbon::now(),
            'updated'            => Carbon::now()
        ];

        $provider->onLaunch();

        header('Location: /');
        exit;
    }

    public function testStudent()
    {
        $provider = new StudyProgressToolProvider($this->getConnector());

        $provider->user = [

            'userId'             => '12345678',
            'firstname'          => 'Jan',
            'lastname'           => 'de Ridder',
            'fullname'           => 'Jan de Ridder',
            'email'              => 'jan.de.ridder@student.uva.nl',
            'image'              => 'http://ltiapps.net/test/images/lti.gif',
            'roles'              => ['urn:lti:instrole:ims/lis/Student'],
            'groups'             => [],
            'ltiResultSourcedId' => null,
            'created'            => Carbon::now(),
            'updated'            => Carbon::now()
        ];

        $provider->onLaunch();

        header('Location: /');
        exit;
    }

    public function testStudyAdvisor()
    {
        $user = new ToolProvider\User();

        $user->firstname = 'Rosa';
        $user->lastname = 'Roozen';
        $user->fullname = 'Rosa Roozen';
        $user->email = 'r.roozen@uva.nl';
        $user->image = 'http://ltiapps.net/test/images/lti.gif';
        $user->roles = ['urn:lti:instrole:ims/lis/Staff'];
        $user->groups = [];
        $user->ltiResultSourcedId = '264b2edd7a250b2b00c8400b84452e5b:::S3294476:::29123:::dyJ86SiwwA9';
        $user->created = Carbon::now();
        $user->updated = Carbon::now();

        //$user->dataConnector = $this->getConnector();
        //$user->setResourceLinkId(1);

        /*
        $user = [

            'userId'             => '12345678',
            'firstname'          => 'Rosa',
            'lastname'           => 'Roozen',
            'fullname'           => 'Rosa Roozen',
            'email'              => 'r.roozen@uva.nl',
            'image'              => 'http://ltiapps.net/test/images/lti.gif',
            'roles'              => ['urn:lti:instrole:ims/lis/Staff'],
            'groups'             => [],
            'ltiResultSourcedId' => null,
            'created'            => Carbon::now(),
            'updated'            => Carbon::now()
        ];
        */

        $user->save();

        dd($user->getRecordId());

        $provider = new StudyProgressToolProvider($this->getConnector());
        $provider->user = $user;

        $provider->onLaunch();

        header('Location: /');
        exit;
    }

    protected function getConnector()
    {
        return DataConnector\DataConnector::getDataConnector('', DB::connection()->getPdo());
    }
}