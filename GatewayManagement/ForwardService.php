<?php

namespace App\Forward;

use \App\Group\Group;
use \App\Ip\IpAddress;
use App\Forward\Port\Port;
use App\Forward\Type\Type;
use App\Database\Models\Model;
use App\Forward\Port\PortRepository;
use App\Forward\Port\Events\PortCreated;
use App\Forward\Gateway\Group\GroupRepository;
use App\Forward\Gateway\Exceptions\OutOfGateways;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;

class ForwardService
{
    protected $group;
    protected $port;
    protected $event;

    public function __construct(GroupRepository $group, EventDispatcher $event, PortRepository $port)
    {
        $this->group = $group;
        $this->port  = $port;
        $this->event = $event;
    }

    /**
     * @param $typeSlug
     * @param Model $owner App\Server\Server or App\Server\Ipmi\Ipmi
     * @param Group $group
     * @param IpAddress $destIp
     * @param $destPort
     * @return Port
     * @throws OutOfGateways
     */
    public function add($typeSlug, Model $owner, Group $group, IpAddress $destIp, $destPort)
    {
        $gatewayGroup = $this->group->group($group)
            ->whereHas('gateways', function ($query) {
                $query->whereHas('ports', function ($query) {
                    $query->havingRaw('COUNT(*) < port_limit');
                });
            })
            ->first();

        if (!$gatewayGroup or !$gatewayGroup->gateways->count()) {
            throw new OutOfGateways($group);
        }

        $port    = new Port();
        $gateway = $gatewayGroup->gateways->first();
        $type    = Type::where('slug', $typeSlug)->firstOrFail();

        $port->type_id   = $type->id;
        $port->src_port  = $this->incrementPort();
        $port->dest_ip   = $destIp;
        $port->dest_port = $destPort;

        $port->gateway()->associate($gateway);
        $port->owner()->save($owner);
        $port->save();

        $this->event->fire(new PortCreated($port));
    }

    private function incrementPort()
    {
        return $this->port->orderBy('src_port', 'desc')->first()->src_port + 1;
    }
}