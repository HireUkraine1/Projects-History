<?php

namespace App\Forward\Port;

use App\Support\ClassMap;
use App\Support\ServiceProvider;

/**
 * Provide the Port feature to the Application.
 */
class PortServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $providers = [
        PortEventServiceProvider::class,
        PortRoutesProvider::class
    ];

    /**
     * @param ClassMap $classMap
     */
    public function boot(ClassMap $classMap)
    {
        $classMap->map('forward.port', Port::class);
    }

}