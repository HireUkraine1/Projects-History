<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Permission extends Eloquent
{

    public $timestamps = false;
    protected $table = "permissions";

    public function adminPermissions()
    {
        return $this->hasMany('AdminPermission');
    }
}
