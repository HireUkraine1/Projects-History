<?php

namespace App\Forward\Gateway\Group;

use App\Api\ApiAuthService;
use Illuminate\Support\Collection;
use App\Support\Http\DeleteService;

class GroupDeleteService extends DeleteService
{
    /**
     * @var ApiAuthService
     */
    protected $auth;

    /**
     * @param ApiAuthService $auth
     */
    public function boot(ApiAuthService $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param Collection $items
     */
    protected function afterDelete(Collection $items)
    {
        $this->successItems('forward.gateway.group.deleted', $items);
    }

    /**
     * @param Group $item
     */
    protected function delete($item)
    {
        $this->checkCanDelete();
        $item->delete();
        $this->queue(new Events\GroupDeleted($item));
    }

    protected function checkCanDelete()
    {
        $this->auth->only('admin');
    }
}