<?php

namespace App\Model;

class Option extends AppModel
{
    protected $table = 'options';

    protected $fillable = ['value', 'option_type_id'];

    public function type()
    {
        return $this->belongsTo('App\Model\OptionType', 'option_type_id');
    }

    public function members()
    {
        return $this->belongsToMany('App\Model\Member', 'members_options', 'option_id', 'member_id');
    }
}
