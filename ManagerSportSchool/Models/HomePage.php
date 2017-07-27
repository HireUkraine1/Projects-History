<?php

namespace App\Models;

use CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HomePage extends Model
{
    use CrudTrait;

    public $timestamps = true;
    protected $table = 'pages';
    protected $primaryKey = 'id';
    protected $fillable = ['slug', 'name', 'content', 'thumbnail', 'status', 'category_id', 'meta_title', 'meta_description', 'meta_keywords', 'search_form', 'baner_image', 'baner_text', 'slogan', 'is_homepage', 'title'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('is_homepage', function (Builder $builder) {
            $builder->where('is_homepage', '=', 'True');
        });
    }

}
