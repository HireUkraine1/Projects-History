<?php

namespace App\Forward\Port\Acl;

use Illuminate\Routing\Router;
use App\Http\RouteServiceProvider;

class AclRoutesProvider extends RouteServiceProvider
{
    protected function api(Router $router)
    {
        $router->group(['as' => 'forward.'], function () use ($router) {
            $router->resource('forward/port/{port}/acl', AclController::class);
        });
    }
}