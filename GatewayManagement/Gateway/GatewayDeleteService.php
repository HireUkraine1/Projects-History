<?php

namespace App\Forward\Gateway;

use Illuminate\Support\Collection;
use App\Support\Http\DeleteService;
use App\Api\ApiAuthService;

class GatewayDeleteService extends DeleteService
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
        $this->successItems('forward.gateway.deleted', $items);
    }

    /**
     * @param Gateway $item
     */
    protected function delete($item)
    {
        $this->checkCanDelete();
        $item->delete();
        $this->queue(new Events\GatewayDeleted($item));
    }

    protected function checkCanDelete()
    {
        $this->auth->only('admin');
    }
}