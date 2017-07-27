<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class MainController extends Controller
{

    function __construct()
    {
        if (\Auth::check()) {
            \JavaScript::put([
                'username' => \Auth::user()->getAttribute('name'),
                'translatefile' => 'frontend',
                'language' => \LaravelLocalization::getCurrentLocale(),
                'language_store_path' => \URL::route('translations.store'),
                'csrf_token' => csrf_token(),
            ]);
        }
    }
}