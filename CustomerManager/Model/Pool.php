<?php

namespace App\Model;

class Pool extends AppModel
{
    protected $table = 'pools';

    protected $fillable = ['type', 'price'];

    public function members()
    {
        return $this->morphToMany('App\Model\Member', 'service', 'services')->withPivot('price');
    }
}
