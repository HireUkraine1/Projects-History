<?php

class Profile extends Eloquent
{

    protected $fillable = array('provider', 'fan_id');

    public function fan()
    {
        return $this->belongsTo('Fan');
    }
}

?>