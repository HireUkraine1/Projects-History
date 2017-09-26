<?php

namespace App\Forward\Port\Events;

use App\Log\Log;

class PortDeleted extends PortEvent
{
    public function log(Log $log)
    {
        $log->setDesc('Port deleted')->setTarget($this->target);
    }
}