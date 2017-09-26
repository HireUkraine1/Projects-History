<?php

namespace App\Forward\Gateway;

use App\Support\ClassMap;
use App\Support\ServiceProvider;
use App\Forward\Gateway\Exceptions\ExceptionHandler;

/**
 * Provide the Gateway feature to the Application.
 */
class GatewayServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $providers = [
        GatewayEventServiceProvider::class,
        GatewayRoutesProvider::class,
        ExceptionHandler::class
    ];

    /**
     * @param ClassMap $classMap
     */
    public function boot(ClassMap $classMap)
    {
        $classMap->map('forward.gateway', Gateway::class);
    }

}