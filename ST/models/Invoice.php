<?php
// use Illuminate\Database\Eloquent\SoftDeletingTrait;
//http://bundles.laravel.com/bundle/mongodm

/**
 *
 */
class Invoice extends Eloquent
{
    static $collection = "";
    protected $table = "invoices";


    public function subscription()
    {
        return $this->belongsTo('Subscription');
    }

    public function coupon()
    {
        return $this->belongsTo('Coupon');
    }

}
