<?php

/**
 *
 */
class Category extends Eloquent
{
    static $collection = "";
    /** use specific config section **/
    public static $config = 'development';
    protected $table = "categories";
    protected $fillable = ['category', 'slug', 'order'];

    public function announcements()
    {
        return $this->belongsToMany('Announcement', 'announcement_cateogry');
    }

}