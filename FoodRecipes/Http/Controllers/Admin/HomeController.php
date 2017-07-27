<?php

namespace App\Http\Controllers\Admin;

class HomeController extends MainController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.home', compact('searchterm', 'results'));
    }
}
