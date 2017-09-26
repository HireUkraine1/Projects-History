<?php

namespace App\Forward\Gateway\Events;

use App\Log\Log;

class GatewayDeleted extends GatewayEvent
{
    public function log(Log $log)
    {
        $log->setDesc('Gateway deleted')->setTarget($this->target);
    }
}