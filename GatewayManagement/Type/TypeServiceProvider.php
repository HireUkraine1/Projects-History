<?php

namespace App\Forward\Type;

use App\Support\ClassMap;
use App\Support\ServiceProvider;

class TypeServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $providers = [
        TypeRoutesProvider::class
    ];

    /**
     * @param ClassMap $classMap
     */
    public function boot(ClassMap $classMap)
    {
        $classMap->map('forward.type', Type::class);
    }

}