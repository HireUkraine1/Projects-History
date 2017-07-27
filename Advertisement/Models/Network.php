<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    public $timestamps = false;

    protected $table = 'networks';

    public function workers()
    {
        return $this->belongsTo('App\Worker');
    }
}
