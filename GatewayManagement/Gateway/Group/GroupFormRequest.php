<?php

namespace App\Forward\Gateway\Group;

use App\Http\Requests\RestRequest;

class GroupFormRequest extends RestRequest
{
    public function boot()
    {
        $this->rules = [
            'gateway_id' => 'required',
            'group_id'   => 'required',
        ];
    }
}