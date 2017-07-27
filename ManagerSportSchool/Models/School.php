<?php

namespace App\Models;

use CrudTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class School extends Authenticatable
{

    use Notifiable;
    use CrudTrait;

    protected $table = 'schools';
    protected $primaryKey = 'id';
    protected $fillable = ['first_name', 'last_name', 'date', 'name', 'business_number',
        'street', 'address_line', 'city', 'state', 'postal', 'phone', 'email', 'website',
        'insurance', 'insurance_start_date', 'insurance_annual_revenue', 'insurance_incidents',
        'fax', 'country', 'approve', 'business_structure', 'password', 'mobile', 'trading_name',
        'postal_mailing', 'street_mailing', 'status_id'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category', 'school_categories', 'school_id', 'category_id');
    }

    public function sportsections()
    {
        return $this->belongsToMany('App\Models\sportSection', 'school_sportsections', 'school_id', 'sportsections_id');
    }

    public function businesses()
    {
        return $this->belongsToMany('App\Models\BusinessStructure', 'school_business', 'school_id', 'business_id');
    }


    public function business()
    {
        return $this->hasOne('App\Models\BusinessStructure', 'business_structure');
    }

    public function landings()
    {
        return $this->hasMany('App\Models\SchoolLanding', 'school_id')->with('category');
    }


    // public function gallery()
    // {
    //     return $this->hasMany('App\Models\SchoolImages', 'school_id');
    // }

    // public function galleries()
    // {
    //     return $this->belongsTo('App\Models\SchoolImages', 'school_id', 'id');
    // }


    public function status()
    {
        return $this->belongsTo('App\Models\SchoolStatus', 'status_id');
    }

    public function previewLanding()
    {
        return $this->hasOne('App\Models\PreviewSchoolLanding', 'school_id');
    }

    public function schoolName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }


    public function setDateAttribute($value)
    {
        $this->attributes['date'] = \Date::parse($value);
    }

    public function setInsuranceStartDateAttribute($value)
    {

        $this->attributes['insurance_start_date'] = \Date::parse($value);
    }

    public function setPasswordAttribute($value)
    {

        $this->attributes['password'] = bcrypt($value);
    }

}
