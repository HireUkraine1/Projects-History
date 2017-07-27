<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);
/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

require __DIR__.'/../bootstrap/autoload.php';
require __DIR__.'/../bootstrap/database.php';
require __DIR__.'/../bootstrap/view.php';
require __DIR__.'/../bootstrap/routes.php';
