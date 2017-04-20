<?php

namespace App\Enums;

abstract class Roles
{
    const Administrator = 'urn:lti:role:ims/lis/rens_1';
    const Student = 'urn:lti:instrole:ims/lis/Student';
    const StudyAdvisor = 'urn:lti:role:ims/lis/rens_2';
}