<?php

namespace App\Models;

use CrudTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use CrudTrait;
    use Sluggable, SluggableScopeHelpers;

    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'slug', 'alias', 'content', 'baner_image', 'thumbnail', 'color', 'lable', 'parent_id', 'meta_title', 'meta_description', 'meta_keywords', 'search_form', 'baner_text', 'slogan', 'short_description', 'title'];


    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
     */

    public function parent()
    {
        return $this->belongsTo('App\Models\Category', 'parent_id')->with('children');
    }

    public function children()
    {
        return $this->hasMany('App\Models\Category', 'parent_id', 'id');
    }

    public function activities()
    {
        return $this->hasMany('App\Models\Activity');
    }

    public function schools()
    {
        return $this->belongsToMany('App\Models\School', 'school_categories', 'category_id', 'school_id');
    }


    public function pages()
    {
        return $this->hasMany('App\Models\Page');
    }

    public function advertisements()
    {
        return $this->belongsToMany('App\Models\Advertisement', 'advertisements_categories', 'category_id', 'advertisement_id');
    }

    public function clubs()
    {
        return $this->hasMany(Club::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Functions
    |--------------------------------------------------------------------------
     */

    public function ScopeGetChildrenBySlug($query, $childrenSlug)
    {
        return $query->with(array('children' => function ($query) use ($childrenSlug) {
            $query->where('slug', '=', $childrenSlug)->first();
        }))->with('children');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
     */

    public function scopeSlug($query, $slug)
    {
        return $query->where('slug', '=', $slug);
    }

    public function scopeNoParent($query)
    {
        return $query->where('parent_id', '=', null);

    }

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
     */

    // The slug is created automatically from the "name" field if no slug exists.
    public function getSlugOrNameAttribute()
    {
        if ($this->slug != '') {
            return $this->slug;
        }

        return $this->name;
    }

    public function getAliasOrNameAttribute()
    {
        if ($this->alias != '') {
            return $this->alias;
        }

        return $this->name;
    }


    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'slug_or_name',
            ],
            'alias' => [
                'source' => 'alias_or_name',
            ],
        ];
    }
}
