<?php

namespace App\Forward\Gateway;

use App\Api\Transformer;

class GatewayTransformer extends Transformer
{
    /**
     * @param Gateway $item
     *
     * @return array
     */
    public function item(Gateway $item)
    {
        return $item->expose([
                'id',
                'hostname',
                'port_limit',
            ]) + [
                'name' => $item->hostname
            ];
    }

    /**
     * @param Gateway $item
     *
     * @return array
     */
    public function resource(Gateway $item)
    {
        return $this->item($item);
    }
}