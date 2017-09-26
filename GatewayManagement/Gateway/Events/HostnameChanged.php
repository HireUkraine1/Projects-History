<?php

namespace App\Forward\Gateway\Events;

use App\Log\Log;

class HostnameChanged extends GatewayEvent
{
    public function log(Log $log)
    {
        $log->setDesc('Gateway hostname changed')->setTarget($this->target);
    }
}