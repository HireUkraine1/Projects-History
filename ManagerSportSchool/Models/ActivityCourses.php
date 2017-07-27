<?php

namespace App\Models;

use CrudTrait;
use Illuminate\Database\Eloquent\Model;

class ActivityCourses extends Model
{
    use CrudTrait;

    protected $table = 'activity_courses';
    protected $primaryKey = 'id';
    protected $fillable = [
        'activity_id',
        'name',
        'description'
    ];


    public function activity()
    {
        return $this->belongsTo('App\Models\Activity', 'activity_id');
    }

    public function landings()
    {
        return $this->belongsToMany('App\Models\SchoolLanding', 'landing_activity_courses', 'activity_course_id', 'landing_id');
    }
}
