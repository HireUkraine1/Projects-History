<?php

namespace App\Forward\Port\Acl\Events;

use App\Log\Log;

class AclCreated extends AclEvent
{
    public function log(Log $log)
    {
        $log->setDesc('ACL created')->setTarget($this->target);
    }
}