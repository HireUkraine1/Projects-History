<?php

/**
 *
 */
class Album extends Eloquent
{
    static $collection = "";
    /** use specific config section **/
    public static $config = 'development';
    protected $table = "albums";
    protected $fillable = ['mbz_id', 'rank', 'release_date', 'title', 'slug'];

    public function performers()
    {
        return $this->belongsToMany('Performer', 'performer_albums');

    }

    public function images()
    {
        return $this->belongsToMany('Image', 'album_images', 'album_id', 'image_id')->withPivot(['type', 'size']);
    }


    public function genres()
    {
        return $this->belongsToMany('Genre', 'album_genres');
    }

    public function dcblAlbum()
    {
        return $this->hasOne('DcblAlbum');
    }

    public function lfmAlbum()
    {
        return $this->hasOne('LfmAlbum');
    }

    public function mbzAlbum()
    {
        return $this->hasOne('MbzAlbum');
    }

    public function tags()
    {
        return $this->hasMany('AlbumTag');
    }

    public function tracks()
    {
        return $this->hasMany('Track');
    }
}