<?php

namespace App\LTI;

use IMSGlobal\LTI\ToolProvider;
use IMSGlobal\LTI\ToolProvider\DataConnector\DataConnector;

class StudyProgressToolProvider extends ToolProvider\ToolProvider
{
    protected $redirectUrl = '/'; // Redirects the user here on successful completion.

    function __construct(DataConnector $dataConnector)
    {
        parent::__construct($dataConnector);

        $this->debugMode = config('app.debug');
    }

    function onLaunch()
    {
        // Insert code here to handle incoming connections - use the user,
        // context and resourceLink properties of the class instance
        // to access the current user, context and resource link.

        $allowed = [

            'firstname',
            'lastname',
            'fullname',
            'email',
            'image',
            'roles',
            'groups',
            'ltiResultSourcedId',
            'created',
            'updated'
        ];

        $userInfo = array_filter((array)$this->user, function ($key) use ($allowed) {

            return in_array($key, $allowed);

        }, ARRAY_FILTER_USE_KEY);

        $request = request();

        $request->session()->put('user', $userInfo);
        $request->session()->put('authenticated', true);
        $request->session()->put('record_id', $this->user->getRecordId());

        $request->session()->save();
    }

    function onContentItem()
    {
        // Insert code here to handle incoming content-item requests - use the user and context
        // properties to access the current user and context.
    }

    function onRegister()
    {
        // Insert code here to handle incoming registration requests - use the user
        // property of the $tool_provider parameter to access the current user.
    }

    function onError()
    {
        // Insert code here to handle errors on incoming connections - do not expect
        // the user, context and resourceLink properties to be populated but check the reason
        // property for the cause of the error.  Return TRUE if the error was fully
        // handled by this method.
    }
}