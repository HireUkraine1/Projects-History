<?php

namespace App\Forward\Type;

use App\Api;

class TypeController extends Api\Controller
{
    use Api\Traits\ListResource;
    use Api\Traits\ShowResource;

    /**
     * @var TypeRepository
     */
    protected $items;
    /**
     * @var TypeFilterService
     */
    protected $filter;

    /**
     * @var TypeTransformer
     */
    protected $transform;

    /**
     * @param TypeRepository $items
     * @param TypeFilterService $filter
     * @param TypeTransformer $transform
     */
    public function boot(
        TypeRepository $items,
        TypeFilterService $filter,
        TypeTransformer $transform
    ) {
        $this->items     = $items;
        $this->filter    = $filter;
        $this->transform = $transform;
    }

    /**
     * Filter the Repository by viewable entries.
     */
    public function filter()
    {
        $this->items->filter([$this->filter, 'viewable']);
    }
}