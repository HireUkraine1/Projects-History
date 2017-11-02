<?php

namespace App\Http\Controllers\Adminpanel;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{

    protected $admin;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->admin = \Auth::user();
            view()->share('userName', $this->admin->name);
            return $next($request);
        });
    }
}
