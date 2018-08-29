<?php

namespace App\Models;

use App\Models\Base\BaseModel;

class Student extends BaseModel
{
    protected $table = 'students';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['program_satisfaction'];

    public function getProgramSatisfactionAttribute()
    {
        return $this->attributes['program_satisfaction_b' . Settings::first()->active_block];
    }

    public function setProgramSatisfactionAttribute($value)
    {
        $this->attributes['program_satisfaction_b' . Settings::first()->active_block] = $value;
    }
}