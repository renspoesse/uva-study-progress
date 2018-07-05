<?php

namespace App\LTI;

use Carbon\Carbon;
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

        if ($request->filled('custom_role_membership')) {

            $this->user->roles = array_merge($this->user->roles, explode(',', $request->input('custom_role_membership')));
        }
        else if ($request->filled('custom_custom_role_membership')) {

            $this->user->roles = array_merge($this->user->roles, explode(',', $request->input('custom_custom_role_membership')));
        }

        $this->user->roles = str_replace('rens_1', 'feb_dashboard_admin', $this->user->roles);
        $this->user->roles = str_replace('rens_2', 'feb_dashboard_studyadviser', $this->user->roles);

        $allowed = [

            'firstname',
            'lastname',
            'fullname',
            'email',
            'image',
            'roles',
            'groups',
            'ltiResultSourcedId', // Only provided when authenticating as student.
            'created',
            'updated'
        ];

        $userInfo = array_filter((array)$this->user, function ($key) use ($allowed) {

            return in_array($key, $allowed);

        }, ARRAY_FILTER_USE_KEY);

        if ($request->filled('custom_student_number')) {

            $userInfo['custom_student_number'] = $request->get('custom_student_number');
        }

        $request->session()->put('user', $userInfo);
        $request->session()->put('authenticated', true);
        $request->session()->put('record_id', $this->user->getRecordId());

        // After completing the onLaunch() method, the LTI library will perform a redirect. The redirect is performed by sending a Location header and stopping script execution.
        // We have to manually save the session here because Laravel's session handling will not be executed when the script is terminated.

        $request->session()->save();

        Log::info('Session id: ' . $request->session()->getId());
        Log::info('Saved LTI information to session: authenticated.');

        // Normally Laravel will sent a cookie with the session ID along with a response object. This cookie is used to identify the session on subsequent requests.
        // In our case however, a response object is never sent (a Location header is sent instead and script execution is stopped).
        // Therefore we have to simulate this session cookie to ensure the current session can be identified on subsequent requests.

        $value  = encrypt($request->session()->getId());
        $expire = config('session.expire_on_close') ? 0 : Carbon::now()->addMinutes(config('session.lifetime'));

        setcookie($request->session()->getName(), $value, $expire, config('session.path'), config('session.domain'), config('session.secure'), config('session.http_only'));

        // The LTI library will now perform the redirect and stop script execution.
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