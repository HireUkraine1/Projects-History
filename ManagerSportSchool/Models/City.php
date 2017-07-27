<?php

namespace App\Models;

use CrudTrait;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use CrudTrait;

    protected $table = 'cities';
    protected $primaryKey = 'ID';

}
