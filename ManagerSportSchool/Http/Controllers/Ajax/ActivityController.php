<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Category activities
     * @param Request $request
     * @return mixed
     */
    public function activities(Request $request)
    {
        if ($request->ajax()) {
            return Models\Activity::where('category_id', '=', $request->catId)->get();
        }
    }


}
