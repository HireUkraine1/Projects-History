<?php

namespace App\Forward\Port;

use Illuminate\Routing\Router;
use App\Http\RouteServiceProvider;

class PortRoutesProvider extends RouteServiceProvider
{
    protected function api(Router $router)
    {
        $router->group(['as' => 'forward.'], function () use ($router) {
            $router->resource('forward/gateway/{gateway}/port', PortController::class);
        });
    }
}