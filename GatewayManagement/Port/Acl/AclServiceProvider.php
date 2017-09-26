<?php

namespace App\Forward\Port\Acl;

use App\Support\ClassMap;
use App\Support\ServiceProvider;

/**
 * Provide the ACL feature to the Application.
 */
class AclServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $providers = [
        AclEventServiceProvider::class,
        AclRoutesProvider::class
    ];

    /**
     * @param ClassMap $classMap
     */
    public function boot(ClassMap $classMap)
    {
        $classMap->map('forward.acl', Acl::class);
    }

}