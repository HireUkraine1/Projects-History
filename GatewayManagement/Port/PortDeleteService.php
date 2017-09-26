<?php

namespace App\Forward\Port;

use App\Api\ApiAuthService;
use Illuminate\Support\Collection;
use App\Support\Http\DeleteService;

class PortDeleteService extends DeleteService
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
        $this->successItems('forward.port.deleted', $items);
    }

    /**
     * @param Port $item
     */
    protected function delete($item)
    {
        $this->checkCanDelete();
        $item->delete();
        $this->queue(new Events\PortDeleted($item));
    }

    protected function checkCanDelete()
    {
        $this->auth->only('admin');
    }
}