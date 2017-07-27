<?php

/**
 *
 */
class LfmAlbum extends Eloquent
{
    static $collection = "";
    /** use specific config section **/
    public static $config = 'development';
    protected $table = "lfm_albums";

    public function album()
    {
        return $this->belongsTo('Album');
    }

}