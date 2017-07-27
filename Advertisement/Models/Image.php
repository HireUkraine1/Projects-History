<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    public $timestamps = false;

    protected $table = 'image_worker_routes';

    public function wokrker()
    {
        return $this->belongsTo('App\Worker');
    }
}
