<?php

namespace App\Forward\Port\Acl;

use App\Forward\Port\Port;
use App\Database\Models\Model;

class Acl extends Model implements \App\Log\Targetable
{
    use \App\Database\Models\Traits\Searchable;

    public $table = 'forward_acl';

    protected $searchCols = ['src_ip', 'expires_at'];

    public function port()
    {
        return $this->belongsTo(Port::class, 'id', 'port_id');
    }

    public function createdBy()
    {
//        TODO: relate with users
//        return $this->hasOne();
    }

}