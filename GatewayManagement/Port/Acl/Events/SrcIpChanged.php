<?php

namespace App\Forward\Port\Acl\Events;

use App\Log\Log;

class SrcIpChanged extends AclEvent
{
    public function log(Log $log)
    {
        $log->setDesc('ACL src ip changed')->setTarget($this->target);
    }
}