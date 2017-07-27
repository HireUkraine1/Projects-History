<?php

namespace App\Http\Controllers\Common;

use App\Category;
use App\City;
use App\Http\Controllers\Controller;
use App\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class CommonSearchContrloller extends Controller
{
    public $allCategory = [];
    public $allSubCategory = [];
    public $popularCities = [];
    public $regions = [];
    public $data = [];
    public $requestParam;
    public $nameSubCategory = [];

    /**
     * CommonSearchContrloller constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct();
        $this->allCategory = $this->getAllCategories();
        $this->allSubCategory = $this->getAllSubCategories();
        $this->popularCities = $this->popularCities();
        $this->requestParam = $this->checkgetQuery($request);
        $this->data = [
            'allCategory' => $this->allCategory,
            'allSubCategory' => $this->allSubCategory,
            'popularCities' => $this->popularCities,
            'requestParam' => $this->requestParam,
        ];

    }

    /**
     * All category list
     * @return mixed
     */
    private function getAllCategories()
    {
        $category = Category::where('show', 1)->get()
            ->toArray();
        return $category;
    }

    /**
     * sub category
     *
     * @return array
     */
    private function getAllSubCategories()
    {
        $allSubcategory = [];
        $subCategories = SubCategory::where('show', 1)
            ->whereHas('category', function ($category) {
                $category->where('show', 1);
            })
            ->get()
            ->map(function ($subCategories) {
                return $subCategories;
            })
            ->toArray();
        foreach ($subCategories as $subCategory) {
            $allSubcategory[$subCategory['category_id']][$subCategory['id']] = $subCategory;

        }
        return $allSubcategory;
    }

    /**
     * The most popular city
     * @return mixed
     */
    private function popularCities()
    {
        $popularCities = City::where('id', '!=', 1)
            ->orderBy('order', 'ASC')
            ->get(['id', 'city', 'region'])
            ->map(function ($city) {
                return $city;
            })
            ->toArray();
        return $popularCities;
    }

    /**
     * Check request
     * @param $request
     * @return array
     */
    private function checkgetQuery($request)
    {
        if (count($request) > 0) {
            $queryWord = 0;
            if (isset($request['word']) && !empty($request['word']) && is_string($request['word'])) {
                $queryWord = $request['word'];
            }
            $queryCategories = 0;
            $nameCat = 0;
            $this->nameSubCategory = [];
            $this->idSubCategory = [];
            if (isset($request['categories']) && is_array($request['categories']) && count($request['categories']) > 0) {
                foreach ($request['categories'] as $subCategoriesId) {
                    if (!empty($subCategoriesId)) {
                        $category = Category::whereHas('subCategories', function ($category) use ($subCategoriesId) {
                            $category->where('id', $subCategoriesId);
                        })
                            ->first();
                        if ($category instanceof Category) {
                            $queryCategories = $category->id;
                            $nameCat = $category->name;
                        }
                        $nameSubCategory = SubCategory:: select('name', 'id')->where('id', $subCategoriesId)->first();
                        if (isset($nameSubCategory) && !empty($nameSubCategory)) {
                            $this->nameSubCategory[] = $nameSubCategory->name;
                            $this->idSubCategory[] = $nameSubCategory->id;
                        }
                    }
                }
            }
            $queryCity = 0;
            $queryCityId = 0;
            if (isset($request['city']) && !empty($request['city']) && is_string($request['city'])) {
                $city = City::where('id', $request['city'])->where('id', '!=', 1)->first();
                if ($city instanceof City) {
                    $queryCity = $city->city;
                    if ($city->region != null) {
                        $queryCity .= ' (' . $city->region . ')';
                    }
                    $queryCityId = $request['city'];
                } else {
                    $queryCity = 0;
                    $queryCityId = 0;
                }
            }
        }
        return [
            'queryWord' => $queryWord,
            'queryCity' => $queryCity,
            'queryCityId' => $queryCityId,
            'queryCategories' => $queryCategories,
            'nameCat' => $nameCat,
            'nameSubCategory' => $this->nameSubCategory,
            'idSubCategory' => $this->idSubCategory,
        ];
    }

    /**
     * Get city
     */
    public function getSitiesByRegionAjax()
    {
        $regionId = Input::get('region');
        $cities = City::where('region', function ($city) use ($regionId) {
            $city->select('region')->from('cities')->where('id', $regionId);
        })
            ->orderBy('city')
            ->get(['id', 'city', 'state'])
            ->map(function ($city) {
                return $city;
            })
            ->toArray();
        $str = '';
        foreach ($cities as $city) {
            $id = $city['id'];
            $city = $city['city'] . ', ' . $city['state'];
            $str .= "<option value=\"$id\">$city</option>\n";
        }
        echo $str;
    }

    /**
     * Ajax get sub category
     */
    public function getSubCatAjax()
    {
        $catId = Input::get('id');
        $subCategories = SubCategory::where('category_id', $catId)->where('show', 1)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(function ($subCategory) {
                return $subCategory;
            })
            ->toArray();
        $str = '';
        foreach ($subCategories as $subCategory) {
            $id = $subCategory['id'];
            $name = $subCategory['name'];
            $str .= "<option value=\"$id\">$name</option>\n";
        }
        echo $str;
    }
}
