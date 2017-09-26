<?php

namespace App\Forward\Gateway\Group;

use Illuminate\Support\Collection;
use App\Forward\Gateway\Group\Events\GroupCreated;

class GroupUpdateService
{
    /**
     * @var GroupFormRequest
     */
    protected $request;
    protected $requestClass = GroupFormRequest::class;

    public function afterCreate(Collection $items)
    {
        $createEvent = $this->queueHandler(GroupCreated::class);

        $this->successItems('forward.gateway.group.created', $items->each($createEvent));

    }

    protected function updateAll(Collection $items)
    {
        if (!$this->create) {
            /*
             * TODO: Throw "Not Applicable" exception or skip.
             */
        }

        $items->map([$this, 'fillData']);
    }

    /**
     * @param Group $item
     */
    public function fillData(Group $item)
    {
        $item->group_id   = $this->request->input('group_id');
        $item->gateway_id = $this->request->input('gateway_id');
    }
}