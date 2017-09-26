<?php

namespace App\Forward\Port;

use App\Log\EventLogger;
use App\Support\EventServiceProvider;
use App\Forward\Port\Listeners\AclDisabled;

class PortEventServiceProvider extends EventServiceProvider
{
    protected $listen = [
        Events\PortCreated::class => [
            EventLogger::class,
        ],
        Events\PortDeleted::class => [
            EventLogger::class,
        ],
        Events\DestIpChanged::class => [
            EventLogger::class,
        ],
        Events\DestPortChanged::class => [
            EventLogger::class,
        ],
        Events\GatewayChanged::class => [
            EventLogger::class,
        ],
        Events\SrcPortChanged::class => [
            EventLogger::class,
        ],
        Events\TypeChanged::class => [
            EventLogger::class,
        ],
        Events\AclChanged::class => [
            EventLogger::class,
            AclDisabled::class,
        ],
    ];
}