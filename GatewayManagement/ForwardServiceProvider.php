<?php

namespace App\Forward;

use App\Support\ServiceProvider;
use App\Forward\Port\PortServiceProvider;
use App\Forward\Type\TypeServiceProvider;
use App\Forward\Port\Acl\AclServiceProvider;
use App\Forward\Gateway\GatewayServiceProvider;
use App\Forward\Gateway\Group\GroupServiceProvider;

class ForwardServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $providers = [
        PortServiceProvider::class,
        GatewayServiceProvider::class,
        GroupServiceProvider::class,
        TypeServiceProvider::class,
        AclServiceProvider::class
    ];

}