<?php

namespace App\Forward\Port\Acl\Events;

use App\Log\Log;

class PortChanged extends AclEvent
{
    public function log(Log $log)
    {
        $log->setDesc('ACL port changed')->setTarget($this->target);
    }
}