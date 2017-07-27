<?php

namespace App\Statistic;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    protected $table='statistics';

    protected $fillable = [
        'serialize_object'
    ];
}
