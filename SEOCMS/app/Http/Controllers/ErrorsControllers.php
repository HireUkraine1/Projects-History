<?php

namespace App\Http\Controllers;


class ErrorsControllers extends Controller
{
    /**
     * Display 404 error
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function error_404()
    {
        return view('errors.404');
    }

}