<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models;
use Illuminate\Http\Request;

class ClubController extends Controller
{
    /**
     * Club list
     *
     * @param Request $request
     * @return string
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $cat = $request->get('category');
            //get All Landings where status active  and has categoty
            $landings = Models\SchoolLanding::where('active', '=', 1)
                ->whereHas('school.businesses', function ($businesses) {
                    $businesses->where('id', '=', 2);
                })
                ->whereHas('school', function ($school) {
                    $school->where('approve', '=', 1)
                        ->where('status_id', '=', 3);
                })
                ->whereHas('locations')
                ->with('school', 'locations', 'category')
                ->getLandingByCategory($cat);

            //filter school by name
            if ($request->has('school') && !empty($request->get('school'))) {
                $landings->getLandingByName($request->school);
            }

            //filter school by country
            if ($request->has('country') && !empty($request->get('country'))) {
                $landings->getLandingByLocation($request->country);
            }

            //filter school by activity name (we don't consider activity id  just name) //need some edits
            if ($request->has('activity') && !empty($request->get('activity')) && $request->activity != 'Select Activity') {
                $landings->getLandingByActivity($request->activity);
            }

            $landingsMap = [];
            foreach ($landings->get() as $landing) {
                foreach ($landing->locations as $location) {
                    $landingsMap[] = [
                        'position' => ['lat' => (float)$location->latitude, 'lng' => (float)$location->longitude],
                        'cat' => strtolower($landing->category->alias),
                        'title' => $landing->school->name,
                        'description' => $landing->school->name,
                    ];
                }
                unset($landing);
            }

            return json_encode($landingsMap);
        }
    }
}
