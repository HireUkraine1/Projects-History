<?php

namespace App\Forward\Gateway\Events;

use App\Log\Log;

class GatewayCreated extends GatewayEvent
{
    public function log(Log $log)
    {
        $log->setDesc('Gateway created')->setTarget($this->target);
    }
}