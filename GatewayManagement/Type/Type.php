<?php

namespace App\Forward\Type;

use App\Database\Models\Model;


class Type extends Model
{
    public $table = 'forward_types';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}