<?php

namespace App\Forward\Port;

use App\Api\Transformer;

class PortTransformer extends Transformer
{
    /**
     * @param Port $item
     *
     * @return array
     */
    public function item(Port $item)
    {
        return $item->expose([
                'id',
                'src_port',
                'dest_ip',
                'dest_port',
                'is_acl_enabled',
            ]) + [
                'name'    => $item->src_port . ':' . $item->dest_port,
                'type'    => $item->type->expose(['id', 'name']),
                'gateway' => $item->gateway->expose(['id', 'hostname']),
                'owner'   => $item->owner ? $item->owner->expose(['id', 'nickname']) : null,
            ];
    }

    /**
     * @param Port $item
     *
     * @return array
     */
    public function resource(Port $item)
    {
        return $this->item($item);
    }
}