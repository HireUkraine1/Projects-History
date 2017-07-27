<?php

namespace App\Models;

use CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{

    use CrudTrait;

    public $timestamps = false;
    protected $table = 'activities';
    protected $primaryKey = 'id';
    protected $fillable = ['category_id', 'name', 'image', 'parent_id', 'level'];

    public function setImageAttribute($value)
    {
        $attribute_name = "image";
        $disk = "uploads";
        $destination_path = "activities/images";
        if ($value == null) {
            \Storage::disk($disk)->delete($this->image);
            $this->attributes[$attribute_name] = null;
        }
        if (starts_with($value, 'data:image')) {
            $image = \Image::make($value);
            $filename = md5(rand(1, 100) . $value . time()) . '.jpg';
            \Storage::disk($disk)->put($destination_path . '/' . $filename, $image->stream());
            $this->attributes[$attribute_name] = 'uploads/' . $destination_path . '/' . $filename;
        }
    }


    public function landings()
    {
        return $this->belongsToMany('App\Models\SchoolLanding', 'landing_activities', 'activity_id', 'landing_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }

    public function templates()
    {
        return $this->hasMany('App\Models\ActivityCourses', 'activity_id');
    }

}
