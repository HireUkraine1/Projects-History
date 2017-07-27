<?php

namespace App\Models;

use CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use CrudTrait;

    protected $table = 'countries';

}
