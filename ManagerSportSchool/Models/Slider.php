<?php

namespace App\Models;

use CrudTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use CrudTrait;
    use Sluggable, SluggableScopeHelpers;

    protected $table = 'sliders';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'shortcode', 'blocks'];

    public function sluggable()
    {
        return [
            'shortcode' => [
                'source' => 'shortcode_or_name',
            ],
        ];
    }

    // The shortcode is created automatically from the "name" field if no shortcode exists.
    public function getShortcodeOrNameAttribute()
    {
        if ($this->shortcode != '') {
            return $this->shortcode;
        }

        return $this->name;
    }

}
