<?php

namespace App\Forward\Port\Events;

use App\Log\Log;

class DestPortChanged extends PortEvent
{
    public function log(Log $log)
    {
        $log->setDesc('Dest port changed')->setTarget($this->target);
    }
}