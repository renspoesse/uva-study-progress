<?php

namespace App\Enums;

abstract class Roles
{
    const Administrator = 'feb_dashboard_admin';
    const Student = 'urn:lti:role:ims/lis/Learner'; // urn:lti:instrole:ims/lis/Student
    const StudyAdviser = 'feb_dashboard_studyadviser'; // urn:lti:role:ims/lis/Instructor
}
