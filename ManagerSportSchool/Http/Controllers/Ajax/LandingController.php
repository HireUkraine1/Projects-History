<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Get Activities
     *
     * @param $landing_id
     * @param Request $request
     * @return mixed
     */
    public function activities($landing_id, Request $request)
    {
        if ($request->ajax()) {
            return Models\Activity::whereHas('landings', function ($q) use ($landing_id) {
                $q->where('id', '=', $landing_id);
            })->get();
        }
    }

    /**
     * Get address
     *
     * @param $landing_id
     * @param Request $request
     * @return mixed
     */
    public function address($landing_id, Request $request)
    {
        if ($request->ajax()) {
            return Models\LandingLocation::where('landing_id', '=', $landing_id)->get();
        }
    }

    /**
     * Get course template
     *
     * @param Request $request
     * @return mixed
     */
    public function courseTemplates(Request $request)
    {
        if ($request->ajax()) {
            return $templates = \App\Models\ActivityCourses::where('activity_id', $request->activity_id)->whereHas('landings', function ($q) use ($request) {
                $q->where('id', $request->landing_id);
            })->get();
        }
    }


}
