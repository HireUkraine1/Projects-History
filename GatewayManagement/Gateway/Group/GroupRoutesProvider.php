<?php

namespace App\Forward\Gateway\Group;

use Illuminate\Routing\Router;
use App\Http\RouteServiceProvider;

class GroupRoutesProvider extends RouteServiceProvider
{
    protected function api(Router $router)
    {
        $router->group(['as' => 'forward.gateway.'], function () use ($router) {
            $router->resource('forward/gateway/{gateway}/group', GroupController::class);
        });

    }
}