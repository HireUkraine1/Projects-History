<?php

namespace App\Forward\Gateway;

use App\Http\RouteServiceProvider;
use Illuminate\Routing\Router;

class GatewayRoutesProvider extends RouteServiceProvider
{
    protected function api(Router $router)
    {
        $router->group(['as' => 'forward.'], function () use ($router) {
            $router->resource('forward/gateway', GatewayController::class);
        });

    }
}