<?php

namespace App\Model;

class WinterStorage extends AppModel
{
    protected $table = 'winter_storage';

    protected $fillable = ['name', 'description', 'price'];

    public function orders()
    {
        return $this->belongsToMany('App\Model\Order', 'orders_winter_storage', 'winter_storage_id', 'order_id')->withPivot(['price', 'count']);
    }
}

