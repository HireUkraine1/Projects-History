<?php

/**
 *
 */
class  Track extends Eloquent
{

    static $collection = "";
    /** use specific config section **/
    public static $config = 'development';
    protected $table = "tracks";
    protected $fillable = ['album_id', 'duration', 'name', 'mbz_id', 'playcount', 'rank'];

    public function album()
    {
        return $this->hasOne('Album');
    }

    //--------------------------------------
    public function getMostPlayed($limit_cnt = 5)
    {
        return $this->orderBy('playcount', 'desc')->take($limit_cnt)->get();
    }
}

?>