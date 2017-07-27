<?php

namespace App\Model;


class VolunteerProgram extends AppModel
{
    protected $table = 'volunteer_programs';

    protected $fillable = ['name', 'status'];

    public function members()
    {
        return $this->morphToMany('App\Model\Member', 'service', 'services')->withPivot('price');
    }
}
