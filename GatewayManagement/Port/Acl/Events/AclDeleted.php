<?php

namespace App\Forward\Port\Acl\Events;

use App\Log\Log;

class AclDeleted extends AclEvent
{
    public function log(Log $log)
    {
        $log->setDesc('ACL deleted')->setTarget($this->target);
    }
}