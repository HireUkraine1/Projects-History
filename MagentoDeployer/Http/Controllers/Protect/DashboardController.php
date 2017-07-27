<?php

namespace App\Http\Controllers\Protect;

use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Request;
use Sentinel;

class DashboardController extends CommonController
{
    /**
     * @return mixed
     */
    public function index()
    {
        return view('pages.design-dashboard');
    }
}
