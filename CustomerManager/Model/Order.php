<?php

namespace App\Model;

class Order extends AppModel
{
    protected $table = 'orders';

    protected $fillable = ['season_id', 'user_id', 'order_status_id', 'terms', 'waiting_list_id', 'updated_at'];

    public function members()
    {
        return $this->hasMany('App\Model\Member', 'order_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

    public function season()
    {
        return $this->belongsTo('App\Model\Season', 'season_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Model\OrderStatus', 'order_status_id');
    }

    public function waiting_list()
    {
        return $this->belongsTo('App\Model\WaitingList', 'waiting_list_id');
    }

    public function send_summer_email()
    {
        return $this->belongsTo('App\Model\SummerEmail', 'send_summer_mail_id');
    }

    public function docks()
    {
        return $this->belongsToMany('App\Model\Dock', 'orders_docks', 'order_id', 'dock_id')->withPivot('price', 'size_type_boat');
    }

    public function pram_storages()
    {
        return $this->belongsToMany('App\Model\PramStorage', 'orders_pram_storage', 'order_id', 'pram_storage_id')->withPivot(['price', 'count']);
    }

    public function sailboat_storages()
    {
        return $this->belongsToMany('App\Model\SailboatStorage', 'orders_sailboat_storage', 'order_id', 'sailboat_storage_id')->withPivot(['price', 'count']);
    }

    public function winter_storages()
    {
        return $this->belongsToMany('App\Model\WinterStorage', 'orders_winter_storage', 'order_id', 'winter_storage_id')->withPivot(['price', 'count']);
    }

}
