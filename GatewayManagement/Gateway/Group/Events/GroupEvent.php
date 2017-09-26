<?php

namespace App\Forward\Gateway\Group\Events;

use App\Support\Event;
use App\Log\LoggableEvent;
use App\Forward\Gateway\Group\Group;

abstract class GroupEvent extends Event implements LoggableEvent
{
    /**
     * @var Group
     */
    public $target;

    /**
     * Create a new event instance.
     *
     * @param Group $group
     */
    public function __construct(Group $group)
    {
        $this->target = $group;
    }
}