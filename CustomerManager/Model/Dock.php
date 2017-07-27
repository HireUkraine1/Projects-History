<?php

namespace App\Model;


class Dock extends AppModel
{
    protected $table = 'docks';

    protected $fillable = ['name', 'description', 'price'];

    public function orders()
    {
        return $this->belongsToMany('App\Model\Order', 'orders_docks', 'dock_id', 'order_id')->withPivot('price', 'size_type_boat');
    }
}
