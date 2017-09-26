<?php

namespace App\Forward\Gateway;

use App\Http\Requests\RestRequest;

class GatewayFormRequest extends RestRequest
{
    public function boot()
    {
        $this->rules = [
            'hostname'   => 'required',
            'port_limit' => 'required',
        ];
    }
}