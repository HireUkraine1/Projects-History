<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'workers_tags';

    public function worker()
    {
        return $this->belongsTo('App\Worker');
    }

}
