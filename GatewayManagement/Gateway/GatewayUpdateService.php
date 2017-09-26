<?php

namespace App\Forward\Gateway;

use Illuminate\Support\Collection;
use App\Support\Http\UpdateService;
use App\Forward\Gateway\Events\GatewayCreated;
use App\Forward\Gateway\Events\HostnameChanged;
use App\Forward\Gateway\Events\PortLimitChanged;

class GatewayUpdateService extends UpdateService
{
    /**
     * @var GatewayFormRequest
     */
    protected $request;
    protected $requestClass = GatewayFormRequest::class;

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
        $createEvent = $this->queueHandler(GatewayCreated::class);

        $this->successItems('forward.gateway.created', $items->each($createEvent));
    }

    protected function updateAll(Collection $items)
    {
        $this->setHostname($items);
        $this->setPortLimit($items);
    }

    private function setHostname(Collection $items)
    {
        $inputs = ['hostname' => $this->input('hostname')];

        $createEvent = $this->queueHandler(HostnameChanged::class);

        $this->successItems('forward.gateway.update.hostname',
            $items
                ->filter($this->changed($inputs))
                ->reject([$this, 'isCreating'])
                ->each($createEvent)
        );
    }

    private function setPortLimit(Collection $items)
    {
        $inputs = ['port_limit' => $this->input('port_limit')];

        $createEvent = $this->queueHandler(PortLimitChanged::class);

        $this->successItems('forward.gateway.update.port_limit',
            $items
                ->filter($this->changed($inputs))
                ->reject([$this, 'isCreating'])
                ->each($createEvent)
        );
    }
}