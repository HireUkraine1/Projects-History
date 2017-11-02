<?php

namespace App\Http\Controllers;

use App\Jobs\DowngradePagePriority;
use App\Models;
use App\Support\Sitemap\SitemapPages;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    use DispatchesJobs;

    /**
     * Output dynamic sitemap
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $pages = Models\Pagesheet::where('active', true)
            ->orderBy('sitemappriority', 'DESC')
            ->get();

        $userAgent = $request->header('User-Agent');

        if (in_array($userAgent, config('search-engine-bot'))) {
            dispatch(new DowngradePagePriority($pages));
        }

        $sitemapPages = new SitemapPages($pages);


        return response()->sitemap($sitemapPages);
    }

}
