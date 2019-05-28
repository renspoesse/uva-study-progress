<template>

    <div>

        <alert type="info" message="You need to be authenticated to view this page. Please go (back) to Canvas/Blackboard and open the StudyProgress dashboard from there!" show="true" v-bind:closeable="false"></alert>
        <!--<alert type="danger" message="You need to be authenticated to view this page. Currently only LTI authentication is supported." show="true" v-bind:closeable="false"></alert>-->

        <!--a class="btn btn-primary-outline m-b-s" href="/lti/launch/teststudent">Test as student</a><br>
        <a class="btn btn-primary-outline m-b-s" href="/lti/launch/teststudyadviser">Test as study adviser</a><br>
        <a class="btn btn-primary-outline" href="/lti/launch/testadmin">Test as administrator</a><br-->

        <!--<a class="btn btn-primary-outline" href="http://ltiapps.net/test/tc.php" target="_blank">Go to LTI testing tool</a>-->

    </div>

</template>
