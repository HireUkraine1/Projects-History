<?php

namespace App\Forward\Port\Acl;

use App\Http\Requests\RestRequest;

class AclFormRequest extends RestRequest
{
    protected $rules = [
        'src_ip'     => 'required|ip',
        'port_id'    => 'required|exists:forward_ports,id',
        'expires_at' => 'required|date',
    ];
}