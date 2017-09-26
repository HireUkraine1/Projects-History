<?php

namespace App\Forward\Gateway\Group;

use App\Api;

class GroupController extends Api\Controller
{
    use Api\Traits\ListResource;
    use Api\Traits\ShowResource;
    use Api\Traits\UpdateResource;
    use Api\Traits\CreateResource;
    use Api\Traits\DeleteResource;

    /**
     * @var GroupFilterService
     */
    protected $filter;

    /**
     * @var GroupRepository
     */
    protected $items;

    /**
     * @var GroupTransformer
     */
    protected $transform;

    /**
     * @var GroupUpdateService
     */
    protected $update;

    /**
     * @var GroupDeleteService
     */
    protected $delete;

    /**
     * @param GroupRepository $items
     * @param GroupFilterService $filter
     * @param GroupTransformer $transform
     * @param GroupUpdateService $update
     * @param GroupDeleteService $delete
     */
    public function boot(
        GroupRepository $items,
        GroupFilterService $filter,
        GroupTransformer $transform,
        GroupUpdateService $update,
        GroupDeleteService $delete
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