<?php

namespace App\Http\Controllers\FrontendControllers;

use App\Models;
use Illuminate\Http\Request;

class PageController extends BaseController
{

    /**
     * For front pages which have type "page" or "category"
     *
     **/
    public function index($page, Request $request)
    {
        $pageRequest = $request->info;

        //head variable
        $meta_title = $pageRequest['content']->meta_title ?? '';
        $meta_description = $pageRequest['content']->meta_description ?? '';
        $meta_key = $pageRequest['content']->meta_key ?? '';
        $color = (isset($pageRequest['parents'])) ? end($pageRequest['parents'])->color : ($pageRequest['content']->color ?? '#0b6cb7');
        $light_color = HexRgba($color);
        $rgba = 'rgba(' . $light_color["Hue"] . ',' . $light_color["Saturation"] . ',' . $light_color["Luminance"] . ', 0.7)';

        //bady variable
        $class = (isset($pageRequest['parents'])) ? end($pageRequest['parents'])->alias : ($pageRequest['content']->alias ?? 'common');
        $home = ($pageRequest['content']->is_homepage == 'True') ? 'home' : '';
        $slug = $pageRequest['content']->slug ?? '';
        $shadow = (
        !($pageRequest['content']->baner_image
            || $pageRequest['content']->slogan == 'Enable'
            || $pageRequest['content']->baner_text
            || $pageRequest['content']->search_form != '-')
        ) ? 'shadow' : '';

        //top baner variable
        $is_banner = false;
        $top_banner = false;
        if ($pageRequest['content']->baner_image
            || $pageRequest['content']->slogan == 'Enable'
            || $pageRequest['content']->baner_text
            || $pageRequest['content']->search_form != '-'
            || $pageRequest['type'] == 'category'
        ) {
            $top_banner = true;
            $is_banner = ($pageRequest['content']->baner_image) ? true : false;
            $baner_image = $pageRequest['content']->baner_image ?? '';
            $slogan = $pageRequest['content']->slogan ?? '';
            $baner_text = $pageRequest['content']->baner_text ?? '';
            $search_form = (string)$pageRequest['content']->search_form ?? '';
            $page_type = $pageRequest['type'];
            $category_name = isset($pageRequest['parents']) ? end($pageRequest['parents'])->name : ($pageRequest['content']->name) ?? '';
            $alias = $pageRequest['content']->alias ?? '';
        }


        //menu variable
        $menu = (
            $pageRequest['type'] == 'category'
            && (
                ($pageRequest['content']->slug == 'sport'
                    || $pageRequest['content']->slug == 'sport'
                    || $pageRequest['content']->slug == 'sport')
                || (isset($pageRequest['parents'])
                    && (end($pageRequest['parents'])->slug == 'sport'
                        || end($pageRequest['parents'])->slug == 'sport'
                        || end($pageRequest['parents'])->slug == 'sport')
                )
            )
        ) ? true : false;
        $childrens = (isset($pageRequest['parents'])) ? end($pageRequest['parents'])->children : (isset($pageRequest['content']->children) ? $pageRequest['content']->children : []);
        $mainCategory = (isset($pageRequest['parents'])) ? end($pageRequest['parents']) : $pageRequest['content'];

        //Search club club == school
        $search_club = ($pageRequest['content']->search_form == 'Search Club') ? true : false;
        if ($search_club) {
            $landings = Models\SchoolLanding::where('active', '=', 1)
                ->whereHas('school', function ($school) {
                    $school->where('approve', '=', 1)
                        ->where('status_id', '=', 3);
                })
                ->whereHas('school.businesses', function ($businesses) {
                    $businesses->where('id', '=', 2);
                })
                ->whereHas('locations')
                ->with('school', 'locations', 'category')
                ->getLandingByCategory($mainCategory->name);

            //filter school by name
            if ($request->has('school')) {
                $landings->getLandingByName($request->school);
            }

            //filter school by country
            if ($request->has('country')) {
                $landings->getLandingByLocation($request->country);
            }

            //filter school by activity name (we don't consider activity id  just name) //need some edits
            if ($request->has('activity') && $request->activity != 'Select Activity') {
                $landings->getLandingByActivity($request->activity);
            }
            $landings = $landings->paginate(12);
            $old = $request->only('country', 'activity', 'school');
        }


        //breadcrumbs
        $has_breadcrumbs = (
                !isset($pageRequest['content']->is_homepage)
                || $pageRequest['content']->is_homepage == 'False'
            ) ?? false;
        $breadcrumbs = '';
        $startSlug = '/';
        $breadcrumbsArray = array_reverse($pageRequest['parents'] ?? []);
        foreach ($breadcrumbsArray as $parent) {
            $breadcrumbs = '<a href="' . $startSlug . $parent->slug . '">' . $parent->alias . '<i class="fa fa-angle-right"></i></a></li>';
            $startSlug .= $parent->slug . '/';
        }
        $name = $pageRequest['content']->name;

        //main content variable
        $content = $pageRequest['content']->content;
        $row = (
            $pageRequest['type'] == 'category'
            && (
                (isset($pageRequest['content']->pages) && $pageRequest['content']->pages->count())
                || (isset($pageRequest['content']->children) && $pageRequest['content']->children->count())
            )
        ) ? true : false;
        $has_advertisements = $pageRequest['content']->advertisements->count() ? true : false;

        //sports
        $sports = \App\Models\Category::whereIn('id', [1, 2, 3])->get();
        $data = compact(
            'pageRequest',
            'sports',
            'meta_title',
            'meta_description',
            'meta_key',
            'color',
            'light_color',
            'class',
            'home',
            'slug',
            'shadow',
            'is_banner',
            'top_banner',
            'baner_image',
            'slogan',
            'baner_text',
            'search_form',
            'page_type',
            'category_name',
            'alias',
            'rgba',
            'search_club',
            'landings',
            'old',
            'menu',
            'childrens',
            'mainCategory',
            'has_breadcrumbs',
            'breadcrumbs',
            'name',
            'row',
            'has_advertisements',
            'content'
        );
        if ($home) {
            return view('templates.home_template', $data)->withShortcodes();
        }
        return view('templates.page_template', $data)->withShortcodes();
    }
}
