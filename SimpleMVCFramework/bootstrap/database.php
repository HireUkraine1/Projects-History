<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'db',
    'port' => '3306',
    'database' => 'test',
    'username' => 'test',
    'password' => 'test',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();
