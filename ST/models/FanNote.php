<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

//http://bundles.laravel.com/bundle/mongodm

/**
 *
 */
class FanNote extends Eloquent
{
    static $collection = "";
    protected $table = "fan_notes";

    public function fan()
    {
        return $this->belongsTo('Fan');
    }

    public function admin()
    {
        return $this->belongsTo('AdminUser');
    }

}
