<?php

namespace App\Enums;

abstract class Roles
{
    const Administrator = 'rens_1';
    const Student = 'urn:lti:role:ims/lis/Learner'; // urn:lti:instrole:ims/lis/Student
    const StudyAdviser = 'rens_2'; // urn:lti:role:ims/lis/Instructor
}