<?php

namespace App\LTI;

use IMSGlobal\LTI\ToolProvider;
use IMSGlobal\LTI\ToolProvider\DataConnector\DataConnector;
use Illuminate\Support\Facades\Log;

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

        Log::info('Processing LTI launch request.');

        $request = request();

        if ($request->has('custom_role_membership')) {

            $this->user->roles = array_merge($this->user->roles, explode(',', $request->input('custom_role_membership')));
        }
        else if ($request->has('custom_custom_role_membership')) {

            $this->user->roles = array_merge($this->user->roles, explode(',', $request->input('custom_custom_role_membership')));
        }

        $allowed = [

            'firstname',
            'lastname',
            'fullname',
            'email',
            'image',
            'roles',
            'groups',
            'ltiResultSourcedId', // Only provided when authenticating in as student.
            'created',
            'updated'
        ];

        $userInfo = array_filter((array)$this->user, function ($key) use ($allowed) {

            return in_array($key, $allowed);

        }, ARRAY_FILTER_USE_KEY);

        if ($request->has('custom_student_number')) {

            $userInfo['custom_student_number'] = $request->get('custom_student_number');
        }

        $request->session()->put('user', $userInfo);
        $request->session()->put('authenticated', true);
        $request->session()->put('record_id', $this->user->getRecordId());

        $request->session()->save();

        Log::info('Session id: ' . $request->session()->getId());
        Log::info('Saved LTI information to session: authenticated.');
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

        Log::error('LTI request rejected. Reason: ' . $this->reason);
    }
}