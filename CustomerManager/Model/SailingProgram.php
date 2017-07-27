<?php

namespace App\Model;


class SailingProgram extends AppModel
{
    protected $table = 'sailing_programs';

    protected $fillable = ['name', 'price'];

    public function members()
    {
        return $this->morphToMany('App\Model\Member', 'service', 'services')->withPivot('price');
    }
}
