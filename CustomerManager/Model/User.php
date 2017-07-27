<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable implements AppModelInterface
{
    use Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'entrance_fee',
        'status',
        'balance',
        'password',
        'note',
        'note_order'
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

    public function orders()
    {
        return $this->hasMany('App\Model\Order', 'user_id');
    }

    public function members()
    {
        return $this->hasManyThrough('App\Model\Member', 'App\Model\Order', 'user_id', 'order_id');
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
