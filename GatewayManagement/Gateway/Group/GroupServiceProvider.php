<?php

namespace App\Forward\Gateway\Group;

use App\Support\ClassMap;
use App\Support\ServiceProvider;

/**
 * Provide the Group feature to the Application.
 */
class GroupServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $providers = [
        GroupEventServiceProvider::class,
        GroupRoutesProvider::class
    ];

    /**
     * @param ClassMap $classMap
     */
    public function boot(ClassMap $classMap)
    {
        $classMap->map('forward.gateway.group', Group::class);
    }

}