<?php

namespace App\Forward\Port\Acl;

use App\Api\Transformer;

class AclTransformer extends Transformer
{
    /**
     * @param Acl $item
     *
     * @return array
     */
    public function item(Acl $item)
    {
        return $item->expose(['id', 'src_ip', 'port_id', 'expires_at', 'created_at', 'updated_at']);
    }

    /**
     * @param Acl $item
     *
     * @return array
     */
    public function resource(Acl $item)
    {
        return $this->item($item);
    }
}