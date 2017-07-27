<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Request;


class SuperAdminDashboard extends Controller
{
    /**
     * Admin dashboard
     *
     * @return mixed
     */
    public function index()
    {
        return view('super-admin.dashboard');
    }
}
