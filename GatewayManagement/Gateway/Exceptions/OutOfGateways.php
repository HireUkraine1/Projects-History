<?php

namespace App\Forward\Gateway\Exceptions;

use \App\Group\Group;

class OutOfGateways extends \Exception
{
    public function __construct(Group $group)
    {
        $this->group = $group;
    }
}