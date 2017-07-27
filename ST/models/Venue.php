<?php

use Purekid\Mongodm\Model;

//http://bundles.laravel.com/bundle/mongodm

/**
 *
 */
class Venue extends Eloquent
{

    static $collection = "";
    /** use specific config section **/
    public static $config = 'development';
    public $timestamps = true;
    protected $table = "venues";
    protected $fillable = array('name', 'slug');

    public function geoLocation()
    {
        return $this->belongsTo('GeoLocation');
    }

    public function concerts()
    {
        return $this->hasMany('Concert');
    }

    public function conerts_after($date = false, $limit = 10)
    {
        if (!$date) $date = date('Y-m-d');
        return $this->hasMany('Concert')->where('date', '>', $date)->orderBy('date', 'asc')->take($limit);
    }

    public function tnVenue()
    {
        return $this->hasOne('TnVenue');
    }


}