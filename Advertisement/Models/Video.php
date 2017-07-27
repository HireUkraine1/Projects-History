<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    public $timestamps = false;

    protected $table = 'video_worker_links';

    public function wokrker()
    {
        return $this->belongsTo('App\Worker');
    }
}
