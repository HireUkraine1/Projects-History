<?php

namespace App\Models;

use CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{

    use CrudTrait;

    protected $table = 'school_courses';
    protected $primaryKey = 'id';
    protected $fillable = [
        'school_id',
        'landing_id',
        'activity_id',
        'landing_locations_id',
        'activity_courses_id',
        'date',
        'quantity_lessons',
        'quantity_places',
        'busy_places',
        'price'
    ];


    public function address()
    {
        return $this->belongsTo('App\Models\LandingLocation', 'landing_locations_id');
    }

    public function landing()
    {
        return $this->belongsTo('App\Models\SchoolLanding', 'landing_id');
    }

    public function school()
    {
        return $this->belongsTo('App\Models\School', 'school_id');
    }

    public function activity()
    {
        return $this->belongsTo('App\Models\Activity', 'activity_id');
    }

    public function template()
    {
        return $this->belongsTo('App\Models\ActivityCourses', 'activity_courses_id');
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = \Date::parse($value);
    }

}
