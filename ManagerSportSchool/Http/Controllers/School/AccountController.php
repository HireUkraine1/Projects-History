<?php

namespace App\Http\Controllers\School;

use CrudController;
use Illuminate\Http\Request;

class AccountController extends CrudController
{
    public $school;

    public function __construct()
    {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            $this->school = \Auth::guard('school')->user();
            return $next($request);
        });
    }

    public function index()
    {
        $this->data['title'] = 'Account'; // set the page title
        $this->data['school'] = $this->school;
        return view('school_account', $this->data);
    }
}
