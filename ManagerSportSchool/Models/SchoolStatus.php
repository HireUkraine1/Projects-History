<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolStatus extends Model
{
    protected $table = 'school_status';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'name'];
}
