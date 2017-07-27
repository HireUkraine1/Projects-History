<?php
//use Zizaco\Mongolid;

class GeoDetails extends MongoLid
{
    protected $collection = "geo_details";
    protected $database = 'sale_dev';

    //THIS IS GHETTO FIX
    public function __construct()
    {
        parent::__construct();
        $this->database = \Config::get('database.connections.mongodb.default.database', 'sale_dev');
    }

    public function getDetailsBySlug($slug)
    {
        echo "TEST";
    }
}