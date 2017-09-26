<?php

namespace App\Forward\Port\Events;

use App\Log\Log;

class GatewayChanged extends PortEvent
{
    public function log(Log $log)
    {
        $log->setDesc('Port gateway changed')->setTarget($this->target);
    }
}