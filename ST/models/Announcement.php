<?php

/**
 *
 */
class Announcement extends Eloquent
{
    static $collection = "";
    /** use specific config section **/
    public static $config = 'development';
    protected $table = "announcements";
    protected $fillable = ['admin_id', 'excerpt', 'text', 'slug', 'title', 'is_page', 'note', 'publish_date'];

    public function categories()
    {
        return $this->belongsToMany('Category', 'announcement_cateogry');
    }

    public function author()
    {
        return $this->hasOne('AdminUser', 'id', 'admin_id');
    }

    public function performer()
    {
        return $this->hasOne('Performer', 'id', 'performer_id');
    }
}