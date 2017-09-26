<?php

namespace App\Forward\Type;

use Illuminate\Routing\Router;
use App\Http\RouteServiceProvider;

class TypeRoutesProvider extends RouteServiceProvider
{
    protected function api(Router $router)
    {
        $router->group(['as' => 'forward.'], function () use ($router) {
            $router->resource('forward/type', TypeController::class);
        });
    }
}