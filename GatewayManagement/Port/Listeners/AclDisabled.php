<?php

namespace App\Forward\Port\Listeners;

use App\Forward\Port\Events\AclChanged;

class AclDisabled
{
    public function handle(AclChanged $event)
    {
        if (!$event->target->is_acl_enabled) {
            $event->target->acl()->delete();
        }
    }
}