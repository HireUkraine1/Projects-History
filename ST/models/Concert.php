<?php

use Purekid\Mongodm\Model;

//http://bundles.laravel.com/bundle/mongodm

/**
 *
 */
class Concert extends Eloquent
{
    static $collection = "";
    /** use specific config section **/
    public static $config = 'development';
    protected $table = "concerts";
    protected $fillable = array('name', 'slug', 'status');

    public static function getCity($concert_id)
    {
        return Location::find(Concert::find($concert_id)->geo_location_id)->city;
    }

    public static function getLocation($concert_id)
    {
        return Location::find(Concert::find($concert_id)->geo_location_id);
    }

    public function performers()
    {
        return $this->belongsToMany('Performer', 'concert_performers');
    }

    public function featured_performers()
    {
        return $this->belongsToMany('FeaturedPerformer', 'concert_performers', 'performer_id', 'performer_id');
    }

    public function venue()
    {
        return $this->belongsTo('Venue');
    }

    public function tnConcert()
    {
        return $this->hasOne('TnConcert');
    }


    public function genres()
    {
        return $this->belongsToMany('Genre', 'concert_genres');
    }

    public function location()
    {
        return $this->belongsTo('Location');
    }

    public function tickets()
    {
        return $this->hasMany('TnTicket', 'concert_id', 'id');
    }

    public function geo_location()
    {
        return $this->hasOne('GeoLocation');
    }

    public function concertTN()
    {
        return $this->hasOne('ConcertTN');
    }

    public function fans()
    {
        return $this->belongsToMany('Fan', 'concert_fan');
    }

}