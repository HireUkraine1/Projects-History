<?php

namespace App\Forward\Port;

use App\Forward\Type\Type;
use App\Forward\Port\Acl\Acl;
use App\Database\Models\Model;
use App\Forward\Gateway\Gateway;

class Port extends Model implements \App\Log\Targetable
{
    use \App\Database\Models\Traits\Searchable;

    public $table = 'forward_ports';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $searchCols = ['src_port', 'dest_port'];

    public function type()
    {
        return $this->hasOne(Type::class, 'id', 'type_id');
    }

    public function gateway()
    {
        return $this->belongsTo(Gateway::class, 'gateway_id', 'id');
    }

    public function owner()
    {
        return $this->morphTo();
    }

    public function logTargets()
    {
        return array_merge([$this->gateway], parent::logTargets());
    }

    public function acl()
    {
        return $this->hasMany(Acl::class, 'port_id', 'id');
    }
}