<?php

namespace App\Models;

use CrudTrait;
use Illuminate\Database\Eloquent\Model;

class sportSection extends Model
{
    use CrudTrait;

    protected $table = 'sport_section';
    protected $primaryKey = 'id';
    protected $fillable = ['name'];


    public function schools()
    {
        return $this->belongsToMany('App\Models\School', 'school_sportsections', 'sportsections_id', 'school_id');
    }

}
