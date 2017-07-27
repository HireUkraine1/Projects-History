<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

//http://bundles.laravel.com/bundle/mongodm

/**
 *
 */
class Fan extends Eloquent
{
    static $collection = "";
    protected $table = "fans";

    public function profiles()
    {
        return $this->hasMany('Profile');
    }

    public function locations()
    {
        return $this->belongsToMany('Location', 'location_fans');
    }

    public function performers()
    {
        return $this->belongsToMany('Performer', 'performer_fans');
    }

    public function notifications()
    {
        return $this->hasMany('NotificationSettings');
    }

    public function info()
    {
        return $this->hasOne('FanInfo', 'fan_id');
    }

    public function subscriptions()
    {
        return $this->hasMany('Subscription');
    }

    public function notes()
    {
        return $this->hasMany('FanNote');
    }

    public function maillists()
    {
        return $this->belongsToMany('Maillist', 'fan_maillist')->withPivot('status');
    }

    public function newsletters()
    {
        return $this->belongsToMany('Newsletter', 'fan_newsletter')->withPivot('send_on', 'status', 'reject_reason', 'mandrill_id');
    }

    public function concerts()
    {
        return $this->belongsToMany('Concert', 'concert_fan');
    }
}
