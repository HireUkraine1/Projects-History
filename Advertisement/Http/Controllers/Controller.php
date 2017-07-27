<?php

namespace App\Http\Controllers;

use GeoIp2\Database\Reader;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $cityId;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        \View::share('socialNetworksVar', \App\SocialNetworkSetting::get());
        $this->cityId = 1;
    }

}
