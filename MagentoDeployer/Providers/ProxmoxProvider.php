<?php

namespace App\Providers;

use App\Http\ConfigParameters\ParametersList;
use App\Http\Controllers\Library\PVE2_API;
use Illuminate\Support\ServiceProvider;

class ProxmoxProvider extends ServiceProvider
{


    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $parameters = new ParametersList;
        $this->app->bind('App\Http\Controllers\Library\PVE2_API', function () use ($parameters) {
            return new PVE2_API($parameters->hostname, $parameters->username, $parameters->realm, $parameters->password, $parameters->port);
        });
    }
}


