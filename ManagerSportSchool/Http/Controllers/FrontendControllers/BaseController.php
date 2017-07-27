<?php

namespace App\Http\Controllers\FrontendControllers;

use App\Http\Controllers\Controller;
use App\Models;

class BaseController extends Controller
{
    /**
     * In the constructor set the header and footer settings front pages
     *
     **/
    public function __construct()
    {
        $main_category_menu = Models\Category::whereIn('slug', ['sport', 'sport', 'sport'])->get()->toArray();
        view()->share('header_logo', \Config::get('settings.header_logo') ?? null);
        view()->share('header_menu', \Config::get('settings.header_menu') ? json_decode(\Config::get('settings.header_menu')) : []);
        view()->share('footer_logo', \Config::get('settings.footer_logo') ?? null);
        view()->share('footer_menu_column_1', \Config::get('settings.footer_menu_column_1') ? json_decode(\Config::get('settings.footer_menu_column_1')) : []);
        view()->share('footer_menu_column_2', \Config::get('settings.footer_menu_column_2') ? json_decode(\Config::get('settings.footer_menu_column_2')) : []);
        view()->share('footer_menu_column_3', \Config::get('settings.footer_menu_column_3') ? json_decode(\Config::get('settings.footer_menu_column_3')) : []);
        view()->share('footer_menu_column_4', \Config::get('settings.footer_menu_column_4') ? json_decode(\Config::get('settings.footer_menu_column_4')) : []);
        view()->share('footer_email', \Config::get('settings.footer_email') ?? null);
        view()->share('footer_fb_link', \Config::get('settings.footer_fb_link') ?? null);
        view()->share('main_category_menu', $main_category_menu);
    }
}
