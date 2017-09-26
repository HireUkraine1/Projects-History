<?php

namespace App\Forward\Port;

use App\Http\Requests\RestRequest;
use Illuminate\Validation\Rule;

class PortFormRequest extends RestRequest
{
    public function rules()
    {
        $rule = Rule::unique('forward_ports', 'src_port')
            ->ignore($this->request->get('id'))
            ->where('gateway_id', $this->request->get('gateway_id'));

        return [
            'type.id'    => 'required|exists:forward_types,id',
            'gateway_id' => 'required|exists:forward_gateways,id',
            'src_port'   => $rule . '|required|integer|max:65535',
            'dest_ip'    => '',
            'dest_port'  => 'integer|max:65535'
        ];
    }
}