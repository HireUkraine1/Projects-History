<?php

/**
 *
 */
class TnVenue extends Eloquent
{
    static $collection = "";
    /** use specific config section **/
    public static $config = 'development';
    public $timestamps = true;
    protected $table = "tn_venues";
    protected $fillable = array();

    public function venue()
    {
        return $this->belongsTo('Venue');
    }

}