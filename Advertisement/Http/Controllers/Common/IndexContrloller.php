<?php

namespace App\Http\Controllers\Common;

use App\Category;
use App\SubCategory;
use App\Tag;
use App\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class IndexContrloller extends CommonSearchContrloller
{
    private $lastAddCategory = [];
    private $listCatFirs = [];
    private $listCatSecond = [];
    private $countCategory = 0;
    private $countTag = 0;
    private $countWorker = 0;

    /**
     * Home page
     *
     * @return mixed
     */
    public function index()
    {
        $categoriesInfo = $this->getCommonCategoriesInfo();
        $this->listCatFirs = $categoriesInfo['listCatFirs'];
        $this->listCatSecond = $categoriesInfo['listCatSecond'];
        $this->countCategory = $categoriesInfo['countCategory'];
        $this->countWorker = $this->countWorker();
        $this->countTag = $this->countTag();
        $this->lastAddCategory = $this->getLastCategories();
        $data = [
            'listCatFirs' => $this->listCatFirs,
            'listCatSecond' => $this->listCatSecond,
            'countCategory' => $this->countCategory,
            'countTag' => $this->countTag,
            'countWorker' => $this->countWorker,
            'lastAddCategory' => $this->lastAddCategory,
            'allCategory' => $this->allCategory,
            'allSubCategory' => $this->allSubCategory,
            'popularCities' => $this->popularCities,
            'regions' => $this->regions,
        ];

        return view('common.index')->with('data', $data);
    }

    /**
     * Category Info
     *
     * @return array
     */
    private function getCommonCategoriesInfo()
    {
        $listCatFirs = [];
        $listCatSecond = [];
        $countCategory = 0;
        $category = Category::where('show', 1)->orderBy('name')->get();
        if ($category->count()) {
            $countCategory = $category->count();
            $firstCount = ceil($countCategory / 2);
            $i = 0;
            for ($i; $i < $firstCount; $i++) {
                $listCatFirs[$i]['name'] = $category[$i]->name;
                $listCatFirs[$i]['slug'] = $category[$i]->slug;
            }
            if (isset($category[$i])) {
                for ($i; $i < $countCategory; $i++) {
                    $listCatSecond[$i]['name'] = $category[$i]->name;
                    $listCatSecond[$i]['slug'] = $category[$i]->slug;
                }
            }
        }
        return ['countCategory' => $countCategory, 'listCatFirs' => $listCatFirs, 'listCatSecond' => $listCatSecond];
    }

    /**
     * Worker Counter
     *
     * @return int
     */
    private function countWorker()
    {
        $workers = Worker::where('show', 1)->get();
        $countWorker = 0;
        if ($workers->count()) {
            $countWorker = $workers->count();
        }
        return $countWorker;
    }

    /**
     * Tag Counter
     *
     * @return int
     */
    private function countTag()
    {
        $countTag = 0;
        $tags = Tag::whereHas('worker', function ($worker) {
            $worker->where('show', 1);
        })
            ->get(['tag'])
            ->map(function ($tag) {
                return $tag->tag;
            })
            ->toArray();
        if (count($tags) > 0) {
            $tags = array_unique($tags);
            $countTag = count($tags);
        }
        return $countTag;
    }

    /**
     * List of last category
     *
     * @return array
     */
    private function getLastCategories()
    {
        $lastAddCategory = [];
        $categoryId = [];
        $categories = [];
        $lastCategoryId = SubCategory::where('show', 1)->whereHas('workers', function ($worker) {
            $worker->where('show', 1)->orderBy('created_at')->take(14);
        })
            ->whereHas('category', function ($category) {
                $category->where('show', 1);
            })
            ->get(['category_id'])
            ->map(function ($categories) {
                return $categories->category_id;
            })
            ->toArray();
        if (count($lastCategoryId) > 0):
            $categoryId = array_unique($lastCategoryId);
        endif;
        if (count($categoryId) > 0):
            $categories = Category::where('show', 1)->whereIn('id', $categoryId)
                ->get(['name', 'slug'])
                ->toArray();
            array_splice($categories, 14);
        endif;
        return $categories;
    }
}
