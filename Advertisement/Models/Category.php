<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $timestamps = false;

    protected $table = 'categories';

    public function subCategories()
    {
        return $this->hasMany('App\SubCategory', 'category_id');
    }

}
