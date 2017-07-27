<?php

namespace App\Model;

class Relation extends AppModel
{
    protected $table = 'relations';

    protected $fillable = ['name'];

    public function members()
    {
        return $this->hasMany('App\Model\Member', 'relation_id');
    }
}
