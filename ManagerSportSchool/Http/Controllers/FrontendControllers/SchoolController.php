<?php

namespace App\Http\Controllers\FrontendControllers;

use App\Models;
use Illuminate\Http\Request;

class SchoolController extends BaseController
{
    /**
     * List of Schools
     *
     */
    public function index(Request $request)
    {
        //get All Landings where status active  and has categoty
        $landings = Models\SchoolLanding::where('active', '=', 1)
            ->whereHas('category', function ($category) {
                $category->whereIn('id', function ($query) {
                    $query->select('category_id')
                        ->from('school_categories')
                        ->whereRaw('school_id =`school_landings`.`school_id`');
                });
            })->whereHas('school', function ($school) {
                $school->where('approve', '=', 1)
                    ->where('status_id', '=', 3);
            })
            ->whereHas('locations')
            ->with('school', 'locations', 'category');

        //filter school by name
        if ($request->has('school')) {
            $landings->getLandingByName($request->school);
        }

        //filter school by country
        if ($request->has('country')) {
            $landings->getLandingByLocation($request->country);
        }
        //filter school by category
        if ($request->has('cat') && $request->cat != 'Select Category') {
            $landings->getLandingByCategory($request->cat);
        }
        //filter school by activity name (we don't consider activity id  just name) //need some edits
        if ($request->has('activity') && $request->activity != 'Select Activity') {
            $landings->getLandingByActivity($request->activity);
        }
        //school on map
        $landingsMap = []; // $allLanding->locations;
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
        $landingsMap = json_encode($landingsMap); //convert to json, use this json for GOOGLE MAP
        //Paginate found school
        $landings = $landings->paginate(12);
        //Get All activities
        $activities = Models\Activity::get();
        //Get All main category (sports)
        $sports = Models\Category::whereIn('id', [1, 2, 3])->get();
        //request paramenrs for searching form
        $old = $request->only('country', 'cat', 'activity', 'school');

        return view('templates.schools', compact('landings', 'activities', 'sports', 'old', 'landingsMap'));
    }

    /**
     * Show landing school by id school
     *
     **/
    public function show($school_id, $landing_id)
    {
        $landing = Models\SchoolLanding::whereId($landing_id)
            ->whereHas('school', function ($query) use ($school_id) {
                $query->where('school_id', '=', $school_id)->where('active', '=', 1);
            })
            ->whereHas('locations')
            ->where('active', '=', 1)
            ->with(['activities' => function ($activity) use ($landing_id) {
                $activity->whereIn('id', function ($query) use ($landing_id) {
                    $query->select('activity_id')
                        ->from('landing_activities')
                        ->whereRaw('landing_id = ' . $landing_id . ' AND category_id =(SELECT sport FROM `school_landings` WHERE id =' . $landing_id . ')');
                });
            }])
            ->with('images', 'school', 'locations', 'category')
            ->firstOrFail();
        //    dd($landing);
        $school = $landing->school;
        $title = $school->name;
        $category = $landing->category;
        $gallery = $landing->images;
        $activities = $landing->activities;
        $locations = [];
        foreach ($landing->locations as $location) {
            $locations[] = [
                'position' => ['lat' => (float)$location->latitude, 'lng' => (float)$location->longitude],
                'cat' => strtolower($landing->category->alias)
            ];
        }
        $locations = json_encode($locations);
        $website = null;
        if ($school->website) {
            $http = 'http://';
            $https = 'https://';
            $posHttp = strpos($school->website, $http);
            $posHttps = strpos($school->website, $https);
            if ($posHttp === false && $posHttps === false) {
                $website = $http . $school->website;
            }
        }
        return view('templates.school_landing', compact('title', 'school', 'landing', 'category', 'gallery', 'activities', 'locations', 'website'));
    }


}
