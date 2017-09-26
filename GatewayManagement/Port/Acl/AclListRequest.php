<?php

namespace App\Forward\Port\Acl;

use App\Http\Requests\ListRequest;

class AclListRequest extends ListRequest
{
    public $orders = [];
}