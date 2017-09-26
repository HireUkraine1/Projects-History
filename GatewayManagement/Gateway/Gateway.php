<?php

namespace App\Forward\Gateway;

use App\Forward\Port\Port;
use App\Database\Models\Model;

class Gateway extends Model
{
    use GatewaySearch;

    public $table = 'forward_gateways';

    public function ports()
    {
        return $this->hasMany(Port::class, 'gateway_id', 'id');
    }
}