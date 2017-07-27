<?php


use Jenssegers\Mongodb\Model as Eloquent;

class ConcertTicketMongo extends Eloquent
{
    protected $collection = 'concert_tickets';
    protected $connection = 'mongodb3';
}