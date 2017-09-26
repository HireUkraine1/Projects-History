<?php

namespace App\Forward\Port;

use App\Api;

class PortController extends Api\Controller
{
    use Api\Traits\ListResource;
    use Api\Traits\ShowResource;
    use Api\Traits\UpdateResource;
    use Api\Traits\CreateResource;
    use Api\Traits\DeleteResource;

    /**
     * @var PortRepository
     */
    protected $items;
    /**
     * @var PortFilterService
     */
    protected $filter;
    /**
     * @var PortUpdateService
     */
    protected $update;
    /**
     * @var PortDeleteService
     */
    protected $delete;
    /**
     * @var PortTransformer
     */
    protected $transform;

    /**
     * @param PortRepository $items
     * @param PortFilterService $filter
     * @param PortUpdateService $update
     * @param PortDeleteService $delete
     * @param PortTransformer $transform
     */
    public function boot(
        PortRepository $items,
        PortFilterService $filter,
        PortUpdateService $update,
        PortDeleteService $delete,
        PortTransformer $transform
    ) {
        $this->items     = $items;
        $this->filter    = $filter;
        $this->update    = $update;
        $this->delete    = $delete;
        $this->transform = $transform;
    }

    /**
     * Filter the Repository by viewable entries.
     *
     * @param int $gatewayId
     */
    public function filter($gatewayId = null)
    {
        $this->items->where('gateway_id', $gatewayId);
        $this->items->filter([$this->filter, 'viewable']);
    }
}