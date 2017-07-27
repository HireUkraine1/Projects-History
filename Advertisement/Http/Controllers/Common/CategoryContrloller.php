<?php

namespace App\Http\Controllers\Common;

use App\Category;
use App\City;
use App\SubCategory;
use App\Worker;
use DB;
use Illuminate\Http\Request;

class CategoryContrloller extends CommonSearchContrloller
{
    /**
     * Open user category
     *
     * @param Request $request
     * @param $name
     * @return mixed
     */
    public function categoryUsers(Request $request, $name)
    {
        $ads = \App\Baner::where('city_id', $this->cityId)->where('show', 1)
            ->get()
            ->toArray();
        if (count($ads) < 1) {
            $ads = \App\Baner::where('city_id', 1)->where('show', 1)
                ->get()
                ->toArray();
        }
        $c = City::where('id', $this->cityId)->first();
        $perPage = ($request->input('perPage')) ? $request->input('perPage') : 10;
        $get = $this->paginationLink($request->all());
        $category = Category::where('slug', $name)
            ->where('show', 1)
            ->first();
        if ($category instanceof Category) {
            $title = "Ханди | $category->name";
            $subCategoriesId = SubCategory::where('category_id', $category->id)->get(['id'])
                ->reject(function ($subCategory) {
                    return $subCategory->show === 1;
                })
                ->map(function ($subCategory) {
                    return $subCategory->id;
                });
            $workers = Worker::where('show', 1)->whereHas('sub_categories', function ($subCategory) use ($subCategoriesId) {
                $subCategory->whereIn('sub_categories_id', $subCategoriesId);
            })
                ->orderBy('position')
                ->paginate($perPage);

            return view('common.single-category')->with('workers', $workers)
                ->with('get', $get)
                ->with('perPage', $perPage)
                ->with('ads', $ads)
                ->with('c', $c)
                ->with('title', $title)
                ->with('data', $this->data);
        } else {
            abort(404);
        }


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
}
