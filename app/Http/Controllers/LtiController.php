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

    /*
    public function testAdmin(Request $request)
    {
        $params = $this->getBlackboardRequest(['urn:lti:instrole:ims/lis/Administrator']);
        $params = $this->signRequest($params);

        return $this->sendProxiedRequest($params);
    }

    public function testStudent(Request $request)
    {
        $params = $this->getBlackboardRequest(['Learner']);
        $params = $this->signRequest($params);

        return $this->sendProxiedRequest($params);
    }

    public function testStudyAdvisor(Request $request)
    {
        $params = $this->getBlackboardRequest(['Instructor']);
        $params = $this->signRequest($params);

        return $this->sendProxiedRequest($params);
    }
    */

    protected function getConnector()
    {
        return DataConnector\DataConnector::getDataConnector('', DB::connection()->getPdo());
    }

    /*
    protected function sendProxiedRequest($parameters)
    {
        $client = new Client();

        $response = $client->post('https://studyprogress.joinkinder.org/lti/launch', [

            'form_params' => $parameters
        ]);

        return response($response->getBody(), $response->getStatusCode());
    }

    protected function signRequest($parameters)
    {
        $consumer = new OAuthConsumer('testing.edu', 'ThisIsASecret!');
        $token = new OAuthToken($consumer, '');

        $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'POST', 'https://studyprogress.joinkinder.org/lti/launch', $parameters);

        $signer = new OAuthSignatureMethod_HMAC_SHA1();
        $request->sign_request($signer, $consumer, $token);

        return $request->get_parameters();
    }

    private function getBlackboardRequest(array $roles = [])
    {
        return [

            "lti_message_type"                       => "basic-lti-launch-request",
            "lti_version"                            => "LTI-1p0",
            "resource_link_id"                       => "_101809_1_5780576_1",
            "resource_link_title"                    => "Live Training Recordings",
            "resource_link_description"              => "<p>The 'Did You Know?' Live Training Series augments this self-paced course and delves deeper into online course creation and facilitation topics.</p>",
            "user_id"                                => "_92554_1",
            "roles"                                  => $roles,
            "lis_person_name_full"                   => "Sean Connery",
            "lis_person_name_family"                 => "Connery",
            "lis_person_name_given"                  => "Sean",
            "lis_person_contact_email_primary"       => "sean@adomain.com",
            "lis_person_sourcedid"                   => "s92304945",
            "user_image"                             => "http://ltiapps.net/test/images/lti.gif",
            "context_id"                             => "_101809_1",
            "context_type"                           => "CourseSection",
            "context_title"                          => "Getting Started with CourseSites",
            "context_label"                          => "getting",
            "lis_course_offering_sourcedid"          => "C3445",
            "lis_course_section_sourcedid"           => "C3445:14A",
            "tool_consumer_info_product_family_code" => "learn",
            "tool_consumer_info_version"             => "9.1.140152",
            "tool_consumer_instance_guid"            => "f286ecaca8d548eeb5a22e58354f55fa",
            "tool_consumer_instance_name"            => "UofT",
            "tool_consumer_instance_description"     => "The University of Testing",
            "tool_consumer_instance_contact_email"   => "webmaster@learn9.com",
            "tool_consumer_instance_url"             => "http://ltiapps.net",
            "launch_presentation_return_url"         => "http://ltiapps.net/test/tc-return.php?id=launch&course_id_101809_1&content_id=_5780576_1",
            "launch_presentation_css_url"            => "http://ltiapps.net/test/css/tc.css",
            "launch_presentation_locale"             => "en_US",
            "launch_presentation_document_target"    => "frame",
            "lis_outcome_service_url"                => "http://ltiapps.net/test/tc-outcomes.php",
            "lis_result_sourcedid"                   => "6b41b7416c6fe6ba9caa70b49c34cf3e:::_101809_1:::_92554_1:::ddec3js418",
            "ext_ims_lis_basic_outcome_url"          => "http://ltiapps.net/test/tc-ext-outcomes.php",
            "ext_ims_lis_resultvalue_sourcedids"     => "decimal,percentage,ratio,passfail,letteraf,letterafplus,freetext",
            "ext_ims_lis_memberships_url"            => "http://ltiapps.net/test/tc-ext-memberships.php",
            "ext_ims_lis_memberships_id"             => "6b41b7416c6fe6ba9caa70b49c34cf3e:::4jflkkdf9s",
            "ext_ims_lti_tool_setting_url"           => "http://ltiapps.net/test/tc-ext-setting.php",
            "ext_ims_lti_tool_setting_id"            => "6b41b7416c6fe6ba9caa70b49c34cf3e:::d94gjklf954kj",
            "custom_tc_profile_url"                  => "http://ltiapps.net/test/tc-profile.php/6b41b7416c6fe6ba9caa70b49c34cf3e",
            "custom_system_setting_url"              => "http://ltiapps.net/test/tc-settings.php/system/6b41b7416c6fe6ba9caa70b49c34cf3e",
            "custom_context_setting_url"             => "http://ltiapps.net/test/tc-settings.php/context/6b41b7416c6fe6ba9caa70b49c34cf3e",
            "custom_link_setting_url"                => "http://ltiapps.net/test/tc-settings.php/link/6b41b7416c6fe6ba9caa70b49c34cf3e"
        ];
    }

    private function getCanvasRequest(array $roles = [])
    {
        return [

            "lti_message_type"                         => "basic-lti-launch-request",
            "lti_version"                              => "LTI-1p0",
            "resource_link_id"                         => "6c91bbd81282ca4b12c02887e8b71bcaef2a38e2",
            "resource_link_title"                      => "Essential text",
            "user_id"                                  => "ade4ea9150fabd4ed32e0c27defd20ef6979c940",
            "roles"                                    => $roles,
            "lis_person_name_full"                     => "Jack Vettriano",
            "lis_person_name_family"                   => "Vettriano",
            "lis_person_name_given"                    => "Jack",
            "lis_person_contact_email_primary"         => "jack@vettriano.com",
            "user_image"                               => "https://secure.gravatar.com/avatar/000?s=50&d=https%3A%2F%2Fcanvas.instructure.com%2Fimages%2Fmessages%2Favatar-50.png",
            "context_id"                               => "6dea912122e999239a4663d99fc96ee4507a10bd",
            "context_title"                            => "Critical Thinking",
            "context_label"                            => "Critical Thinking",
            "tool_consumer_info_product_family_code"   => "canvas",
            "tool_consumer_info_version"               => "cloud",
            "tool_consumer_instance_guid"              => "05e55907852a5a4c2713937691a9a49c6939be99.acme.instructure.com",
            "tool_consumer_instance_name"              => "Trial",
            "tool_consumer_instance_contact_email"     => "notifications@instructure.com",
            "launch_presentation_return_url"           => "http://ltiapps.net/test/tc-return.php/courses/1623302",
            "launch_presentation_locale"               => "en-GB",
            "launch_presentation_document_target"      => "iframe",
            "lis_outcome_service_url"                  => "http://ltiapps.net/test/tc-outcomes.php/api/lti/v1/tools/63258/grade_passback",
            "lis_result_sourcedid"                     => "6b41b7416c6fe6ba9caa70b49c34cf3e:::6dea912122e999239a4663d99fc96ee4507a10bd:::ade4ea9150fabd4ed32e0c27defd20ef6979c940:::63258-1623302-6419650-3558075-c29475ac7 ▶",
            "ext_ims_lis_basic_outcome_url"            => "http://ltiapps.net/test/tc-outcomes.php/api/lti/v1/tools/63258/ext_grade_passback",
            "ext_outcome_data_values_accepted"         => "url,text",
            "custom_canvas_api_domain"                 => "acme.instructure.com",
            "custom_canvas_assignment_id"              => "6419650",
            "custom_canvas_assignment_points_possible" => "100",
            "custom_canvas_assignment_title"           => "Assignments 1",
            "custom_canvas_course_id"                  => "1623302",
            "custom_canvas_enrollment_state"           => "active",
            "custom_canvas_user_id"                    => "3558075",
            "custom_canvas_user_login_id"              => "a93dfc2f3b765af7719d220a3f14601d4c9219d8"
        ];
    }
    */
}