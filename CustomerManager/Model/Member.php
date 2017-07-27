<?php

namespace App\Model;


class Member extends AppModel
{
    protected $table = 'members';

    protected $fillable = ['relation_id', 'first_name', 'last_name',
        'primary_email', 'secondary_email', 'winter_phone', 'summer_phone', 'cell_phone',
        'birthdate', 'winter_state', 'winter_city', 'winter_address',
        'winter_zip_code', 'summer_town', 'summer_address',
        'summer_zip_code', 'service_group', 'order_id', 'summer_state', 'CM_dir'];

    public function order()
    {
        return $this->belongsTo('App\Model\Order', 'order_id');
    }

    public function relation()
    {
        return $this->belongsTo('App\Model\Relation', 'relation_id');
    }

    public function options()
    {
        return $this->belongsToMany('App\Model\Option', 'members_options', 'member_id', 'option_id');
    }

    public function pools()
    {
        return $this->morphedByMany('App\Model\Pool', 'service', 'services')->withPivot('price');
    }

    public function volunteers()
    {
        return $this->morphedByMany('App\Model\VolunteerProgram', 'service', 'services')->withPivot('price');
    }

    public function swims()
    {
        return $this->morphedByMany('App\Model\SwimProgram', 'service', 'services')->withPivot('price');
    }

    public function sailings()
    {
        return $this->morphedByMany('App\Model\SailingProgram', 'service', 'services')->withPivot('price');
    }

    public function dues()
    {
        return $this->morphedByMany('App\Model\Due', 'service', 'services')->withPivot('price');
    }


}


