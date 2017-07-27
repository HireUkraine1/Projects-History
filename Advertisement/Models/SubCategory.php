<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{

    public $timestamps = false;

    protected $table = 'sub_categories';

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function workers()
    {
        return $this->belongsToMany('App\Worker', 'worker_subcategory', 'sub_categories_id', 'worker_id');
    }

}
