<?php

namespace App\Forward\Port\Events;

use App\Support\Event;
use App\Log\LoggableEvent;
use App\Forward\Port\Port;

abstract class PortEvent extends Event implements LoggableEvent
{
    /**
     * @var Port
     */
    public $target;

    /**
     * Create a new event instance.
     *
     * @param Port $port
     */
    public function __construct(Port $port)
    {
        $this->target = $port;
    }
}