<?php

namespace App\Http\Controllers;

use App\Models;
use Illuminate\Routing\Controller as RouteController;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Routing\Route;

class DynamicRouteController extends RouteController
{
    //variable need only to redirect to the home page
    public $redirect = false;

    /**
     *  Handles URL categories and pages
     **/
    public function handler($link = null)
    {
        if ($link == null && $homePage = Models\Page::withoutGlobalScopes()->where('is_homepage', '=', 'True')->first()) {
            $link = $homePage->slug;
            $this->redirect = true;
        }
        //check home page for error 428
        if ($link == null && !$homePage = Models\Page::withoutGlobalScopes()->where('is_homepage', '=', 'True')->first()) {
            abort(428);
        }
        $explodeLink = explode('/', $link);
        $mainSlug = end($explodeLink);
        //get page or category without parent: http://site.com/page_or_category_slug
        if (count($explodeLink) == 1) {
            $pageOrCategory = $this->getSinglePageOrCategory($mainSlug);
            $this->redirect = $this->redirect ? false : true;
        }
        //if page is homepage redirect to page without slug
        if (isset($pageOrCategory['content']->is_homepage) && $pageOrCategory['content']->is_homepage == 'True' && $this->redirect) {
            return redirect('/');
        }
        //get page or category with parent: http://site.com/category/page_or_category_slug
        if (count($explodeLink) > 1) {
            $pageOrCategory = $this->getPageOrCategoryWithParent($explodeLink);
        }
        if (!$pageOrCategory) {
            abort(404);
        }
        $controller = 'App\Http\Controllers\FrontendControllers\PageController';
        $action = 'index';
        $container = app();
        $route = $container->make(Route::class);
        $route->parameters['info'] = $pageOrCategory;
        $controllerInstance = $container->make($controller);
        return (new ControllerDispatcher($container))->dispatch($route, $controllerInstance, $action);
    }

    /**
     * Get page or category without parent
     */
    private function getSinglePageOrCategory($mainSlug)
    {
        if ($page = Models\Page::withoutGlobalScopes()->slug($mainSlug)->noCategory()->with('advertisements')->first()) {
            return ['content' => $page, 'type' => 'page'];
        }
        if ($category = Models\Category::slug($mainSlug)->noParent()->with('children')->with('advertisements')->with('pages')->first()) {
            return ['content' => $category, 'type' => 'category'];
        }
        return null;
    }

    /**
     * Get page or category with parent: http://site.com/category/page_or_category_slug
     **/
    private function getPageOrCategoryWithParent($explodeLink)
    {
        $mainSlug = array_pop($explodeLink);
        $parent = end($explodeLink);
        $category = null;
        //search page by slug
        $page = Models\Page::slug($mainSlug)->where('category_id', function ($query) use ($parent) {
            $query->select('id')
                ->from(with(new Models\Category)->getTable())
                ->where('slug', '=', $parent);
        })->with('advertisements')->first();
        if ($page && isset($page->category_id) && $categoryRealtion = $this->checkCategoryRelation($page->category_id, $explodeLink)) {
            return ['content' => $page, 'type' => 'page', 'parents' => $categoryRealtion];
        }
        //if dont found page searching category by slug
        $category = Models\Category::slug($mainSlug)->where('parent_id', function ($query) use ($parent) {
            $query->select('id')
                ->from(with(new Models\Category)->getTable())
                ->where('slug', '=', $parent);
        })->with('parent')->with('children')->with('pages')->with('advertisements')->first();

        //doesn't fount page or category we return bull
        if ($category && isset($category->parent_id) && $categoryRealtion = $this->checkCategoryRelation($category->parent_id, $explodeLink)) {
            return ['content' => $category, 'type' => 'category', 'parents' => $categoryRealtion];
        }
        return false;
    }

    /**
     * Check recursive category parent
     * last category doesn't has any parent
     * if last category has parent return null
     **/
    private function checkCategoryRelation($category_id, $explodeLink)
    {
        $arrayCategory = [];
        $categoryRelation = Models\Category::where('id', '=', $category_id)->with('parent')->with('children')->first();
        foreach (array_reverse($explodeLink) as $slugCategory) {
            $slug = $categoryRelation->slug;
            $arrayCategory[$slug] = $categoryRelation;
            if (isset($categoryRelation->parent) && count($categoryRelation->parent)) {
                $categoryRelation = $categoryRelation->parent;
            }
            $categoryRelation->getChildrenBySlug($slugCategory);
            if ($slug != $slugCategory) {
                return false;
            }
        }
        if ($categoryRelation->parent) {
            return false;
        }
        return $arrayCategory;
    }
}
