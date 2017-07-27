<?php

namespace App\Http\Controllers\Admin;

class FrontpageEditController extends MainController
{
    public function index()
    {
        \Debugbar::disable();

        return view('frontend.pages.home', [
            'editmode' => true
        ]);
    }
}