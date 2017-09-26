<?php

namespace App\Forward\Port;

use App\Http\Requests\ListRequest;

class PortListRequest extends ListRequest
{
    public $orders = [];
}