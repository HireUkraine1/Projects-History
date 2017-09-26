<?php

namespace App\Forward\Type;

use App\Http\Requests\ListRequest;

class TypeListRequest extends ListRequest
{
    public $orders = [];
}