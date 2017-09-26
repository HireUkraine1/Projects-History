<?php

namespace App\Forward\Port\Events;

use App\Log\Log;

class DestIpChanged extends PortEvent
{
    public function log(Log $log)
    {
        $log->setDesc('Port dest ip changed')->setTarget($this->target);
    }
}