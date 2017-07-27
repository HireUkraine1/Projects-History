<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    /**
     * return the home page
     */
    public function view()
    {
        return $this->cacheForOneDay(view('frontend.pages.home'));
    }
}
