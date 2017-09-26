<?php

namespace App\Forward\Gateway\Group\Events;

use App\Log\Log;

class GroupCreated extends GroupEvent
{
    public function log(Log $log)
    {
        $log->setDesc('Group created')->setTarget($this->target);
    }
}