<?php

namespace App\Http\Controllers\Common;

use App\City;
use App\SubCategory;
use App\Tag;
use App\Worker;
use DB;
use Illuminate\Http\Request;

class SearchController extends CommonSearchContrloller
{

    protected $word = '';
    protected $subcategories = [];
    protected $cities = [];

    /**
     * Search result
     *
     * @param Request $request
     * @return mixed
     */
    public function searchResult(Request $request)
    {
        $ads = \App\Baner::where('city_id', $this->cityId)->where('show', 1)->get()->toArray();
        $perPage = ($request->input('perPage')) ? $request->input('perPage') : 10;
        $get = $this->paginationLink($request->all());
        $this->subcategories = $this->getSubCategories($request);
        $this->word = $this->searchByWord(trim($request->word));
        $this->cities = $this->getCities($request);
        $workers = $this->searchWorker($perPage);

        return view('common.search-result')->with('workers', $workers)
            ->with('get', $get)
            ->with('perPage', $perPage)
            ->with('ads', $ads)
            ->with('data', $this->data);
    }

    /**
     * Custom pagination
     *
     * @param $request
     * @return string
     */
    private function paginationLink($request)
    {
        $get = '';
        foreach ($request as $key => $values) {
            if ($key != 'page') {
                if (is_array($values)) {
                    foreach ($values as $value) {
                        $get .= '&' . $key . '[]=' . $value;
                    }
                } else {
                    $get .= '&' . $key . '=' . $values;
                }
            }
        }
        return $get;
    }

    /**
     * Search by sub category
     *
     * @param $request
     * @return array
     */
    private function getSubCategories($request)
    {
        if (isset($request->categories) && !empty($request->categories[0]) && count($request->categories) > 0) {
            $subcategories = $request->categories;
        } else {
            $subcategories = SubCategory::where('show', 1)->get(['id'])
                ->map(function ($subCategory) {
                    return $subCategory->id;
                })
                ->toArray();
            $subcategories = array_unique($subcategories);
        }
        return $subcategories;
    }

    /**
     * Search by word
     *
     * @param $word
     * @return array
     */
    private function searchByWord($word)
    {
        $searchworkerByTag = Tag:: where('tag', 'LIKE', "%$word%")->lists('worker_id')
            ->unique()
            ->toArray();
        $searchworkerByWord = Worker::where('description', 'LIKE', "%$word%")->orWhereIn('id', $searchworkerByTag)
            ->lists('id')
            ->toArray();
        $searchworkerByWord = array_unique($searchworkerByWord);
        return $searchworkerByWord;
    }

    /**
     * Search by city
     *
     * @param $request
     * @return array
     */
    private function getCities($request)
    {

        if (isset($request->city) && $request->city != null) {
            $cities[] = $request->city;
        } else {
            $cities = City::whereHas('workers', function ($worker) {
                $worker->where('show', 1);
            })
                ->get(['id'])
                ->map(function ($city) {
                    return $city->id;
                })
                ->toArray();
        }
        return $cities;
    }

    /**
     * Search by Worker
     *
     * @param $perPage
     * @return mixed
     */
    private function searchWorker($perPage)
    {

        $workers = Worker:: where('show', 1)->wherein('id', $this->word)
            ->whereHas('sub_categories', function ($subCategory) {
                $subCategory->whereIn('sub_categories_id', $this->subcategories);
            })
            ->whereHas('cities', function ($city) {
                $city->whereIn('city_id', $this->cities);
            })
            ->orderBy('position')
            ->paginate($perPage);
        return $workers;
    }

}
