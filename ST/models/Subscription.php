<?php
// use Illuminate\Database\Eloquent\SoftDeletingTrait;
//http://bundles.laravel.com/bundle/mongodm

/**
 *
 */
class Subscription extends Eloquent
{
    static $collection = "";
    protected $table = "subscriptions";


    public function plan()
    {
        return $this->belongsTo('Plan');
    }

    public function fan()
    {
        return $this->belongsTo('Fan');
    }

    public function invoice()
    {
        return $this->hasOne('Invoice');
    }
}
