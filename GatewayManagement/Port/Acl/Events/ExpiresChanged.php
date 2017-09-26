<?php

namespace App\Forward\Port\Acl\Events;

use App\Log\Log;

class ExpiresChanged extends AclEvent
{
    public function log(Log $log)
    {
        $log->setDesc('ACL date expire changed')->setTarget($this->target);
    }
}