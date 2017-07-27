<?php

namespace App\Model;

class Season extends AppModel
{
    protected $table = 'seasons';

    protected $fillable = ['account_member_can_edit'];

    public function orders()
    {
        return $this->hasMany('App\Model\Orders', 'season_id');
    }
}
