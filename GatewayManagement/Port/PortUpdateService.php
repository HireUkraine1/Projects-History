<?php

namespace App\Forward\Port;

use Illuminate\Support\Collection;
use App\Support\Http\UpdateService;

class PortUpdateService extends UpdateService
{
    /**
     * @var PortFormRequest
     */
    protected $request;
    protected $requestClass = PortFormRequest::class;

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
        $createEvent = $this->queueHandler(Events\PortCreated::class);

        $this->successItems('forward.port.created', $items->each($createEvent));
    }

    protected function updateAll(Collection $items)
    {
        $this->setSrcPort($items);
        $this->setDestIp($items);
        $this->setDestPort($items);
        $this->setType($items);
        $this->setGateway($items);
        $this->setIsAclEnabled($items);
    }

    private function setSrcPort(Collection $items)
    {
        $inputs = ['src_port' => $this->input('src_port')];

        $event = $this->queueHandler(Events\SrcPortChanged::class);

        $this->successItems('forward.port.update.src_port',
            $items
                ->filter($this->changed($inputs))
                ->reject([$this, 'isCreating'])
                ->each($event)
        );
    }

    private function setDestIp(Collection $items)
    {
        $inputs = ['dest_ip' => $this->input('dest_ip')];

        $event = $this->queueHandler(Events\DestIpChanged::class);

        $this->successItems('forward.port.update.dest_ip',
            $items
                ->filter($this->changed($inputs))
                ->reject([$this, 'isCreating'])
                ->each($event)
        );
    }

    private function setDestPort(Collection $items)
    {
        $inputs = ['dest_port' => $this->input('dest_port')];

        $event = $this->queueHandler(Events\DestPortChanged::class);

        $this->successItems('forward.port.update.dest_port',
            $items
                ->filter($this->changed($inputs))
                ->reject([$this, 'isCreating'])
                ->each($event)
        );
    }

    private function setType(Collection $items)
    {
        $inputs = ['type_id' => $this->input('type.id')];

        $event = $this->queueHandler(Events\TypeChanged::class);

        $this->successItems('forward.port.update.type',
            $items
                ->filter($this->changed($inputs))
                ->reject([$this, 'isCreating'])
                ->each($event)
        );
    }

    private function setGateway(Collection $items)
    {
        $inputs = ['gateway_id' => $this->input('gateway_id')];

        $event = $this->queueHandler(Events\GatewayChanged::class);

        $this->successItems('forward.port.update.gateway',
            $items
                ->filter($this->changed($inputs))
                ->reject([$this, 'isCreating'])
                ->each($event)
        );
    }

    private function setIsAclEnabled(Collection $items)
    {
        $inputs = ['*is_acl_enabled' => $this->input('is_acl_enabled')];

        $event = $this->queueHandler(Events\AclChanged::class);

        $this->successItems('forward.port.update.acl',
            $items
                ->filter($this->changed($inputs))
                ->reject([$this, 'isCreating'])
                ->each($event)
        );
    }

}