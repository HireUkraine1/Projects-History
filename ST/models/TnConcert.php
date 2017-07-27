<?php
// use Purekid\Mongodm\Model;
//http://bundles.laravel.com/bundle/mongodm

/**
 *
 */
class TnConcert extends Eloquent
{
    static $collection = "";
    /** use specific config section **/
    public static $config = 'development';
    protected $table = "tn_concerts";
    protected $fillable = array('id');

    public function concert()
    {
        return $this->belongsTo('Concert');
    }
}