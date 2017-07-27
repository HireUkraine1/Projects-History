<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ConcertTrack extends Eloquent
{
    public $timestamps = false;
    protected $table = "concert_track";
}