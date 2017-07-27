<?php
// use Illuminate\Database\Eloquent\SoftDeletingTrait;
//http://bundles.laravel.com/bundle/mongodm

/**
 *
 */
class Coupon extends Eloquent
{
    static $collection = "";
    protected $table = "coupons";

    public function invoices()
    {
        return $this->hasMany('Invoice');
    }


}
