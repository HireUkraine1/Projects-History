<?php

/**
 *
 */
class Location extends Eloquent
{
    static $collection = "";
    /** use specific config section **/
    public static $config = 'development';
    public $timestamps = false;
    protected $table = "locations";
    protected $fillable = ['city', 'state', 'country', 'state_full', 'event_count', 'slug'];

    public function geo_location()
    {
        return $this->hasMany('GeoLocation'); //check on keys
    }

    public function concerts()
    {
        return $this->hasMany('Concert'); //check keys
    }

    public function featured()
    {
        return $this->hasOne('FeaturedLocation');
    }

    public function burbs()
    {
        return $this->hasMany('Burb', 'parent_location_id', 'id');
    }

    public function performers()
    {
        return $this->hasManyThrough('Performer', 'Concert', 'location_id', 'id');
    }

}
