<?php

class SlugRedirect extends Eloquent
{
    static $collection = "";
    /** use specific config section **/
    public static $config = 'development';
    public $timestamps = true;
    protected $table = "active_redirects";
    protected $fillable = array('url', 'to_url', 'status');

}