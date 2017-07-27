<?php

namespace App\Model;

class OrderStatus extends AppModel
{
    protected $table = 'order_status';

    protected $fillable = ['name'];

    public function orders()
    {
        return $this->hasMany('App\Model\Orders', 'order_status_id');
    }
}
