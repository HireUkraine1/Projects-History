<?php

namespace App\Forward\Gateway;

trait GatewaySearch
{
    use \App\Database\Models\Traits\Searchable;

    /**
     * @var array
     */
    protected $searchCols = ['hostname'];
}