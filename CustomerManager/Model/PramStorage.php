<?php

namespace App\Model;

class PramStorage extends AppModel
{
    protected $table = 'pram_storage';

    protected $fillable = ['name', 'description', 'price'];

    public function orders()
    {
        return $this->belongsToMany('App\Model\Order', 'orders_pram_storage', 'pram_storage_id', 'order_id')->withPivot(['price', 'count']);
    }
}
