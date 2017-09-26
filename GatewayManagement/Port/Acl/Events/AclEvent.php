<?php

namespace App\Forward\Port\Acl\Events;

use App\Support\Event;
use App\Log\LoggableEvent;
use App\Forward\Port\Acl\Acl;

abstract class AclEvent extends Event implements LoggableEvent
{
    /**
     * @var Acl
     */
    public $target;

    /**
     * Create a new event instance.
     *
     * @param Acl $acl
     */
    public function __construct(Acl $acl)
    {
        $this->target = $acl;
    }
}