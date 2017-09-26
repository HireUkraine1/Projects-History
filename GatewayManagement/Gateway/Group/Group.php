<?php

namespace App\Forward\Gateway\Group;

use App\Database\Models\Model;
use App\Group\Group as IpGroup;
use App\Forward\Gateway\Gateway;

class Group extends Model
{
    public $table = 'gateway_group';

    public function gateways()
    {
        return $this->hasOne(Gateway::class, 'id', 'gateway_id');
    }

    public function groups()
    {
        return $this->hasOne(IpGroup::class, 'group_id', 'id');
    }

    public function scopeGroup($query, IpGroup $group)
    {
        return $query->where('group_id', $group->id);
    }
}