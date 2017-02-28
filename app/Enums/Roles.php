<?php

namespace App\Enums;

abstract class Roles
{
    const Administrator = 'urn:lti:role:ims/lis/Administrator';
    const Instructor = 'urn:lti:role:ims/lis/Instructor';
    const Learner = 'urn:lti:role:ims/lis/Learner';
}