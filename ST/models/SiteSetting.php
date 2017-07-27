<?php
//http://bundles.laravel.com/bundle/mongodm

/**
 *
 */
class SiteSetting extends Eloquent
{
    static $collection = "";
    /** use specific config section **/
    public static $config = 'development';
    protected $table = "site_settings";
    protected $fillable = array('value');


}