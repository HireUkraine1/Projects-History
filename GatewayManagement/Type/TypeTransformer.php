<?php

namespace App\Forward\Type;

use App\Api\Transformer;

class TypeTransformer extends Transformer
{
    /**
     * @param Type $item
     *
     * @return array
     */
    public function item(Type $item)
    {
        return $item->expose(['id', 'slug', 'name']);
    }

    /**
     * @param Type $item
     *
     * @return array
     */
    public function resource(Type $item)
    {
        return $this->item($item);
    }
}