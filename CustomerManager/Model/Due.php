<?php

namespace App\Model;

class Due extends AppModel
{
    protected $table = 'dues';

    protected $fillable = ['name', 'description', 'price'];

    public function members()
    {
        return $this->morphToMany('App\Model\Member', 'service', 'services')->withPivot('price');
    }
}
