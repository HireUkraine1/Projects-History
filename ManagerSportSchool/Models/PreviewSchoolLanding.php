<?php

namespace App\Models;

use CrudTrait;
use Illuminate\Database\Eloquent\Model;

class PreviewSchoolLanding extends Model
{

    use CrudTrait;

    protected $table = 'preview_school_landing';
    protected $primaryKey = 'id';
    protected $fillable = ['landing_id', 'banner', 'about_us', 'meet_team', 'service_overview', 'location_features',
        'tourist_attributes', 'accomodations', 'features', 'thumbnail', 'token'];

    public function images()
    {
        return $this->hasMany('App\Models\LandingImages', 'landing_id');
    }

    public function setTokenAttribute()
    {
        $this->attributes['token'] = uniqid();
    }
}
