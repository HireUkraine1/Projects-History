<?php

namespace App\Forward\Gateway\Group;

use App\Log\EventLogger;
use App\Support\EventServiceProvider;

class GroupEventServiceProvider extends EventServiceProvider
{
    protected $listen = [
        Events\GroupCreated::class => [
            EventLogger::class,
        ],
        Events\GroupDeleted::class => [
            EventLogger::class,
        ],
    ];
}