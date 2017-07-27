<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public $timestamps = false;

    protected $table = 'cities';

    public function workers()
    {
        return $this->belongsToMany('App\Worker', 'worker_city');
    }
}
