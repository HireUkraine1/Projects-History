<?php
//use Zizaco\Mongolid;

class AccessLog extends Moloquent
{
    protected $collection = "access_log";
    protected $database = 'sale_dev_NONE';
    protected $connection = 'mongodb2';

    //THIS IS GHETTO FIX
    public function __construct()
    {
        parent::__construct();
        $this->database = \Config::get('database.connections.mongodb.default.database', 'mongolid');
    }


}