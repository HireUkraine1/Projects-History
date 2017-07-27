<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ExistUser extends Model
{
    protected $table = 'exist_users';

    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'status',
        'password'
    ];
}
