<?php

namespace App\Forward\Port\Acl;

use App\Log\EventLogger;
use App\Support\EventServiceProvider;

class AclEventServiceProvider extends EventServiceProvider
{
    protected $listen = [
        Events\AclCreated::class => [
            EventLogger::class,
        ],
        Events\AclDeleted::class => [
            EventLogger::class,
        ],
        Events\ExpiresChanged::class => [
            EventLogger::class,
        ],
        Events\PortChanged::class => [
            EventLogger::class,
        ],
        Events\SrcIpChanged::class => [
            EventLogger::class,
        ],
    ];
}