<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    protected $user;

    /**
     * CommonController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = \Auth::user();
            view()->share('userName', $this->user->first_name . ' ' . $this->user->last_name);
            view()->share('userID', $this->user->id);
            view()->share('roleName', 'Member');
            if (!$this->user) {
                \Auth::logout();
                return redirect('/');
            }
            return $next($request);
        });
    }
}
