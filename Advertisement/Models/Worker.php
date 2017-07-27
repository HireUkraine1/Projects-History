<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    protected $table = 'workers';

    public function tags()
    {
        return $this->hasMany('App\Tag', 'worker_id');
    }

    public function cities()
    {
        return $this->belongsToMany('App\City', 'worker_city');
    }

    public function sub_categories()
    {
        return $this->belongsToMany('App\SubCategory', 'worker_subcategory', 'worker_id', 'sub_categories_id');
    }

    public function videos()
    {
        return $this->hasMany('App\Video', 'worker_id');
    }

    public function images()
    {
        return $this->hasMany('App\Image', 'worker_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function networks()
    {
        return $this->hasMany('App\Network', 'worker_id');
    }
}
