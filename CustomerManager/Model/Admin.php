<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class Admin extends Authenticatable implements AppModelInterface
{
    use Notifiable;

    protected $table = 'admins';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'role_id', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function role()
    {
        return $this->belongsTo('App\Model\Role', 'role_id');
    }

    public function scopeGetAll($query)
    {
        $result = $query->get();
        if (count($result) == 0) {
            $result = collect([[]]);
        };
        return $result;
    }
}
