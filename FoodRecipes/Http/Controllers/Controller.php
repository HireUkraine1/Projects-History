<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Cache a page for 1 day
     *
     * @param string $string
     *
     * @return mixed
     */
    protected function cacheForOneDay($string = '')
    {
        // check if cache is enabled
        $cache = \Settings::get('settings.cache_enabled', false);
        if (!$cache) {
            return $string;
        }
        return \Response::make($string)->setTtl(60 * 60 * 24); // Cache 1 day
    }
}
