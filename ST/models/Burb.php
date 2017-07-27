<?php

/**
 *
 */
class Burb extends Eloquent
{
    static $collection = "";
    /** use specific config section **/
    public static $config = 'development';
    public $timestamps = true;
    protected $table = "burbs";
    protected $fillable = ['parent_location_id', 'location_id'];

    public function location()
    {
        return $this->belongsTo('Location', 'location_id');
    }

    public function metro()
    {
        return $this->belongsTo('Location', 'parent_location_id');
    }

}