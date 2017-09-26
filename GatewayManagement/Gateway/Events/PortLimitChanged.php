<?php

namespace App\Forward\Gateway\Events;

use App\Log\Log;

class PortLimitChanged extends GatewayEvent
{
    public function log(Log $log)
    {
        $log->setDesc('Gateway port limit changed')->setTarget($this->target);
    }
}