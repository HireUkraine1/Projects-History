<?php

namespace App\Forward\Port\Events;

use App\Log\Log;

class AclChanged extends PortEvent
{
    public function log(Log $log)
    {
        $log->setDesc('Port acl changed')->setTarget($this->target);
    }
}