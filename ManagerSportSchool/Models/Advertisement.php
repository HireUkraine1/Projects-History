<?php

namespace App\Models;

use CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use CrudTrait;

    protected $table = 'advertisements';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'image', 'link'];

    public function pages()
    {
        return $this->belongsToMany('App\Models\Page', 'advertisements_pages', 'advertisement_id', 'page_id');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category', 'advertisements_categories', 'advertisement_id', 'category_id');
    }
}
