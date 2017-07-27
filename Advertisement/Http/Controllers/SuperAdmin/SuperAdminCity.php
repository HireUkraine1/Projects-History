<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SuperAdminCity extends Controller
{
    /**
     * List of city
     *
     * @return mixed
     */
    public function index()
    {
        $cities = \App\City::where('id', '!=', 1)->get()->toArray();
        $commonBaner = \App\Baner::where('city_id', 1)->get()->toArray();

        $data = [
            'cities' => $cities,
            'commonBaner' => $commonBaner
        ];
        return view('super-admin.cities')->with('data', $data);
    }

    /**
     * City banners
     * @param $id
     * @return mixed
     */
    public function cityBaners($id)
    {
        $city = \App\City::where('id', 'LIKE', $id)->first();

        if ($city instanceof \App\City) {
            $commonBaner = \App\Baner::where('city_id', $id)->get()->toArray();
        } else {
            return abort('404');
        }

        $data = [
            'city' => $city,
            'commonBaner' => $commonBaner
        ];
        return view('super-admin.city-baners')->with('data', $data);
    }

    /**
     * Create city
     *
     * @param Request $request
     * @return string
     */
    public function ajaxNewCity(Request $request)
    {
        $error = 0;
        $state = ($request->state) ? $request->state : null;
        $region = ($request->region) ? $request->region : null;
        $order = ($request->order) ? $request->order : 0;
        $allRussianCities = \DB::table('all_russian_cities')->where('city', 'LIKE', "$request->name")->get();
        $cityCheck = \App\City::where('city', 'LIKE', $request->name)->where('state', 'LIKE', $state)->where('region', 'LIKE', $region)->first();
        if (!$cityCheck instanceof \App\City && !empty($request->name) && mb_strlen($request->name) >= 2 && count($allRussianCities) > 0) {
            $city = new \App\City();
            $city->city = $request->name;
            $city->state = $state;
            $city->region = $region;
            $city->order = $order;
            $city->save();

        } else {
            $error = 1;
        }
        return json_encode(['error' => $error]);
    }

    /**
     * Edit city
     *
     * @param Request $request
     * @return string
     */
    public function ajaxEditCity(Request $request)
    {
        $error = 0;
        $state = ($request->state) ? $request->state : null;
        $region = ($request->region) ? $request->region : null;
        $order = ($request->order) ? $request->order : 0;
        $cityCheck = \App\City::where('city', 'LIKE', $request->name)->whereNotIn('id', [$request->id])->where('state', '=', $state)->where('region', '=', $region)->first();
        $city = \App\City::where('id', 'LIKE', $request->id)->first();
        $allRussianCities = \DB::table('all_russian_cities')->where('city', 'LIKE', "$request->name")->get();
        if (!$cityCheck instanceof \App\City && count($allRussianCities) > 0) {
            $city->city = $request->name;
            $city->state = $state;
            $city->region = $region;
            $city->order = $order;
            $city->save();
        }
        else {
            $error = 1;
        }
        return json_encode(['error' => $error]);
    }

    /**
     * Delete city
     *
     * @param Request $request
     * @return string
     */
    public function delCity(Request $request)
    {
        $error = 0;
        $cityCheck = \App\City::where('id', 'LIKE', $request->id)->first();
        if ($cityCheck instanceof \App\City) {
            $cityCheck->delete();
        } else {
            $error = 1;
        }
        return json_encode(['error' => $error]);
    }

    /**
     * Create banner for city
     *
     * @param Request $request
     * @return string
     */
    public function ajaxNewBaner(Request $request)
    {
        $id = $request->id;
        $city = \App\City::where('id', $id)->first();
        $img = $request->file('img');
        $link = $request->link;
        $imgPath = 'baner-img/' . $id . '/' . $img->getClientOriginalName();
        $path_city = public_path('baner-img/' . $id . '/');
        $imgPathFile = public_path($imgPath);
        if ($img == null || $link == null) {
            return json_encode(['error' => 1, 'msg' => 'Заполните все поля']);
        }
        if ($city instanceof \App\City) {
            $banerExist = \App\Baner::where('city_id', $request->id)->where('img_path', $imgPath)->first();
            if (!$banerExist instanceof \App\Baner) {
                $baner = new \App\Baner();
                $baner->city_id = $id;
                $baner->link = $link;
                $baner->img_path = $imgPath;
                $baner->show = 1;
                $baner->save();
                if (!\File::exists($path_city)) {
                    \File::makeDirectory($path_city, $mode = 0775, true);
                }
                return json_encode(['error' => 0]);
            } elseif ($city instanceof \App\City && !\File::exists($imgPathFile)) {
                if (!\File::exists($path_city)) {
                    \File::makeDirectory($path_city, $mode = 0775, true);
                }
            } else {
                return json_encode(['error' => 1, 'msg' => 'Банер с такой картинкой существует']);
            }
        } else {
            return json_encode(['error' => 1, 'msg' => 'Город не найден']);
        }
    }

    /**
     * Delete banner
     *
     * @param Request $request
     * @return array
     */
    public function ajaxDelBaner(Request $request)
    {
        $baner = \App\Baner::where('id', $request->id)->first();
        if ($baner instanceof \App\Baner) {
            $path = public_path($baner->img_path);
            $baner->delete();
        } else {
            return ['error' => 1, 'msg' => 'Банера не существует'];
        }
        return ['error' => 0];
    }

    public function ajaxSwichBaner(Request $request)
    {
        $id = $request->id;
        $baner = \App\Baner::where('id', $id)->first();
        if ($baner instanceof \App\Baner) {
            $baner->show = $request->swich;
            $baner->save();
        } else {
            return ['error' => 1, 'msg' => 'Банера не существует'];
        }
        return ['error' => 0];
    }

    /**
     * @param Request $request
     * @return string
     */
    public function checkCityRegion(Request $request)
    {
        $name = $request->name;
        $regions = \DB::table('all_russian_cities')->select('region')->distinct()->where('city', 'LIKE', "$name")->get();
        return json_encode(['error' => 0, 'regions' => $regions]);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function checkRegionState(Request $request)
    {
        $city = $request->name;
        $region = $request->region;
        $state = \DB::table('all_russian_cities')->select('state')->distinct()->where('city', 'LIKE', "$city")->where('region', 'LIKE', "$region")->get();
        return json_encode(['error' => 0, 'state' => $state]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function takeInfoCity(Request $request)
    {
        $id = $request->id;
        $city = \App\City::where('id', $id)->first();
        if ($city instanceof \App\City) {
            $allStates = \DB::table('all_russian_cities')->select('state')->distinct()->where('city', 'LIKE', "$city->city")->where('region', 'LIKE', "$city->regions")->get();
            $allRegions = \DB::table('all_russian_cities')->select('region')->distinct()->where('city', 'LIKE', "$city->city")->get();
            $region = $city->region;
            $state = $city->state;
            $order = $city->order;
            return ['error' => 0, 'allStates' => $allStates, 'allRegions' => $allRegions, 'state' => $state, 'region' => $region, 'order' => $order];
        } else {
            return ['error' => 1];
        }
    }
}
