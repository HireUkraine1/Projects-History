<?php

namespace App\Model;

class OptionType extends AppModel
{
    protected $table = 'option_types';

    protected $fillable = ['name'];

    public function option()
    {
        return $this->hasMany('App\Model\Option', 'option_type_id');
    }
}
