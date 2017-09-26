<?php

namespace App\Forward\Gateway;

use App\Log\EventLogger;
use App\Support\EventServiceProvider;
use App\Forward\Gateway\Events\GatewayCreated;
use App\Forward\Gateway\Events\GatewayDeleted;
use App\Forward\Gateway\Events\HostnameChanged;
use App\Forward\Gateway\Events\PortLimitChanged;

class GatewayEventServiceProvider extends EventServiceProvider
{
    protected $listen = [
        GatewayCreated::class => [
            EventLogger::class,
        ],
        GatewayDeleted::class => [
            EventLogger::class,
        ],
        HostnameChanged::class => [
            EventLogger::class,
        ],
        PortLimitChanged::class => [
            EventLogger::class,
        ],
    ];
}