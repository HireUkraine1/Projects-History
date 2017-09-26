<?php

namespace App\Forward\Port\Events;

use App\Log\Log;

class TypeChanged extends PortEvent
{
    public function log(Log $log)
    {
        $log->setDesc('Port type changed')->setTarget($this->target);
    }
}