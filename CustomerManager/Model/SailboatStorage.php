<?php

namespace App\Model;

class SailboatStorage extends AppModel
{
    protected $table = 'sailboat_storage';

    protected $fillable = ['name', 'description', 'price'];

    public function orders()
    {
        return $this->belongsToMany('App\Model\Order', 'orders_sailboat_storage', 'sailboat_storage_id', 'order_id')->withPivot(['price', 'count']);
    }
}
