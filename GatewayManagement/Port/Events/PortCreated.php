<?php

namespace App\Forward\Port\Events;

use App\Log\Log;

class PortCreated extends PortEvent
{
    public function log(Log $log)
    {
        $log->setDesc('Port created')->setTarget($this->target);
    }
}