<?php

namespace App\Http\Controllers;


class CommonController extends Controller
{

    public function __construct()
    {
        $user = \Sentinel::check();
        \View::share('user', $user);
    }

}

