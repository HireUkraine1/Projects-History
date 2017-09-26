<?php

namespace App\Forward\Port\Acl;

use Illuminate\Support\Collection;
use App\Support\Http\UpdateService;

class AclUpdateService extends UpdateService
{
    /**
     * @var AclFormRequest
     */
    protected $request;
    protected $requestClass = AclFormRequest::class;

    protected function beforeAll(Collection $items)
    {
        $checkPerms = function () {
            if (!$this->permission->has('network.forward.write')) {
                abort(403, 'You do not have access to edit Port Forwarding.');
            }
        };
        $this->auth->only([
            'admin'       => $checkPerms,
            'integration' => $checkPerms,
        ]);
    }

    public function afterCreate(Collection $items)
    {
        $createEvent = $this->queueHandler(Events\AclCreated::class);

        $this->successItems('forward.port.acl.created', $items->each($createEvent));
    }

    protected function updateAll(Collection $items)
    {
        $this->setSrcIp($items);
        $this->setPort($items);
        $this->setExpires($items);
    }


    private function setSrcIp(Collection $items)
    {
        $inputs = ['src_ip' => $this->input('src_ip')];

        $event = $this->queueHandler(Events\SrcIpChanged::class);

        $this->successItems('forward.port.acl.update.src_ip',
            $items
                ->filter($this->changed($inputs))
                ->reject([$this, 'isCreating'])
                ->each($event)
        );
    }

    private function setPort(Collection $items)
    {
        $inputs = ['port_id' => $this->input('port_id')];

        $event = $this->queueHandler(Events\PortChanged::class);

        $this->successItems('forward.port.acl.update.port',
            $items
                ->filter($this->changed($inputs))
                ->reject([$this, 'isCreating'])
                ->each($event)
        );
    }

    private function setExpires(Collection $items)
    {
        $inputs = ['expires_at' => $this->input('expires_at')];

        $event = $this->queueHandler(Events\ExpiresChanged::class);

        $this->successItems('forward.port.acl.update.expires_at',
            $items
                ->filter($this->changed($inputs))
                ->reject([$this, 'isCreating'])
                ->each($event)
        );
    }

}