<?php

namespace App\Models;

use CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
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
            $builder->where('is_homepage', '=', 'False');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
     */

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }

    public function advertisements()
    {
        return $this->belongsToMany('App\Models\Advertisement', 'advertisements_pages', 'page_id', 'advertisement_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
     */

    public function scopePublished($query)
    {
        return $query->where('status', 'PUBLISHED')
            ->where('date', '<=', date('Y-m-d'))
            ->orderBy('date', 'DESC');
    }

    public function scopeSlug($query, $slug)
    {
        return $query->where('slug', '=', $slug);
    }

    public function scopeNoCategory($query)
    {
        return $query->where('category_id', '=', null);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESORS MUTATORS
    |--------------------------------------------------------------------------
     */


}
