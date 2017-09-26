<?php

namespace App\Forward\Gateway\Group;

use App\Api\Transformer;

class GroupTransformer extends Transformer
{
    /**
     * @param Group $item
     *
     * @return array
     */
    public function item(Group $item)
    {
        return $item->expose(['group_id', 'gateway_id']);
    }

    /**
     * @param Group $item
     *
     * @return array
     */
    public function resource(Group $item)
    {
        return $this->item($item);
    }
}