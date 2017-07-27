<?php

/**
 *
 */
class TnTicket extends Eloquent
{
    static $collection = "";
    /** use specific config section **/
    public static $config = 'development';
    public $fillable = ['tn_id'];
    protected $table = "tn_tickets";

    public function concert()
    {
        return $this->belongsTo('Concert');
    }

}