<?php

/**
 *
 */
class Image extends Eloquent
{
    static $collection = "";
    /** use specific config section **/
    public static $config = 'development';
    public $fillable = ['path'];
    protected $table = "images";


}