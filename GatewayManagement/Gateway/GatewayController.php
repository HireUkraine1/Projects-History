<?php

namespace App\Forward\Gateway;

use App\Api;

class GatewayController extends Api\Controller
{
    use Api\Traits\ListResource;
    use Api\Traits\ShowResource;
    use Api\Traits\UpdateResource;
    use Api\Traits\CreateResource;
    use Api\Traits\DeleteResource;

    /**
     * @var GatewayFilterService
     */
    protected $filter;

    /**
     * @var GatewayRepository
     */
    protected $items;

    /**
     * @var GatewayTransformer
     */
    protected $transform;

    /**
     * @var GatewayUpdateService
     */
    protected $update;

    /**
     * @var GatewayDeleteService
     */
    protected $delete;

    /**
     * @param GatewayRepository $items
     * @param GatewayFilterService $filter
     * @param GatewayTransformer $transform
     * @param GatewayUpdateService $update
     * @param GatewayDeleteService $delete
     */
    public function boot(
        GatewayRepository $items,
        GatewayFilterService $filter,
        GatewayTransformer $transform,
        GatewayUpdateService $update,
        GatewayDeleteService $delete
    ) {
        $this->items     = $items;
        $this->filter    = $filter;
        $this->transform = $transform;
        $this->update    = $update;
        $this->delete    = $delete;
    }

    /**
     * Filter the Repository by viewable entries.
     */
    public function filter()
    {
        $this->items->filter([$this->filter, 'viewable']);
    }
}