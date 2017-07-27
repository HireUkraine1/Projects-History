<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends \Cartalyst\Sentinel\Users\EloquentUser implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    protected $table = 'users';

    protected $fillable = [
        'tel',
        'email',
        'password',
        'permissions',
    ];

    protected $loginNames = ['tel', 'email'];

    public function worker()
    {
        return $this->hasOne('App\Worker');
    }

}

