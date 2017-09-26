<?php

namespace App\Forward\Gateway;

use App\Http\Requests\ListRequest;

class GatewayListRequest extends ListRequest
{
    public $orders = [];
}