<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Baner extends Model
{

    public $timestamps = false;

    protected $table = 'baner';

    public function category()
    {
        return $this->belongsTo('App\City');
    }
}
