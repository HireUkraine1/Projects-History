<?php

namespace App\Models;

use CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use CrudTrait;

    protected $table = 'settings';
    protected $fillable = ['value', 'active'];
}
