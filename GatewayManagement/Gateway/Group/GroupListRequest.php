<?php

namespace App\Forward\Gateway\Group;

use App\Http\Requests\ListRequest;

class GroupListRequest extends ListRequest
{
    public $orders = [];
}