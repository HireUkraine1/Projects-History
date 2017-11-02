<?php

namespace App\Support\Redirect;

use App\Models\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RedirectService
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function get(Request $request)
    {
        $fullUrl   = $request->url();
        $redirects = Cache::get('redirect') ? : $this->cache();

        return isset($redirects[$fullUrl]) ? $redirects[$fullUrl] : false;
    }

    /**
     * Caching redirects
     */
    public function cache()
    {
        $data = [];

        Redirect::all()->each(function ($item) use (&$data) {
            $data[$item->oldurl] = [
                'oldurl'       => $item->oldurl,
                'newurl'       => $item->newurl,
                'coderedirect' => $item->coderedirect,
            ];
        });

        Cache::forever('redirect', $data);
        return $data;
    }
}