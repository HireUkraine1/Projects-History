<?php

namespace App\Http\Controllers\FrontendControllers;

use App\Http\Controllers\Controller as BaseController;
use App\Models\Pagesheet;
use Illuminate\Http\Request;

class PageController extends BaseController
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $requestUrl = str_ireplace(config('settings.current_domain'), "", $request->url());

        $url = !empty($requestUrl) ? $requestUrl : '/';

        $page = $this->getPage($url);

        return dbview($page->template->virtualroot, [
            'h1' => $page->h1,
            'title' => $page->title,
            'keywords' => $page->keywords,
            'description' => $page->description,
            'criticalcss' => $page->criticalcss,
            'url' => $page->url,
        ]);
    }

    /**
     * @param $uri
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    private function getPage($uri)
    {
        return Pagesheet::where('active', true)
            ->where('url', $uri)
            ->firstOrFail();
    }
}
