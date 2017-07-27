<?php

namespace App\Model;


class SwimProgram extends AppModel
{
    protected $table = 'swim_programs';

    protected $fillable = ['name', 'price'];

    public function members()
    {
        return $this->morphToMany('App\Model\Member', 'service', 'services')->withPivot('price');
    }
}
