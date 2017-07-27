<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    protected $admin;

    /**
     * CommonController constructor.
     */
    public function __construct()
    {
        $this->middleware('admin');
        $this->middleware(function ($request, $next) {
            $this->admin = \Auth::guard('admin')->user();
            if (!$this->admin) {
                \Auth::guard('admin')->logout();
                return redirect('/');
            }

            view()->share('userName', $this->admin->name);
            view()->share('userID', $this->admin->id);
            view()->share('roleID', $this->admin->role_id);
            view()->share('roleName', $this->admin->role->name);
            return $next($request);
        });
    }

}