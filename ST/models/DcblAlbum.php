<?php

/**
 *
 */
class DcblAlbum extends Eloquent
{
    static $collection = "";
    public static $config = 'development';
    protected $table = "dcbl_albums";

    public function album()
    {
        return $this->belongs_to('Album', 'album_id');
    }
}