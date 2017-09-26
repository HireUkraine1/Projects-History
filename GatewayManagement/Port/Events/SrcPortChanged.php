<?php

namespace App\Forward\Port\Events;

use App\Log\Log;

class SrcPortChanged extends PortEvent
{
    public function log(Log $log)
    {
        $log->setDesc('Src port changed')->setTarget($this->target);
    }
}