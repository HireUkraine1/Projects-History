<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models;
use Illuminate\Http\Request;

class LacationController extends Controller
{

    /**
     * Get states list by country
     * @param $country
     * @param Request $request
     * @return mixed
     */
    public function states($country, Request $request)
    {
        if ($request->ajax()) {
            return Models\State::where('country', '=', $country)->get();
        }
    }

    /**
     * Get cities
     *
     * @param Request $request
     * @return mixed
     */
    public function cities(Request $request)
    {
        if ($request->ajax()) {
            $country = $request->country;
            $state = Models\State::where('id', '=', $request->state)->first();
            return Models\City::where('country', '=', $country)->where('region', '=', $state->code)->get();
        }
    }

    /**
     * Get Location
     *
     * @param Request $request
     * @return string
     */
    public function location(Request $request)
    {
        if ($request->ajax()) {

            $landings = Models\SchoolLanding::where('active', '=', 1)
                ->whereHas('category', function ($category) {
                    $category->whereIn('id', function ($query) {
                        $query->select('category_id')
                            ->from('school_categories')
                            ->whereRaw('school_id =`school_landings`.`school_id`');
                    });
                })
                ->with('locations')
                ->getLandingByLocation($request->get("query"))
                ->get();

            //get all location from Founds landings
            $countries = []; // $allLanding->locations;
            foreach ($landings as $landing) {
                foreach ($landing->locations as $location) {
                    $countries[] = ['value' => $location->address, 'data' => $location->address];
                    $countries[] = ['value' => $location->country, 'data' => $location->country];
                    $countries[] = ['value' => $location->alias, 'data' => $location->alias];
                }
                unset($landing);
            }
            $sort = function ($a, $b) {
                return strcmp($a["value"], $b["value"]);
            };
            $result = array_values(array_unique($countries, SORT_REGULAR));
            usort($result, $sort);

            return json_encode(array('suggestions' => $result));
        }
    }
}
