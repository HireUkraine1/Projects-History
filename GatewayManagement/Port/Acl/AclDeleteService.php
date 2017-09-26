<?php

namespace App\Forward\Port\Acl;

use App\Api\ApiAuthService;
use Illuminate\Support\Collection;
use App\Support\Http\DeleteService;

class AclDeleteService extends DeleteService
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
        $this->successItems('forward.port.acl.deleted', $items);
    }

    /**
     * @param Acl $item
     */
    protected function delete($item)
    {
        $this->checkCanDelete();
        $item->delete();
        $this->queue(new Events\AclDeleted($item));
    }

    protected function checkCanDelete()
    {
        $this->auth->only('admin');
    }
}