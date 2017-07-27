<?php

namespace App\Models;

use CrudTrait;
use Illuminate\Database\Eloquent\Model;

class SchoolCategory extends Model
{

    use CrudTrait;

    protected $table = 'school_categories';
    protected $primaryKey = 'id';
    protected $fillable = ['school_id', 'category_id'];

    public function category()
    {
        return $this->hasOne('App\Models\Category', 'id', 'category_id');
    }
}
