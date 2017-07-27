<?php

namespace App\Models;

use CrudTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use CrudTrait;
    use Sluggable, SluggableScopeHelpers;

    protected $table = 'baners';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'title', 'shortcode', 'image', 'button_text', 'link'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'shortcode' => [
                'source' => 'shortcode_or_name',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
     */

    // The slug is created automatically from the "name" field if no slug exists.
    public function getShortcodeOrNameAttribute()
    {
        if ($this->shortcode != '') {
            return $this->shortcode;
        }

        return $this->name;
    }

}
