<?php

namespace App\Forward\Port\Acl;

use App\Api;

class AclController extends Api\Controller
{
    use Api\Traits\ListResource;
    use Api\Traits\ShowResource;
    use Api\Traits\UpdateResource;
    use Api\Traits\CreateResource;
    use Api\Traits\DeleteResource;

    /**
     * @var AclRepository
     */
    protected $items;
    /**
     * @var AclFilterService
     */
    protected $filter;
    /**
     * @var AclUpdateService
     */
    protected $update;
    /**
     * @var AclDeleteService
     */
    protected $delete;
    /**
     * @var AclTransformer
     */
    protected $transform;

    /**
     * @param AclRepository $items
     * @param AclFilterService $filter
     * @param AclUpdateService $update
     * @param AclDeleteService $delete
     * @param AclTransformer $transform
     */
    public function boot(
        AclRepository $items,
        AclFilterService $filter,
        AclUpdateService $update,
        AclDeleteService $delete,
        AclTransformer $transform
    ) {
        $this->items     = $items;
        $this->filter    = $filter;
        $this->update    = $update;
        $this->delete    = $delete;
        $this->transform = $transform;
    }

    /**
     * Filter the Repository by viewable entries.
     * @param null $portId
     */
    public function filter($portId = null)
    {
        $this->items->where('port_id', $portId);
        $this->items->filter([$this->filter, 'viewable']);
    }
}