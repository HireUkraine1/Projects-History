<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityPars extends Model
{

    public $timestamps = false;
    protected $table = 'activities';
    protected $primaryKey = 'id';
    protected $fillable = ['category_id', 'name', 'image', 'parent_id', 'level'];

}
