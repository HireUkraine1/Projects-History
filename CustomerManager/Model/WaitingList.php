<?php

namespace App\Model;

class WaitingList extends AppModel
{
    protected $table = 'waiting_list';

    protected $fillable = ['user_id', 'season_id', 'dock', 'sunfish_dolly', 'kayak_storage', 'locker_renewal', 'dock_waiting', 'size_type_boat'];

    public function orders()
    {
        return $this->hasMany('App\Model\Orders', 'waiting_list_id');
    }
}
