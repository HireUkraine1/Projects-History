<?php

namespace App\Models;

use CrudTrait;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use CrudTrait;

    protected $table = 'regions';
    protected $primaryKey = 'ID';

}
