<?php

namespace App\Model;

class Role extends AppModel
{
    protected $table = 'roles';

    protected $fillable = ['name'];


    public function admins()
    {
        return $this->hasMany('App\Model\Admin', 'role_id');
    }
}
