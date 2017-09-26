<?php

namespace App\Forward\Gateway\Events;

use App\Support\Event;
use App\Log\LoggableEvent;
use App\Forward\Gateway\Gateway;

abstract class GatewayEvent extends Event implements LoggableEvent
{
    /**
     * @var Gateway
     */
    public $target;

    /**
     * Create a new event instance.
     *
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway)
    {
        $this->target = $gateway;
    }
}