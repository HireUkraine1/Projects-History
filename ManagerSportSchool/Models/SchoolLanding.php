<?php

namespace App\Models;

use CrudTrait;
use Illuminate\Database\Eloquent\Model;

class SchoolLanding extends Model
{

    use CrudTrait;

    protected $table = 'school_landings';
    protected $primaryKey = 'id';
    protected $fillable = ['school_id', 'banner', 'about_us', 'meet_team', 'service_overview', 'location_features',
        'tourist_attributes', 'accomodations', 'features', 'active', 'thumbnail', 'sport'];

    public function images()
    {
        return $this->hasMany('App\Models\LandingImages', 'landing_id');
    }

    public function locations()
    {
        return $this->hasMany('App\Models\LandingLocation', 'landing_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'sport');
    }

    public function school()
    {
        return $this->belongsTo('App\Models\School', 'school_id');
    }

    public function activities()
    {
        return $this->belongsToMany('App\Models\Activity', 'landing_activities', 'landing_id', 'activity_id');
    }

    public function templates()
    {
        return $this->belongsToMany('App\Models\ActivityCourses', 'landing_activity_courses', 'landing_id', 'activity_course_id');
    }

    public function setBannerAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['banner'] = $value;
        } else {
            $school_id = $this->getAttribute('school_id');
            $attribute_name = "banner";
            $disk = "uploads";
            $destination_path = "school_images/$school_id/banners";
            $this->uploadImage($value, $attribute_name, $disk, $destination_path);
        }

    }

    private function uploadImage($value, $attribute_name, $disk, $destination_path)
    {
        if ($value == null) {
            \Storage::disk($disk)->delete($this->image);
            $this->attributes[$attribute_name] = null;
        }
        if ($filename = $this->saveStoreImage($value, $disk, $destination_path, $attribute_name)) {
            $this->attributes[$attribute_name] = 'uploads/' . $destination_path . '/' . $filename;
        }

    }

    private function saveStoreImage($value, $disk, $destination_path)
    {
        if (starts_with($value, 'data:image')) {
            $image = \Image::make($value);
            $filename = md5(rand(1, 100) . $value . time()) . '.jpg';
            \Storage::disk($disk)->put($destination_path . '/' . $filename, $image->stream());
            return $filename;
        }
        return false;
    }

    public function setThumbnailAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['thumbnail'] = $value;
        } else {
            $school_id = $this->getAttribute('school_id');
            $attribute_name = "thumbnail";
            $disk = "uploads";
            $destination_path = "school_images/$school_id/thumbnails";
            $this->uploadImage($value, $attribute_name, $disk, $destination_path);
        }

    }

    public function setMeetTeamAttribute($value)
    {
        $attribute_name = 'meet_team';
        if (json_decode($value)) {
            $school_id = $this->getAttribute('school_id');
            $disk = "uploads";
            $destination_path = "school_images/$school_id/teams";

            $data = json_decode($value, true);
            foreach ($data as $key => $item) {
                if ($filename = $this->saveStoreImage($item['src'], $disk, $destination_path)) {
                    $data[$key]['src'] = 'uploads/' . $destination_path . '/' . $filename;
                }
            }
            $this->attributes[$attribute_name] = json_encode($data);
        }
    }

    public function scopeGetLandingByName($query, $name)
    {
        $query->whereHas('school', function ($q) use ($name) {
            $q->where('name', '=', $name);
        });
    }

    public function scopeGetLandingByLocation($query, $location)
    {
        $query->whereHas('locations', function ($q) use ($location) {
            $q->where('address', 'like', '%' . $location . '%')->orWhere('alias', '=', $location)->orWhere('country', '=', $location);
        });
    }

    public function scopeGetLandingByCategory($query, $category)
    {
        $query->where('sport', function ($q) use ($category) {
            $q->select('id')->from('categories')->where('name', '=', $category);
        });
    }

    public function scopeGetLandingByActivity($query, $activity)
    {
        $query->whereHas('activities', function ($q) use ($activity) {
            $q->where('name', '=', $activity);
        });
    }

}
