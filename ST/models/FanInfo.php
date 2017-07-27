<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

//http://bundles.laravel.com/bundle/mongodm

/**
 *
 */
class FanInfo extends Eloquent
{
    static $collection = "";
    protected $table = "fan_info";

}
