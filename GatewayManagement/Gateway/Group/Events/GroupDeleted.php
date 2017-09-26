<?php

namespace App\Forward\Gateway\Group\Events;

use App\Log\Log;

class GroupDeleted extends GroupEvent
{
    public function log(Log $log)
    {
        $log->setDesc('Group deleted')->setTarget($this->target);
    }
}